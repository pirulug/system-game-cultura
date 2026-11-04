<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class AdminLog extends Model
{
    public static function log(int $adminUserId, string $action, string $entityType, ?int $entityId, ?string $details = null): void
    {
        $ip = $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";
        $stmt = self::db()->prepare(
            "INSERT INTO admin_logs (admin_user_id, action, entity_type, entity_id, details, ip_address)
             VALUES (:au, :act, :et, :eid, :det, :ip)"
        );
        $stmt->execute([
            ":au" => $adminUserId,
            ":act" => $action,
            ":et" => $entityType,
            ":eid" => $entityId,
            ":det" => $details,
            ":ip" => $ip,
        ]);
    }

    public static function getRecent(int $limit = 20): array
    {
        $stmt = self::db()->prepare(
            "SELECT al.*, u.username AS admin_username
             FROM admin_logs al
             JOIN users u ON u.id = al.admin_user_id
             ORDER BY al.created_at DESC LIMIT :lim"
        );
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

