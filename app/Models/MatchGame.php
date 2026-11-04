<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class MatchGame extends Model
{
    public static function createMatch(
        ?int $roomId,
        string $mode,
        int $questionsCount,
        int $secondsPerQuestion
    ): int {
        $stmt = self::db()->prepare(
            "INSERT INTO matches (room_id, mode, status, current_question_index, total_questions, seconds_per_question, started_at)
             VALUES (:rid, :md, 'in_progress', 0, :qc, :spq, NOW())"
        );
        $stmt->execute([
            ":rid" => $roomId,
            ":md" => $mode,
            ":qc" => $questionsCount,
            ":spq" => $secondsPerQuestion,
        ]);
        return (int)self::db()->lastInsertId();
    }

    public static function addPlayers(int $matchId, array $players): void
    {
        $stmt = self::db()->prepare(
            "INSERT INTO match_players (match_id, user_id, team, score, rating_before, rating_after)
             VALUES (:mid, :uid, :tm, 0, :rb1, :rb2)"
        );

        $getRating = self::db()->prepare(
            "SELECT elo_rating FROM player_ratings WHERE user_id = :uid AND mode = :md LIMIT 1"
        );

        foreach ($players as $p) {
            $mode = ($p["mode"] ?? "1vs1");
            $getRating->execute([":uid" => $p["user_id"], ":md" => $mode]);
            $currentRating = (int)($getRating->fetchColumn() ?: 1200);

            $stmt->execute([
                ":mid" => $matchId,
                ":uid" => $p["user_id"],
                ":tm" => $p["team"] ?? "A",
                ":rb1" => $currentRating,
                ":rb2" => $currentRating,
            ]);
        }
    }

    public static function assignQuestions(int $matchId, array $questionIds): void
    {
        $stmt = self::db()->prepare(
            "INSERT INTO match_questions (match_id, question_id, question_order)
             VALUES (:mid, :qid, :ord)"
        );
        foreach ($questionIds as $index => $qid) {
            $stmt->execute([
                ":mid" => $matchId,
                ":qid" => $qid,
                ":ord" => $index + 1,
            ]);
        }
    }

    public static function findById(int $matchId): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM matches WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => $matchId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getPlayers(int $matchId): array
    {
        $stmt = self::db()->prepare(
            "SELECT mp.*, u.username, p.full_name, p.avatar, p.level
             FROM match_players mp
             JOIN users u ON u.id = mp.user_id
             JOIN player_profiles p ON p.user_id = u.id
             WHERE mp.match_id = :mid
             ORDER BY mp.team ASC, mp.score DESC"
        );
        $stmt->execute([":mid" => $matchId]);
        return $stmt->fetchAll();
    }

    public static function getMatchQuestionByOrder(int $matchId, int $order): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT mq.id AS match_question_id, mq.match_id, mq.question_order,
                    q.id AS question_id, q.category_id, q.question_text, q.difficulty, q.explanation, q.source_reference,
                    c.name AS category_name, c.icon_class AS category_icon
             FROM match_questions mq
             JOIN questions q ON q.id = mq.question_id
             JOIN categories c ON c.id = q.category_id
             WHERE mq.match_id = :mid AND mq.question_order = :ord
             LIMIT 1"
        );
        $stmt->execute([":mid" => $matchId, ":ord" => $order]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function recordAnswer(
        int $matchId,
        int $matchQuestionId,
        int $userId,
        ?int $selectedOptionId,
        bool $isCorrect,
        int $points,
        float $responseTime
    ): bool {
        self::beginTransaction();
        try {
            $stmt = self::db()->prepare(
                "INSERT INTO player_answers (match_id, match_question_id, user_id, selected_option_id, is_correct, points_awarded, response_time_seconds)
                 VALUES (:mid, :mqid, :uid, :opt, :corr, :pts, :rt)"
            );
            $stmt->execute([
                ":mid" => $matchId,
                ":mqid" => $matchQuestionId,
                ":uid" => $userId,
                ":opt" => $selectedOptionId,
                ":corr" => $isCorrect ? 1 : 0,
                ":pts" => $points,
                ":rt" => $responseTime,
            ]);

            // Actualizar marcador de match_players
            $updateMp = self::db()->prepare(
                "UPDATE match_players
                 SET score = score + :pts,
                     correct_answers = correct_answers + :corr,
                     incorrect_answers = incorrect_answers + :inc
                 WHERE match_id = :mid AND user_id = :uid"
            );
            $updateMp->execute([
                ":pts" => $points,
                ":corr" => $isCorrect ? 1 : 0,
                ":inc" => $isCorrect ? 0 : 1,
                ":mid" => $matchId,
                ":uid" => $userId,
            ]);

            self::commit();
            return true;
        } catch (\Throwable $e) {
            self::rollBack();
            return false;
        }
    }

    public static function getAnswersForMatchQuestion(int $matchQuestionId): array
    {
        $stmt = self::db()->prepare(
            "SELECT pa.*, u.username
             FROM player_answers pa
             JOIN users u ON u.id = pa.user_id
             WHERE pa.match_question_id = :mqid"
        );
        $stmt->execute([":mqid" => $matchQuestionId]);
        return $stmt->fetchAll();
    }

    public static function getPlayerAnswerForQuestion(int $matchQuestionId, int $userId): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT * FROM player_answers WHERE match_question_id = :mqid AND user_id = :uid LIMIT 1"
        );
        $stmt->execute([":mqid" => $matchQuestionId, ":uid" => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updateRoundTimers(int $matchId, int $questionIndex, string $roundStart, string $roundEnd, string $roundReveal): void
    {
        $stmt = self::db()->prepare(
            "UPDATE matches
             SET current_question_index = :idx,
                 status = 'in_progress',
                 round_start_time = :st,
                 round_end_time = :et,
                 round_reveal_time = :rt
             WHERE id = :id"
        );
        $stmt->execute([
            ":idx" => $questionIndex,
            ":st" => $roundStart,
            ":et" => $roundEnd,
            ":rt" => $roundReveal,
            ":id" => $matchId,
        ]);
    }

    public static function setShowingResult(int $matchId): void
    {
        $stmt = self::db()->prepare("UPDATE matches SET status = 'showing_result' WHERE id = :id");
        $stmt->execute([":id" => $matchId]);
    }

    public static function finishMatch(int $matchId, ?int $winnerUserId, ?string $winnerTeam): void
    {
        $stmt = self::db()->prepare(
            "UPDATE matches
             SET status = 'finished',
                 winner_user_id = :wuid,
                 winner_team = :wtm,
                 finished_at = NOW()
             WHERE id = :id"
        );
        $stmt->execute([
            ":wuid" => $winnerUserId,
            ":wtm" => $winnerTeam,
            ":id" => $matchId,
        ]);
    }

    public static function updatePlayerResult(int $matchId, int $userId, int $ratingAfter, int $ratingChange, int $xp): void
    {
        $stmt = self::db()->prepare(
            "UPDATE match_players
             SET rating_after = :ra, rating_change = :rc, experience_gained = :xp
             WHERE match_id = :mid AND user_id = :uid"
        );
        $stmt->execute([
            ":ra" => $ratingAfter,
            ":rc" => $ratingChange,
            ":xp" => $xp,
            ":mid" => $matchId,
            ":uid" => $userId,
        ]);
    }

    public static function getRecentMatches(int $limit = 15): array
    {
        $stmt = self::db()->prepare(
            "SELECT m.*, u.username AS winner_name
             FROM matches m
             LEFT JOIN users u ON u.id = m.winner_user_id
             WHERE m.status = 'finished'
             ORDER BY m.finished_at DESC LIMIT :lim"
        );
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        $matches = $stmt->fetchAll();

        foreach ($matches as &$m) {
            $m["players"] = self::getPlayers((int)$m["id"]);
        }

        return $matches;
    }

    public static function getPlayerHistory(int $userId, int $limit = 10): array
    {
        $stmt = self::db()->prepare(
            "SELECT m.*, mp.score, mp.correct_answers, mp.incorrect_answers, mp.rating_change, mp.experience_gained, mp.team,
                    u.username AS winner_name
             FROM match_players mp
             JOIN matches m ON m.id = mp.match_id
             LEFT JOIN users u ON u.id = m.winner_user_id
             WHERE mp.user_id = :uid AND m.status = 'finished'
             ORDER BY m.finished_at DESC LIMIT :lim"
        );
        $stmt->bindValue(":uid", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
