<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Notification extends Model
{
    public static function create(int $userId, string $type, string $title, string $message, ?array $data = null): int
    {
        $stmt = self::db()->prepare(
            "INSERT INTO notifications (user_id, type, title, message, data_json, is_read)
             VALUES (:uid, :tp, :ti, :msg, :dj, 0)"
        );
        $stmt->execute([
            ":uid" => $userId,
            ":tp" => $type,
            ":ti" => $title,
            ":msg" => $message,
            ":dj" => $data !== null ? json_encode($data, JSON_UNESCAPED_UNICODE) : null,
        ]);
        return (int)self::db()->lastInsertId();
    }

    public static function getUnreadByUser(int $userId, int $limit = 10): array
    {
        $stmt = self::db()->prepare(
            "SELECT * FROM notifications WHERE user_id = :uid AND is_read = 0 ORDER BY created_at DESC LIMIT :lim"
        );
        $stmt->bindValue(":uid", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function markAllRead(int $userId): bool
    {
        $stmt = self::db()->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid");
        return $stmt->execute([":uid" => $userId]);
    }
}

