<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\GameRoom;
use App\Models\MatchGame;
use App\Models\Notification;
use App\Models\PlayerProfile;
use App\Services\RankingService;

class DashboardController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        $profile = PlayerProfile::getByUserId($userId);
        $publicRooms = GameRoom::getPublicWaitingRooms();
        $recentMatches = MatchGame::getPlayerHistory($userId, 5);
        $topPlayers = RankingService::getGlobalRanking("1vs1", 5);
        $unreadNotifications = Notification::getUnreadByUser($userId, 5);

        // Progreso de nivel
        $currentXp = (int)$profile["experience_points"];
        $currentLvl = (int)$profile["level"];
        $xpForCurrentLevel = (int)pow($currentLvl - 1, 2) * 100;
        $xpForNextLevel = (int)pow($currentLvl, 2) * 100;
        $xpDelta = max(1, $xpForNextLevel - $xpForCurrentLevel);
        $xpProgress = min(100, max(0, round((($currentXp - $xpForCurrentLevel) / $xpDelta) * 100)));

        $this->render("dashboard/index", [
            "title" => "Panel de Control - Cultura Perú",
            "profile" => $profile,
            "publicRooms" => $publicRooms,
            "recentMatches" => $recentMatches,
            "topPlayers" => $topPlayers,
            "unreadNotifications" => $unreadNotifications,
            "xpProgress" => $xpProgress,
            "xpForNextLevel" => $xpForNextLevel,
        ]);
    }
}

