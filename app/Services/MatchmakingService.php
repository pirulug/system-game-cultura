<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use App\Models\GameRoom;
use App\Models\MatchGame;
use App\Models\Question;
use App\Models\User;
use PDO;

class MatchmakingService
{
    public static function joinQueue(int $userId, string $mode = "1vs1"): array
    {
        $db = Database::getConnection();

        // 1. Obtener ELO del usuario
        $eloStmt = $db->prepare("SELECT elo_rating FROM player_ratings WHERE user_id = :uid AND mode = :md LIMIT 1");
        $eloStmt->execute([":uid" => $userId, ":md" => $mode]);
        $elo = (int)($eloStmt->fetchColumn() ?: 1200);

        // 2. Limpiar registros viejos del usuario
        $del = $db->prepare("DELETE FROM matchmaking_queue WHERE user_id = :uid");
        $del->execute([":uid" => $userId]);

        // 3. Insertar en cola
        $ins = $db->prepare(
            "INSERT INTO matchmaking_queue (user_id, mode, elo_rating, status) VALUES (:uid, :md, :elo, 'searching')"
        );
        $ins->execute([":uid" => $userId, ":md" => $mode, ":elo" => $elo]);

        // 4. Intentar emparejar de inmediato
        return self::tryMatchmaking($userId, $mode, $elo);
    }

    public static function checkQueue(int $userId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM matchmaking_queue WHERE user_id = :uid LIMIT 1");
        $stmt->execute([":uid" => $userId]);
        $row = $stmt->fetch();

        if (!$row) {
            return ["status" => "idle"];
        }

        if ($row["status"] === "matched" && !empty($row["matched_room_id"])) {
            $matchId = (int)$row["matched_room_id"]; // Reutilizamos esta columna para guardar match_id
            // Eliminar de cola
            $del = $db->prepare("DELETE FROM matchmaking_queue WHERE user_id = :uid");
            $del->execute([":uid" => $userId]);

            return [
                "status" => "matched",
                "match_id" => $matchId,
            ];
        }

        // Si sigue buscando, volver a intentar emparejar
        $tryAgain = self::tryMatchmaking($userId, $row["mode"], (int)$row["elo_rating"]);
        if ($tryAgain["status"] === "matched") {
            return $tryAgain;
        }

        return ["status" => "searching"];
    }

    public static function leaveQueue(int $userId): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM matchmaking_queue WHERE user_id = :uid");
        return $stmt->execute([":uid" => $userId]);
    }

    private static function tryMatchmaking(int $userId, string $mode, int $elo): array
    {
        $db = Database::getConnection();

        if ($mode === "1vs1") {
            // Buscar un rival disponible con modo 1vs1
            $stmt = $db->prepare(
                "SELECT * FROM matchmaking_queue
                 WHERE mode = '1vs1' AND status = 'searching' AND user_id != :uid
                 ORDER BY ABS(elo_rating - :elo) ASC, joined_at ASC
                 LIMIT 1"
            );
            $stmt->execute([":uid" => $userId, ":elo" => $elo]);
            $opponent = $stmt->fetch();

            if (!$opponent) {
                return ["status" => "searching"];
            }

            $opponentId = (int)$opponent["user_id"];

            // Crear partida directa
            $questions = Question::getRandomQuestionsForMatch(10, null, null, []);
            if (empty($questions)) {
                return ["status" => "error", "message" => "No hay preguntas en el banco."];
            }

            $questionIds = array_column($questions, "id");
            $matchId = MatchGame::createMatch(null, "1vs1", count($questions), 15);

            MatchGame::addPlayers($matchId, [
                ["user_id" => $userId, "team" => "A", "mode" => "1vs1"],
                ["user_id" => $opponentId, "team" => "B", "mode" => "1vs1"],
            ]);

            MatchGame::assignQuestions($matchId, $questionIds);
            GameEngineService::startRound($matchId, 1, 15);

            // Actualizar ambos registros en matchmaking_queue
            $upd = $db->prepare(
                "UPDATE matchmaking_queue SET status = 'matched', matched_room_id = :mid WHERE user_id IN (:u1, :u2)"
            );
            $upd->execute([":mid" => $matchId, ":u1" => $userId, ":u2" => $opponentId]);

            return [
                "status" => "matched",
                "match_id" => $matchId,
            ];
        }

        if ($mode === "2vs2") {
            // Buscar 3 companeros y rivales
            $stmt = $db->prepare(
                "SELECT * FROM matchmaking_queue
                 WHERE mode = '2vs2' AND status = 'searching' AND user_id != :uid
                 ORDER BY joined_at ASC
                 LIMIT 3"
            );
            $stmt->execute([":uid" => $userId]);
            $candidates = $stmt->fetchAll();

            if (count($candidates) < 3) {
                return ["status" => "searching"];
            }

            $allPlayerIds = array_merge([$userId], array_column($candidates, "user_id"));

            $questions = Question::getRandomQuestionsForMatch(10, null, null, []);
            $questionIds = array_column($questions, "id");
            $matchId = MatchGame::createMatch(null, "2vs2", count($questions), 15);

            MatchGame::addPlayers($matchId, [
                ["user_id" => $allPlayerIds[0], "team" => "A", "mode" => "2vs2"],
                ["user_id" => $allPlayerIds[1], "team" => "A", "mode" => "2vs2"],
                ["user_id" => $allPlayerIds[2], "team" => "B", "mode" => "2vs2"],
                ["user_id" => $allPlayerIds[3], "team" => "B", "mode" => "2vs2"],
            ]);

            MatchGame::assignQuestions($matchId, $questionIds);
            GameEngineService::startRound($matchId, 1, 15);

            $placeholders = implode(",", array_fill(0, count($allPlayerIds), "?"));
            $upd = $db->prepare(
                "UPDATE matchmaking_queue SET status = 'matched', matched_room_id = ? WHERE user_id IN ({$placeholders})"
            );
            $upd->execute(array_merge([$matchId], $allPlayerIds));

            return [
                "status" => "matched",
                "match_id" => $matchId,
            ];
        }

        return ["status" => "searching"];
    }
}

