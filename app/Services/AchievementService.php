<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use App\Models\Achievement;
use PDO;

class AchievementService
{
    public static function checkAfterMatch(int $userId, bool $won, int $correctInMatch, float $minResponseTime): array
    {
        $unlocked = [];
        $db = Database::getConnection();

        // 1. Obtener estadisticas del usuario
        $stmt = $db->prepare("SELECT * FROM player_statistics WHERE user_id = :uid LIMIT 1");
        $stmt->execute([":uid" => $userId]);
        $stats = $stmt->fetch();

        if (!$stats) {
            return [];
        }

        $wins = (int)$stats["matches_won"];
        $streak = (int)$stats["win_streak"];
        $totalCorrect = (int)$stats["total_correct_answers"];

        // Primera victoria
        if ($won && $wins >= 1) {
            if (Achievement::unlock($userId, "primera_victoria")) {
                $unlocked[] = "Primera Victoria";
            }
        }

        // 10 victorias
        if ($wins >= 10) {
            if (Achievement::unlock($userId, "veterano_10")) {
                $unlocked[] = "Veterano de Batalla";
            }
        }

        // 50 victorias
        if ($wins >= 50) {
            if (Achievement::unlock($userId, "mariscal_50")) {
                $unlocked[] = "Gran Mariscal del Bicentenario";
            }
        }

        // Racha de 5
        if ($streak >= 5) {
            if (Achievement::unlock($userId, "racha_5")) {
                $unlocked[] = "Racha Imparable";
            }
        }

        // 100 respuestas correctas
        if ($totalCorrect >= 100) {
            if (Achievement::unlock($userId, "respuestas_100")) {
                $unlocked[] = "Enciclopedia Viviente";
            }
        }

        // Velocidad pura (respuesta correcta en < 4 seg)
        if ($minResponseTime > 0 && $minResponseTime <= 4.0) {
            if (Achievement::unlock($userId, "velocidad_pura")) {
                $unlocked[] = "Reflejos Chaski";
            }
        }

        return $unlocked;
    }
}

