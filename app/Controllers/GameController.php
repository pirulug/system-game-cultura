<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\MatchGame;
use App\Models\PlayerProfile;
use App\Services\GameEngineService;

class GameController extends Controller
{
    public function modes(Request $request, array $params = []): void
    {
        $this->render("game/modes", [
            "title" => "Modalidades de Juego - Cultura Perú",
        ]);
    }

    public function arena(Request $request, array $params = []): void
    {
        $matchId = (int)($params["id"] ?? 0);
        $userId = $this->getCurrentUserId();

        $match = MatchGame::findById($matchId);
        if (!$match) {
            Session::setFlash("error", "La partida no existe.");
            $this->redirect("/dashboard");
            return;
        }

        $players = MatchGame::getPlayers($matchId);
        $isInMatch = false;
        foreach ($players as $p) {
            if ((int)$p["user_id"] === $userId) {
                $isInMatch = true;
                break;
            }
        }

        if (!$isInMatch) {
            Session::setFlash("error", "No formas parte de esta partida.");
            $this->redirect("/dashboard");
            return;
        }

        // Si la partida ya finalizo, redirigir a resultados
        if ($match["status"] === "finished") {
            $this->redirect("/game/match/" . $matchId . "/results");
            return;
        }

        $this->render("game/arena", [
            "title" => "Arena de Batalla - Cultura Perú",
            "match" => $match,
            "players" => $players,
            "matchId" => $matchId,
            "userId" => $userId,
        ]);
    }

    public function results(Request $request, array $params = []): void
    {
        $matchId = (int)($params["id"] ?? 0);
        $userId = $this->getCurrentUserId();

        $match = MatchGame::findById($matchId);
        if (!$match) {
            Session::setFlash("error", "La partida no existe.");
            $this->redirect("/dashboard");
            return;
        }

        // Si todavia no esta finalizada, intentar finalizarla en servidor
        if ($match["status"] !== "finished") {
            GameEngineService::finalizeMatch($matchId);
            $match = MatchGame::findById($matchId);
        }

        $players = MatchGame::getPlayers($matchId);

        $this->render("game/results", [
            "title" => "Resultados de la Partida - Cultura Perú",
            "match" => $match,
            "players" => $players,
            "currentUserId" => $userId,
        ]);
    }
}

