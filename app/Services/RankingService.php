<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use PDO;

class RankingService
{
    public static function getGlobalRanking(string $mode = "1vs1", int $limit = 50): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT u.id, u.username, p.full_name, p.avatar, p.level, p.total_points, p.connection_status,
                    r.elo_rating, r.peak_elo_rating,
                    s.matches_played, s.matches_won, s.matches_lost, s.win_ratio, s.win_streak
             FROM users u
             JOIN player_profiles p ON p.user_id = u.id
             JOIN player_ratings r ON r.user_id = u.id AND r.mode = :md
             LEFT JOIN player_statistics s ON s.user_id = u.id
             WHERE u.status = 'active'
             ORDER BY r.elo_rating DESC, s.matches_won DESC, p.total_points DESC
             LIMIT :lim"
        );
        $stmt->bindValue(":md", $mode);
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getWeeklyRanking(int $limit = 30): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT u.id, u.username, p.full_name, p.avatar, p.level,
                    COUNT(m.id) AS matches_count,
                    SUM(CASE WHEN (m.winner_user_id = u.id OR (m.mode = '2vs2' AND mp.team = m.winner_team)) THEN 1 ELSE 0 END) AS wins_count,
                    SUM(mp.score) AS total_weekly_score
             FROM match_players mp
             JOIN matches m ON m.id = mp.match_id
             JOIN users u ON u.id = mp.user_id
             JOIN player_profiles p ON p.user_id = u.id
             WHERE m.finished_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.status = 'finished'
             GROUP BY u.id, u.username, p.full_name, p.avatar, p.level
             ORDER BY wins_count DESC, total_weekly_score DESC
             LIMIT :lim"
        );
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getMonthlyRanking(int $limit = 30): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT u.id, u.username, p.full_name, p.avatar, p.level,
                    COUNT(m.id) AS matches_count,
                    SUM(CASE WHEN (m.winner_user_id = u.id OR (m.mode = '2vs2' AND mp.team = m.winner_team)) THEN 1 ELSE 0 END) AS wins_count,
                    SUM(mp.score) AS total_monthly_score
             FROM match_players mp
             JOIN matches m ON m.id = mp.match_id
             JOIN users u ON u.id = mp.user_id
             JOIN player_profiles p ON p.user_id = u.id
             WHERE m.finished_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND m.status = 'finished'
             GROUP BY u.id, u.username, p.full_name, p.avatar, p.level
             ORDER BY wins_count DESC, total_monthly_score DESC
             LIMIT :lim"
        );
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getCategoryRanking(int $categoryId, int $limit = 20): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT u.id, u.username, p.full_name, p.avatar, p.level,
                    COUNT(pa.id) AS correct_answers_count
             FROM player_answers pa
             JOIN match_questions mq ON mq.id = pa.match_question_id
             JOIN questions q ON q.id = mq.question_id
             JOIN users u ON u.id = pa.user_id
             JOIN player_profiles p ON p.user_id = u.id
             WHERE q.category_id = :cid AND pa.is_correct = 1
             GROUP BY u.id, u.username, p.full_name, p.avatar, p.level
             ORDER BY correct_answers_count DESC
             LIMIT :lim"
        );
        $stmt->bindValue(":cid", $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

