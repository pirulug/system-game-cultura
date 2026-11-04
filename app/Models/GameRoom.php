<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class GameRoom extends Model
{
    public static function createRoom(
        int $creatorId,
        string $name,
        string $mode,
        bool $isPrivate,
        ?string $password,
        int $maxPlayers,
        int $questionsCount,
        int $secondsPerQuestion,
        string $difficulty,
        ?int $categoryId,
        bool $allowSpectators
    ): array {
        self::beginTransaction();
        try {
            $roomCode = strtoupper(bin2hex(random_bytes(3))); // Codigo unico de 6 caracteres
            $passHash = (!empty($password)) ? password_hash($password, PASSWORD_BCRYPT) : null;

            $stmt = self::db()->prepare(
                "INSERT INTO game_rooms (creator_id, room_code, name, mode, is_private, password_hash,
                                        max_players, questions_count, seconds_per_question, difficulty,
                                        allowed_category_id, allow_spectators, status)
                 VALUES (:cid, :code, :nm, :md, :priv, :ph, :mp, :qc, :spq, :diff, :cat, :spec, 'waiting')"
            );
            $stmt->execute([
                ":cid" => $creatorId,
                ":code" => $roomCode,
                ":nm" => $name,
                ":md" => $mode,
                ":priv" => $isPrivate ? 1 : 0,
                ":ph" => $passHash,
                ":mp" => $maxPlayers,
                ":qc" => $questionsCount,
                ":spq" => $secondsPerQuestion,
                ":diff" => $difficulty,
                ":cat" => ($categoryId > 0) ? $categoryId : null,
                ":spec" => $allowSpectators ? 1 : 0,
            ]);
            $roomId = (int)self::db()->lastInsertId();

            // Agregar anfitrion a la sala como Team A y Listo
            $playerStmt = self::db()->prepare(
                "INSERT INTO game_room_players (room_id, user_id, team, is_ready, is_host)
                 VALUES (:rid, :uid, 'A', 1, 1)"
            );
            $playerStmt->execute([":rid" => $roomId, ":uid" => $creatorId]);

            self::commit();
            return ["id" => $roomId, "room_code" => $roomCode];
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }

    public static function findByCode(string $code): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM game_rooms WHERE room_code = :c LIMIT 1");
        $stmt->execute([":c" => strtoupper(trim($code))]);
        $room = $stmt->fetch();
        return $room ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM game_rooms WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => $id]);
        $room = $stmt->fetch();
        return $room ?: null;
    }

    public static function getPlayers(int $roomId): array
    {
        $stmt = self::db()->prepare(
            "SELECT grp.*, u.username, p.full_name, p.avatar, p.level, r.elo_rating
             FROM game_room_players grp
             JOIN users u ON u.id = grp.user_id
             JOIN player_profiles p ON p.user_id = u.id
             LEFT JOIN player_ratings r ON r.user_id = u.id AND r.mode = '1vs1'
             WHERE grp.room_id = :rid
             ORDER BY grp.is_host DESC, grp.joined_at ASC"
        );
        $stmt->execute([":rid" => $roomId]);
        return $stmt->fetchAll();
    }

    public static function addPlayer(int $roomId, int $userId, string $team = "A"): bool
    {
        // Verificar si ya esta
        $stmt = self::db()->prepare("SELECT id FROM game_room_players WHERE room_id = :rid AND user_id = :uid LIMIT 1");
        $stmt->execute([":rid" => $roomId, ":uid" => $userId]);
        if ($stmt->fetch()) {
            return true;
        }

        $ins = self::db()->prepare(
            "INSERT INTO game_room_players (room_id, user_id, team, is_ready, is_host)
             VALUES (:rid, :uid, :tm, 0, 0)"
        );
        return $ins->execute([":rid" => $roomId, ":uid" => $userId, ":tm" => $team]);
    }

    public static function removePlayer(int $roomId, int $userId): void
    {
        $stmt = self::db()->prepare("DELETE FROM game_room_players WHERE room_id = :rid AND user_id = :uid");
        $stmt->execute([":rid" => $roomId, ":uid" => $userId]);

        // Si la sala queda vacia, cancelarla
        $countStmt = self::db()->prepare("SELECT COUNT(*) FROM game_room_players WHERE room_id = :rid");
        $countStmt->execute([":rid" => $roomId]);
        if ((int)$countStmt->fetchColumn() === 0) {
            self::updateStatus($roomId, "cancelled");
        }
    }

    public static function toggleReady(int $roomId, int $userId): bool
    {
        $stmt = self::db()->prepare(
            "UPDATE game_room_players SET is_ready = IF(is_ready = 1, 0, 1) WHERE room_id = :rid AND user_id = :uid"
        );
        return $stmt->execute([":rid" => $roomId, ":uid" => $userId]);
    }

    public static function changeTeam(int $roomId, int $userId, string $newTeam): bool
    {
        $stmt = self::db()->prepare(
            "UPDATE game_room_players SET team = :tm WHERE room_id = :rid AND user_id = :uid"
        );
        return $stmt->execute([":tm" => $newTeam, ":rid" => $roomId, ":uid" => $userId]);
    }

    public static function updateStatus(int $roomId, string $status): bool
    {
        $stmt = self::db()->prepare("UPDATE game_rooms SET status = :st WHERE id = :id");
        return $stmt->execute([":st" => $status, ":id" => $roomId]);
    }

    public static function getPublicWaitingRooms(): array
    {
        $sql = "SELECT gr.*, u.username AS creator_username,
                       (SELECT COUNT(*) FROM game_room_players grp WHERE grp.room_id = gr.id) AS current_players
                FROM game_rooms gr
                JOIN users u ON u.id = gr.creator_id
                WHERE gr.status = 'waiting' AND gr.is_private = 0
                ORDER BY gr.created_at DESC LIMIT 20";
        return self::db()->query($sql)->fetchAll();
    }
}

