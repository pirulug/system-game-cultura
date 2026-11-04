<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Friendship extends Model
{
    public static function getFriends(int $userId): array
    {
        $stmt = self::db()->prepare(
            "SELECT u.id, u.username, p.full_name, p.avatar, p.level, p.connection_status, p.last_seen_at,
                    r.elo_rating, f.created_at AS friends_since
             FROM friendships f
             JOIN users u ON u.id = f.friend_id
             JOIN player_profiles p ON p.user_id = u.id
             LEFT JOIN player_ratings r ON r.user_id = u.id AND r.mode = '1vs1'
             WHERE f.user_id = :uid
             ORDER BY p.connection_status = 'online' DESC, u.username ASC"
        );
        $stmt->execute([":uid" => $userId]);
        return $stmt->fetchAll();
    }

    public static function areFriends(int $userId, int $friendId): bool
    {
        $stmt = self::db()->prepare(
            "SELECT id FROM friendships WHERE user_id = :u AND friend_id = :f LIMIT 1"
        );
        $stmt->execute([":u" => $userId, ":f" => $friendId]);
        return (bool)$stmt->fetch();
    }

    public static function sendRequest(int $senderId, int $receiverId): array
    {
        if ($senderId === $receiverId) {
            return ["success" => false, "message" => "No puedes enviarte una solicitud a ti mismo."];
        }

        if (self::areFriends($senderId, $receiverId)) {
            return ["success" => false, "message" => "Ya son amigos."];
        }

        // Verificar si ya hay solicitud pendiente
        $check = self::db()->prepare(
            "SELECT id, status FROM friend_requests
             WHERE (sender_id = :s AND receiver_id = :r) OR (sender_id = :r AND receiver_id = :s)
             LIMIT 1"
        );
        $check->execute([":s" => $senderId, ":r" => $receiverId]);
        $existing = $check->fetch();

        if ($existing) {
            if ($existing["status"] === "pending") {
                return ["success" => false, "message" => "Ya existe una solicitud pendiente entre ambos."];
            }
            // Si estaba rechazada, reabrirla
            $reopen = self::db()->prepare("UPDATE friend_requests SET sender_id = :s, receiver_id = :r, status = 'pending' WHERE id = :id");
            $reopen->execute([":s" => $senderId, ":r" => $receiverId, ":id" => $existing["id"]]);
            return ["success" => true, "message" => "Solicitud enviada con éxito."];
        }

        $ins = self::db()->prepare(
            "INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (:s, :r, 'pending')"
        );
        $ins->execute([":s" => $senderId, ":r" => $receiverId]);

        return ["success" => true, "message" => "Solicitud de amistad enviada."];
    }

    public static function getPendingRequests(int $userId): array
    {
        $stmt = self::db()->prepare(
            "SELECT fr.id, fr.sender_id, fr.created_at, u.username, p.full_name, p.avatar, p.level
             FROM friend_requests fr
             JOIN users u ON u.id = fr.sender_id
             JOIN player_profiles p ON p.user_id = u.id
             WHERE fr.receiver_id = :uid AND fr.status = 'pending'
             ORDER BY fr.created_at DESC"
        );
        $stmt->execute([":uid" => $userId]);
        return $stmt->fetchAll();
    }

    public static function respondRequest(int $requestId, int $receiverId, bool $accept): bool
    {
        $stmt = self::db()->prepare(
            "SELECT * FROM friend_requests WHERE id = :id AND receiver_id = :uid AND status = 'pending' LIMIT 1"
        );
        $stmt->execute([":id" => $requestId, ":uid" => $receiverId]);
        $req = $stmt->fetch();
        if (!$req) {
            return false;
        }

        self::beginTransaction();
        try {
            $newStatus = $accept ? "accepted" : "rejected";
            $upd = self::db()->prepare("UPDATE friend_requests SET status = :st WHERE id = :id");
            $upd->execute([":st" => $newStatus, ":id" => $requestId]);

            if ($accept) {
                // Crear vinculo bidireccional en friendships
                $insF1 = self::db()->prepare("INSERT IGNORE INTO friendships (user_id, friend_id) VALUES (:u1, :u2)");
                $insF1->execute([":u1" => $req["sender_id"], ":u2" => $req["receiver_id"]]);

                $insF2 = self::db()->prepare("INSERT IGNORE INTO friendships (user_id, friend_id) VALUES (:u1, :u2)");
                $insF2->execute([":u1" => $req["receiver_id"], ":u2" => $req["sender_id"]]);
            }

            self::commit();
            return true;
        } catch (\Throwable $e) {
            self::rollBack();
            return false;
        }
    }

    public static function removeFriend(int $userId, int $friendId): bool
    {
        self::beginTransaction();
        try {
            $del1 = self::db()->prepare("DELETE FROM friendships WHERE user_id = :u AND friend_id = :f");
            $del1->execute([":u" => $userId, ":f" => $friendId]);

            $del2 = self::db()->prepare("DELETE FROM friendships WHERE user_id = :f AND friend_id = :u");
            $del2->execute([":u" => $userId, ":f" => $friendId]);

            self::commit();
            return true;
        } catch (\Throwable $e) {
            self::rollBack();
            return false;
        }
    }

    public static function searchPlayers(string $query, int $excludeUserId): array
    {
        $stmt = self::db()->prepare(
            "SELECT u.id, u.username, p.full_name, p.avatar, p.level, p.connection_status,
                    r.elo_rating
             FROM users u
             JOIN player_profiles p ON p.user_id = u.id
             LEFT JOIN player_ratings r ON r.user_id = u.id AND r.mode = '1vs1'
             WHERE u.id != :ex AND u.status = 'active' AND (u.username LIKE :q1 OR p.full_name LIKE :q2)
             LIMIT 15"
        );
        $term = "%{$query}%";
        $stmt->execute([
            ":ex" => $excludeUserId,
            ":q1" => $term,
            ":q2" => $term,
        ]);
        return $stmt->fetchAll();
    }
}

