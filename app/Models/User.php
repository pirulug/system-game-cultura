<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    public static function findById(int $id): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT u.id, u.username, u.email, u.role, u.status, u.created_at,
                    p.full_name, p.avatar, p.bio, p.level, p.experience_points, p.total_points, p.connection_status
             FROM users u
             LEFT JOIN player_profiles p ON p.user_id = u.id
             WHERE u.id = :id LIMIT 1"
        );
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByUsername(string $username): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT u.*, p.full_name, p.avatar, p.level, p.experience_points, p.total_points
             FROM users u
             LEFT JOIN player_profiles p ON p.user_id = u.id
             WHERE u.username = :username LIMIT 1"
        );
        $stmt->execute([":username" => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([":email" => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $username, string $email, string $password, string $fullName): int
    {
        self::beginTransaction();
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = self::db()->prepare(
                "INSERT INTO users (username, email, password_hash, role, status) VALUES (:u, :e, :p, 'player', 'active')"
            );
            $stmt->execute([
                ":u" => $username,
                ":e" => $email,
                ":p" => $hash,
            ]);
            $userId = (int)self::db()->lastInsertId();

            $profileStmt = self::db()->prepare(
                "INSERT INTO player_profiles (user_id, full_name, avatar, level, experience_points, total_points, connection_status)
                 VALUES (:uid, :fn, 'default_avatar.png', 1, 0, 0, 'online')"
            );
            $profileStmt->execute([
                ":uid" => $userId,
                ":fn" => $fullName,
            ]);

            $statsStmt = self::db()->prepare(
                "INSERT INTO player_statistics (user_id) VALUES (:uid)"
            );
            $statsStmt->execute([":uid" => $userId]);

            $r1 = self::db()->prepare("INSERT INTO player_ratings (user_id, mode, elo_rating, peak_elo_rating) VALUES (:uid, '1vs1', 1200, 1200)");
            $r1->execute([":uid" => $userId]);

            $r2 = self::db()->prepare("INSERT INTO player_ratings (user_id, mode, elo_rating, peak_elo_rating) VALUES (:uid, '2vs2', 1200, 1200)");
            $r2->execute([":uid" => $userId]);

            self::commit();
            return $userId;
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }

    public static function updateConnectionStatus(int $userId, string $status): void
    {
        $stmt = self::db()->prepare(
            "UPDATE player_profiles SET connection_status = :st, last_seen_at = NOW() WHERE user_id = :uid"
        );
        $stmt->execute([":st" => $status, ":uid" => $userId]);
    }

    public static function getAllWithStats(int $limit = 50, int $offset = 0): array
    {
        $stmt = self::db()->prepare(
            "SELECT u.id, u.username, u.email, u.role, u.status, u.created_at,
                    p.full_name, p.level, p.total_points, p.avatar, p.connection_status,
                    s.matches_played, s.matches_won, s.matches_lost, s.win_ratio,
                    r1.elo_rating AS elo_1vs1, r2.elo_rating AS elo_2vs2
             FROM users u
             LEFT JOIN player_profiles p ON p.user_id = u.id
             LEFT JOIN player_statistics s ON s.user_id = u.id
             LEFT JOIN player_ratings r1 ON r1.user_id = u.id AND r1.mode = '1vs1'
             LEFT JOIN player_ratings r2 ON r2.user_id = u.id AND r2.mode = '2vs2'
             ORDER BY u.id DESC
             LIMIT :lim OFFSET :off"
        );
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":off", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $userId, string $status): bool
    {
        $stmt = self::db()->prepare("UPDATE users SET status = :st WHERE id = :id");
        return $stmt->execute([":st" => $status, ":id" => $userId]);
    }

    public static function updateProfile(int $userId, string $fullName, string $bio, ?string $avatar = null): bool
    {
        if ($avatar !== null) {
            $stmt = self::db()->prepare("UPDATE player_profiles SET full_name = :fn, bio = :b, avatar = :av WHERE user_id = :uid");
            return $stmt->execute([":fn" => $fullName, ":b" => $bio, ":av" => $avatar, ":uid" => $userId]);
        }
        $stmt = self::db()->prepare("UPDATE player_profiles SET full_name = :fn, bio = :b WHERE user_id = :uid");
        return $stmt->execute([":fn" => $fullName, ":b" => $bio, ":uid" => $userId]);
    }

    public static function countAll(): int
    {
        $stmt = self::db()->query("SELECT COUNT(*) FROM users");
        return (int)$stmt->fetchColumn();
    }
}

