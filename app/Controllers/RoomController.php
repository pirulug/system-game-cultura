<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Category;
use App\Models\GameRoom;
use App\Services\GameEngineService;

class RoomController extends Controller
{
    public function create(Request $request, array $params = []): void
    {
        $categories = Category::getAll(true);

        $this->render("game/room_create", [
            "title" => "Crear Sala Personalizada - Cultura Perú",
            "categories" => $categories,
        ]);
    }

    public function store(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token de seguridad inválido.");
            $this->redirect("/game/room/create");
            return;
        }

        $userId = $this->getCurrentUserId();
        $name = trim((string)$request->input("name", "Sala de " . $this->getCurrentUser()["username"]));
        $mode = (string)$request->input("mode", "1vs1");
        $isPrivate = (bool)$request->input("is_private", false);
        $password = (string)$request->input("password", "");
        $questionsCount = max(5, min(25, (int)$request->input("questions_count", 10)));
        $secondsPerQuestion = max(10, min(30, (int)$request->input("seconds_per_question", 15)));
        $difficulty = (string)$request->input("difficulty", "all");
        $categoryId = (int)$request->input("category_id", 0);
        $allowSpectators = (bool)$request->input("allow_spectators", false);

        $maxPlayers = ($mode === "2vs2") ? 4 : 2;

        try {
            $room = GameRoom::createRoom(
                $userId,
                $name,
                $mode,
                $isPrivate,
                !empty($password) ? $password : null,
                $maxPlayers,
                $questionsCount,
                $secondsPerQuestion,
                $difficulty,
                $categoryId > 0 ? $categoryId : null,
                $allowSpectators
            );

            $this->redirect("/game/room/" . $room["room_code"]);
        } catch (\Throwable $e) {
            error_log("Error creando sala: " . $e->getMessage());
            Session::setFlash("error", "No se pudo crear la sala.");
            $this->redirect("/game/room/create");
        }
    }

    public function lobby(Request $request, array $params = []): void
    {
        $code = strtoupper(trim((string)($params["code"] ?? "")));
        $room = GameRoom::findByCode($code);

        if (!$room) {
            Session::setFlash("error", "La sala solicitada no existe o fue cerrada.");
            $this->redirect("/dashboard");
            return;
        }

        $userId = $this->getCurrentUserId();

        // Si la sala ya esta en juego, verificar si hay partida activa asociada
        if ($room["status"] === "in_game") {
            $db = \App\Config\Database::getConnection();
            $stmt = $db->prepare("SELECT id FROM matches WHERE room_id = :rid AND status != 'finished' ORDER BY id DESC LIMIT 1");
            $stmt->execute([":rid" => $room["id"]]);
            $matchId = $stmt->fetchColumn();
            if ($matchId) {
                $this->redirect("/game/match/" . $matchId);
                return;
            }
        }

        // Si el usuario no esta en la sala, agregarlo
        $players = GameRoom::getPlayers((int)$room["id"]);
        $inRoom = false;
        foreach ($players as $p) {
            if ((int)$p["user_id"] === $userId) {
                $inRoom = true;
                break;
            }
        }

        if (!$inRoom) {
            if (count($players) >= (int)$room["max_players"]) {
                Session::setFlash("error", "La sala está llena.");
                $this->redirect("/dashboard");
                return;
            }

            // Asignar equipo balanceado para 2vs2
            $team = "A";
            if ($room["mode"] === "2vs2") {
                $countA = 0;
                $countB = 0;
                foreach ($players as $pl) {
                    if ($pl["team"] === "A") $countA++;
                    if ($pl["team"] === "B") $countB++;
                }
                $team = ($countA <= $countB) ? "A" : "B";
            }

            GameRoom::addPlayer((int)$room["id"], $userId, $team);
            $players = GameRoom::getPlayers((int)$room["id"]);
        }

        $isHost = ((int)$room["creator_id"] === $userId);

        $this->render("game/room_lobby", [
            "title" => "Sala de Espera: " . htmlspecialchars($room["name"]) . " - Cultura Perú",
            "room" => $room,
            "players" => $players,
            "isHost" => $isHost,
            "currentUserId" => $userId,
        ]);
    }

    public function joinByCode(Request $request, array $params = []): void
    {
        $code = strtoupper(trim((string)$request->input("room_code", "")));
        if (empty($code)) {
            Session::setFlash("error", "Debes ingresar un código de sala.");
            $this->redirect("/dashboard");
            return;
        }

        $room = GameRoom::findByCode($code);
        if (!$room) {
            Session::setFlash("error", "No se encontró ninguna sala con ese código.");
            $this->redirect("/dashboard");
            return;
        }

        $this->redirect("/game/room/" . $code);
    }

    public function toggleReady(Request $request, array $params = []): void
    {
        $roomId = (int)$request->input("room_id", 0);
        $userId = $this->getCurrentUserId();

        $success = GameRoom::toggleReady($roomId, $userId);
        $this->json(["success" => $success]);
    }

    public function changeTeam(Request $request, array $params = []): void
    {
        $roomId = (int)$request->input("room_id", 0);
        $team = (string)$request->input("team", "A");
        $userId = $this->getCurrentUserId();

        $success = GameRoom::changeTeam($roomId, $userId, $team);
        $this->json(["success" => $success]);
    }

    public function kick(Request $request, array $params = []): void
    {
        $roomId = (int)$request->input("room_id", 0);
        $targetUserId = (int)$request->input("user_id", 0);
        $userId = $this->getCurrentUserId();

        $room = GameRoom::findById($roomId);
        if (!$room || (int)$room["creator_id"] !== $userId) {
            $this->json(["success" => false, "message" => "No tienes permisos de anfitrión."], 403);
            return;
        }

        GameRoom::removePlayer($roomId, $targetUserId);
        $this->json(["success" => true]);
    }

    public function start(Request $request, array $params = []): void
    {
        $roomId = (int)$request->input("room_id", 0);
        $userId = $this->getCurrentUserId();

        $room = GameRoom::findById($roomId);
        if (!$room || (int)$room["creator_id"] !== $userId) {
            $this->json(["success" => false, "message" => "Solo el anfitrión puede iniciar la partida."], 403);
            return;
        }

        $result = GameEngineService::startMatchFromRoom($roomId);
        $this->json($result);
    }
}

