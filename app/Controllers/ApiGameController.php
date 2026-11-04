<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\GameRoom;
use App\Models\MatchGame;
use App\Services\GameEngineService;

class ApiGameController extends Controller
{
    public function matchStatus(Request $request, array $params = []): void
    {
        $matchId = (int)($params["id"] ?? 0);
        $userId = $this->getCurrentUserId();

        $state = GameEngineService::getMatchState($matchId, $userId);
        $this->json($state);
    }

    public function submitAnswer(Request $request, array $params = []): void
    {
        $matchId = (int)($params["id"] ?? 0);
        $userId = $this->getCurrentUserId();

        $matchQuestionId = (int)$request->input("match_question_id", 0);
        $selectedOptionId = $request->input("option_id") !== null ? (int)$request->input("option_id") : null;

        $result = GameEngineService::submitAnswer($matchId, $userId, $matchQuestionId, $selectedOptionId);
        $this->json($result);
    }

    public function roomStatus(Request $request, array $params = []): void
    {
        $code = strtoupper(trim((string)($params["code"] ?? "")));
        $room = GameRoom::findByCode($code);

        if (!$room) {
            $this->json(["status" => "not_found"], 404);
            return;
        }

        $players = GameRoom::getPlayers((int)$room["id"]);

        // Verificar si la sala ya inicio partida
        $matchId = null;
        if ($room["status"] === "in_game") {
            $db = \App\Config\Database::getConnection();
            $stmt = $db->prepare("SELECT id FROM matches WHERE room_id = :rid AND status != 'finished' ORDER BY id DESC LIMIT 1");
            $stmt->execute([":rid" => $room["id"]]);
            $matchId = (int)($stmt->fetchColumn() ?: 0);
        }

        $this->json([
            "status" => $room["status"],
            "room_code" => $room["room_code"],
            "match_id" => $matchId,
            "players" => $players,
        ]);
    }
}

