<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Achievement extends Model
{
    public static function getAll(): array
    {
        $stmt = self::db()->query("SELECT * FROM achievements ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public static function getUserAchievements(int $userId): array
    {
        $stmt = self::db()->prepare(
            "SELECT a.*, pa.unlocked_at
             FROM achievements a
             LEFT JOIN player_achievements pa ON pa.achievement_id = a.id AND pa.user_id = :uid
             ORDER BY a.id ASC"
        );
        $stmt->execute([":uid" => $userId]);
        return $stmt->fetchAll();
    }

    public static function unlock(int $userId, string $code): bool
    {
        $stmt = self::db()->prepare("SELECT * FROM achievements WHERE code = :code LIMIT 1");
        $stmt->execute([":code" => $code]);
        $ach = $stmt->fetch();
        if (!$ach) {
            return false;
        }

        // Verificar si ya lo tiene
        $check = self::db()->prepare(
            "SELECT id FROM player_achievements WHERE user_id = :uid AND achievement_id = :aid LIMIT 1"
        );
        $check->execute([":uid" => $userId, ":aid" => $ach["id"]]);
        if ($check->fetch()) {
            return false;
        }

        self::beginTransaction();
        try {
            $ins = self::db()->prepare(
                "INSERT INTO player_achievements (user_id, achievement_id) VALUES (:uid, :aid)"
            );
            $ins->execute([":uid" => $userId, ":aid" => $ach["id"]]);

            // Sumar puntos y XP de recompensa
            PlayerProfile::addExperience($userId, (int)$ach["xp_reward"], "Logro desbloqueado: " . $ach["name"]);

            $upd = self::db()->prepare(
                "UPDATE player_profiles SET total_points = total_points + :pts WHERE user_id = :uid"
            );
            $upd->execute([":pts" => $ach["points_reward"], ":uid" => $userId]);

            // Notificacion
            Notification::create(
                $userId,
                "achievement",
                "¡Logro Desbloqueado!",
                "Has obtenido el logro '" . $ach["name"] . "'. +" . $ach["xp_reward"] . " XP.",
                ["achievement_id" => $ach["id"]]
            );

            self::commit();
            return true;
        } catch (\Throwable $e) {
            self::rollBack();
            return false;
        }
    }
}

