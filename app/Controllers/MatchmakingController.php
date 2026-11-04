<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\MatchmakingService;

class MatchmakingController extends Controller
{
    public function searchView(Request $request, array $params = []): void
    {
        $mode = (string)$request->input("mode", "1vs1");
        $this->render("game/matchmaking", [
            "title" => "Buscando Oponente - Cultura Perú",
            "mode" => $mode,
        ]);
    }

    public function join(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        $mode = (string)$request->input("mode", "1vs1");

        $result = MatchmakingService::joinQueue($userId, $mode);
        $this->json($result);
    }

    public function status(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        $result = MatchmakingService::checkQueue($userId);
        $this->json($result);
    }

    public function cancel(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        MatchmakingService::leaveQueue($userId);
        $this->json(["success" => true]);
    }
}

