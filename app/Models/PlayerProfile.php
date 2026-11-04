<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class PlayerProfile extends Model
{
    public static function getByUserId(int $userId): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT p.*, u.username, u.email, u.role, u.status, u.created_at AS user_created_at,
                    s.matches_played, s.matches_won, s.matches_lost, s.matches_tied,
                    s.win_streak, s.max_win_streak, s.win_ratio, s.total_correct_answers,
                    r1.elo_rating AS elo_1vs1, r1.peak_elo_rating AS peak_elo_1vs1,
                    r2.elo_rating AS elo_2vs2, r2.peak_elo_rating AS peak_elo_2vs2
             FROM player_profiles p
             JOIN users u ON u.id = p.user_id
             LEFT JOIN player_statistics s ON s.user_id = u.id
             LEFT JOIN player_ratings r1 ON r1.user_id = u.id AND r1.mode = '1vs1'
             LEFT JOIN player_ratings r2 ON r2.user_id = u.id AND r2.mode = '2vs2'
             WHERE p.user_id = :uid LIMIT 1"
        );
        $stmt->execute([":uid" => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function addExperience(int $userId, int $xpAmount, string $source, ?int $matchId = null): void
    {
        if ($xpAmount <= 0) {
            return;
        }

        self::beginTransaction();
        try {
            // Historial de XP
            $historyStmt = self::db()->prepare(
                "INSERT INTO experience_history (user_id, match_id, amount, source) VALUES (:uid, :mid, :amt, :src)"
            );
            $historyStmt->execute([
                ":uid" => $userId,
                ":mid" => $matchId,
                ":amt" => $xpAmount,
                ":src" => $source,
            ]);

            // Actualizar perfil
            $stmt = self::db()->prepare("SELECT experience_points FROM player_profiles WHERE user_id = :uid LIMIT 1");
            $stmt->execute([":uid" => $userId]);
            $currentXp = (int)$stmt->fetchColumn();

            $newXp = $currentXp + $xpAmount;
            // Formula de nivel: Nivel = 1 + int(raiz_cuadrada(XP / 100))
            $newLevel = 1 + (int)floor(sqrt($newXp / 100));

            $update = self::db()->prepare(
                "UPDATE player_profiles SET experience_points = :xp, level = :lvl WHERE user_id = :uid"
            );
            $update->execute([
                ":xp" => $newXp,
                ":lvl" => $newLevel,
                ":uid" => $userId,
            ]);

            self::commit();
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }

    public static function updateStatsAfterMatch(int $userId, bool $won, bool $tied, int $correct, int $points): void
    {
        self::beginTransaction();
        try {
            $stmt = self::db()->prepare("SELECT * FROM player_statistics WHERE user_id = :uid LIMIT 1");
            $stmt->execute([":uid" => $userId]);
            $stats = $stmt->fetch();

            if (!$stats) {
                $ins = self::db()->prepare("INSERT INTO player_statistics (user_id) VALUES (:uid)");
                $ins->execute([":uid" => $userId]);
                $stats = ["matches_played" => 0, "matches_won" => 0, "matches_lost" => 0, "matches_tied" => 0, "win_streak" => 0, "max_win_streak" => 0, "total_correct_answers" => 0];
            }

            $played = (int)$stats["matches_played"] + 1;
            $wonCount = (int)$stats["matches_won"] + ($won ? 1 : 0);
            $tiedCount = (int)$stats["matches_tied"] + ($tied ? 1 : 0);
            $lostCount = (int)$stats["matches_lost"] + ((!$won && !$tied) ? 1 : 0);

            $currentStreak = $won ? ((int)$stats["win_streak"] + 1) : 0;
            $maxStreak = max((int)$stats["max_win_streak"], $currentStreak);
            $correctTotal = (int)$stats["total_correct_answers"] + $correct;
            $winRatio = ($played > 0) ? round(($wonCount / $played) * 100, 2) : 0.00;

            $updateStats = self::db()->prepare(
                "UPDATE player_statistics SET
                    matches_played = :mp,
                    matches_won = :mw,
                    matches_lost = :ml,
                    matches_tied = :mt,
                    win_streak = :ws,
                    max_win_streak = :mws,
                    total_correct_answers = :tca,
                    win_ratio = :wr
                 WHERE user_id = :uid"
            );
            $updateStats->execute([
                ":mp" => $played,
                ":mw" => $wonCount,
                ":ml" => $lostCount,
                ":mt" => $tiedCount,
                ":ws" => $currentStreak,
                ":mws" => $maxStreak,
                ":tca" => $correctTotal,
                ":wr" => $winRatio,
                ":uid" => $userId,
            ]);

            // Sumar puntos al perfil
            $updateProfile = self::db()->prepare(
                "UPDATE player_profiles SET total_points = total_points + :pts WHERE user_id = :uid"
            );
            $updateProfile->execute([":pts" => $points, ":uid" => $userId]);

            self::commit();
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }
}

