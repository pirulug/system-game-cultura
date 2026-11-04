<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Friendship;
use App\Models\Notification;

class FriendsController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        $friends = Friendship::getFriends($userId);
        $pendingRequests = Friendship::getPendingRequests($userId);

        $searchQuery = trim((string)$request->input("q", ""));
        $searchResults = [];
        if (!empty($searchQuery)) {
            $searchResults = Friendship::searchPlayers($searchQuery, $userId);
        }

        $this->render("friends/index", [
            "title" => "Amigos y Desafíos - Cultura Perú",
            "friends" => $friends,
            "pendingRequests" => $pendingRequests,
            "searchQuery" => $searchQuery,
            "searchResults" => $searchResults,
        ]);
    }

    public function sendRequest(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        $targetUserId = (int)$request->input("user_id", 0);

        $result = Friendship::sendRequest($userId, $targetUserId);

        if ($result["success"]) {
            // Notificar al receptor
            $myUser = $this->getCurrentUser();
            Notification::create(
                $targetUserId,
                "friend_request",
                "Nueva Solicitud de Amistad",
                "El jugador @" . $myUser["username"] . " te ha enviado una solicitud de amistad.",
                ["sender_id" => $userId]
            );
        }

        $this->json($result);
    }

    public function respond(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        $requestId = (int)$request->input("request_id", 0);
        $action = (string)$request->input("action", "accept");

        $accept = ($action === "accept");
        $success = Friendship::respondRequest($requestId, $userId, $accept);

        $this->json([
            "success" => $success,
            "message" => $accept ? "Solicitud de amistad aceptada." : "Solicitud rechazada.",
        ]);
    }

    public function remove(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        $friendId = (int)$request->input("friend_id", 0);

        $success = Friendship::removeFriend($userId, $friendId);
        $this->json(["success" => $success]);
    }
}

