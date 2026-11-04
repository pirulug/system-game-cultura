<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\App;
use App\Config\Database;
use App\Models\Category;
use App\Models\GameRoom;
use App\Models\MatchGame;
use App\Models\PlayerProfile;
use App\Models\Question;
use App\Models\User;
use PDO;

class GameEngineService
{
    public const REVEAL_DURATION_SECONDS = 4;

    public static function startMatchFromRoom(int $roomId): array
    {
        $room = GameRoom::findById($roomId);
        if (!$room) {
            return ["success" => false, "message" => "Sala no encontrada."];
        }

        $players = GameRoom::getPlayers($roomId);
        if (count($players) < 2) {
            return ["success" => false, "message" => "Se necesitan al menos 2 jugadores para iniciar."];
        }

        $userIds = array_column($players, "user_id");

        // 1. Obtener preguntas excluyendo preguntas recientes de los jugadores
        $recentIds = Question::getRecentQuestionIdsForPlayers($userIds, 20);
        $questionsCount = (int)$room["questions_count"];
        $categoryId = !empty($room["allowed_category_id"]) ? (int)$room["allowed_category_id"] : null;
        $difficulty = ($room["difficulty"] !== "all") ? $room["difficulty"] : null;

        $questions = Question::getRandomQuestionsForMatch($questionsCount, $categoryId, $difficulty, $recentIds);

        if (count($questions) < $questionsCount) {
            // Reintentar sin excluir
            $questions = Question::getRandomQuestionsForMatch($questionsCount, $categoryId, $difficulty, []);
        }

        if (empty($questions)) {
            return ["success" => false, "message" => "No hay preguntas disponibles con la configuración elegida."];
        }

        $questionIds = array_column($questions, "id");

        // 2. Crear partida
        $matchId = MatchGame::createMatch(
            $roomId,
            $room["mode"],
            count($questions),
            (int)$room["seconds_per_question"]
        );

        // 3. Asignar jugadores
        $matchPlayersData = [];
        foreach ($players as $p) {
            $matchPlayersData[] = [
                "user_id" => (int)$p["user_id"],
                "team" => $p["team"] ?? "A",
                "mode" => $room["mode"],
            ];
            User::updateConnectionStatus((int)$p["user_id"], "in_game");
        }
        MatchGame::addPlayers($matchId, $matchPlayersData);

        // 4. Asignar preguntas
        MatchGame::assignQuestions($matchId, $questionIds);

        // 5. Iniciar primera ronda
        self::startRound($matchId, 1, (int)$room["seconds_per_question"]);

        // 6. Actualizar estado de la sala
        GameRoom::updateStatus($roomId, "in_game");

        return ["success" => true, "match_id" => $matchId];
    }

    public static function startRound(int $matchId, int $roundOrder, int $secondsPerQuestion): void
    {
        $now = time();
        $startTime = date("Y-m-d H:i:s", $now);
        $endTime = date("Y-m-d H:i:s", $now + $secondsPerQuestion);
        $revealTime = date("Y-m-d H:i:s", $now + $secondsPerQuestion + self::REVEAL_DURATION_SECONDS);

        MatchGame::updateRoundTimers($matchId, $roundOrder, $startTime, $endTime, $revealTime);
    }

    public static function getMatchState(int $matchId, int $currentUserId): array
    {
        $match = MatchGame::findById($matchId);
        if (!$match) {
            return ["error" => "Partida no encontrada."];
        }

        $now = time();
        $roundStart = !empty($match["round_start_time"]) ? strtotime($match["round_start_time"]) : 0;
        $roundEnd = !empty($match["round_end_time"]) ? strtotime($match["round_end_time"]) : 0;
        $roundReveal = !empty($match["round_reveal_time"]) ? strtotime($match["round_reveal_time"]) : 0;
        $currentIndex = (int)$match["current_question_index"];
        $totalQuestions = (int)$match["total_questions"];
        $secondsPerQuestion = (int)$match["seconds_per_question"];

        // Si la partida esta activa, evaluar transicion automatica
        if ($match["status"] === "in_progress" || $match["status"] === "showing_result") {
            // Verificar si el tiempo de revelado ya culmino
            if ($roundReveal > 0 && $now >= $roundReveal) {
                if ($currentIndex >= $totalQuestions) {
                    // Fin de la partida
                    self::finalizeMatch($matchId);
                    $match = MatchGame::findById($matchId);
                } else {
                    // Pasar a la siguiente ronda
                    $nextIndex = $currentIndex + 1;
                    self::startRound($matchId, $nextIndex, $secondsPerQuestion);
                    $match = MatchGame::findById($matchId);
                    $roundStart = strtotime($match["round_start_time"]);
                    $roundEnd = strtotime($match["round_end_time"]);
                    $roundReveal = strtotime($match["round_reveal_time"]);
                    $currentIndex = $nextIndex;
                }
            }
        }

        // Obtener jugadores de la partida
        $players = MatchGame::getPlayers($matchId);

        // Si ya finalizo, devolver resumen final
        if ($match["status"] === "finished") {
            return [
                "status" => "finished",
                "match_id" => $matchId,
                "mode" => $match["mode"],
                "winner_user_id" => $match["winner_user_id"],
                "winner_team" => $match["winner_team"],
                "players" => $players,
            ];
        }

        // Obtener pregunta actual
        $questionData = MatchGame::getMatchQuestionByOrder($matchId, $currentIndex);
        if (!$questionData) {
            return ["error" => "Error al obtener la pregunta de la ronda."];
        }

        $matchQuestionId = (int)$questionData["match_question_id"];
        $answers = MatchGame::getAnswersForMatchQuestion($matchQuestionId);
        $answeredUserIds = array_column($answers, "user_id");

        // Si todos los jugadores ya respondieron antes de tiempo y aun estabamos en tiempo de pregunta, acelerar a revelado
        $allPlayersAnswered = (count($answeredUserIds) >= count($players));
        if ($allPlayersAnswered && $now < $roundEnd) {
            $nowStr = date("Y-m-d H:i:s", $now);
            $revealStr = date("Y-m-d H:i:s", $now + self::REVEAL_DURATION_SECONDS);
            MatchGame::updateRoundTimers($matchId, $currentIndex, $match["round_start_time"], $nowStr, $revealStr);
            $roundEnd = $now;
            $roundReveal = $now + self::REVEAL_DURATION_SECONDS;
        }

        $isRevealing = ($now >= $roundEnd);

        // Ocultar la respuesta correcta si aun no esta en fase de revelado
        $options = Question::getOptionsByQuestionId((int)$questionData["question_id"], !$isRevealing);

        // Verificar si el usuario actual ya respondio
        $myAnswer = null;
        foreach ($answers as $ans) {
            if ((int)$ans["user_id"] === $currentUserId) {
                $myAnswer = $ans;
                break;
            }
        }

        $remainingSeconds = max(0, $roundEnd - $now);
        $revealRemainingSeconds = max(0, $roundReveal - $now);

        // Mapear estado de respuesta de los demas jugadores
        $playerStatuses = [];
        foreach ($players as $p) {
            $pUid = (int)$p["user_id"];
            $hasAnswered = in_array($pUid, $answeredUserIds, true);
            $playerStatuses[] = [
                "user_id" => $pUid,
                "username" => $p["username"],
                "avatar" => $p["avatar"],
                "team" => $p["team"],
                "score" => (int)$p["score"],
                "has_answered" => $hasAnswered,
            ];
        }

        return [
            "status" => $isRevealing ? "showing_result" : "in_progress",
            "match_id" => $matchId,
            "mode" => $match["mode"],
            "current_question_index" => $currentIndex,
            "total_questions" => $totalQuestions,
            "remaining_seconds" => $remainingSeconds,
            "reveal_remaining_seconds" => $revealRemainingSeconds,
            "is_revealing" => $isRevealing,
            "has_answered" => ($myAnswer !== null),
            "my_answer" => $myAnswer,
            "question" => [
                "id" => (int)$questionData["question_id"],
                "match_question_id" => $matchQuestionId,
                "order" => $currentIndex,
                "text" => $questionData["question_text"],
                "category_name" => $questionData["category_name"],
                "category_icon" => $questionData["category_icon"],
                "difficulty" => $questionData["difficulty"],
                "explanation" => $isRevealing ? $questionData["explanation"] : null,
                "source_reference" => $isRevealing ? $questionData["source_reference"] : null,
                "options" => $options,
            ],
            "players" => $playerStatuses,
        ];
    }

    public static function submitAnswer(int $matchId, int $userId, int $matchQuestionId, ?int $selectedOptionId): array
    {
        $match = MatchGame::findById($matchId);
        if (!$match || $match["status"] === "finished") {
            return ["success" => false, "message" => "La partida no está disponible."];
        }

        $now = time();
        $roundEnd = !empty($match["round_end_time"]) ? strtotime($match["round_end_time"]) : 0;
        $roundStart = !empty($match["round_start_time"]) ? strtotime($match["round_start_time"]) : 0;

        // Comprobar que no haya expirado el tiempo de respuesta
        if ($now > $roundEnd + 1) { // 1 segundo de tolerancia por latencia de red
            return ["success" => false, "message" => "El tiempo para responder esta pregunta ha concluido."];
        }

        // Comprobar si ya respondio
        $existing = MatchGame::getPlayerAnswerForQuestion($matchQuestionId, $userId);
        if ($existing) {
            return ["success" => false, "message" => "Ya has enviado tu respuesta para esta pregunta."];
        }

        // Validar alternativa en la base de datos
        $isCorrect = false;
        if ($selectedOptionId !== null && $selectedOptionId > 0) {
            $stmt = Database::getConnection()->prepare(
                "SELECT is_correct FROM question_options WHERE id = :opt_id LIMIT 1"
            );
            $stmt->execute([":opt_id" => $selectedOptionId]);
            $isCorrect = (bool)$stmt->fetchColumn();
        }

        // Tiempo de respuesta
        $responseTime = max(0.2, (float)($now - $roundStart));

        // Calculo de puntuacion
        $points = 0;
        if ($isCorrect) {
            $timeLeft = max(0, $roundEnd - $now);
            $speedFraction = ($match["seconds_per_question"] > 0) ? ($timeLeft / (float)$match["seconds_per_question"]) : 0;
            $speedBonus = (int)round($speedFraction * App::MAX_SPEED_BONUS);
            $points = App::BASE_POINTS_PER_CORRECT + $speedBonus;
        }

        $recorded = MatchGame::recordAnswer(
            $matchId,
            $matchQuestionId,
            $userId,
            $selectedOptionId,
            $isCorrect,
            $points,
            $responseTime
        );

        if (!$recorded) {
            return ["success" => false, "message" => "Error al registrar la respuesta."];
        }

        return [
            "success" => true,
            "is_correct" => $isCorrect,
            "points" => $points,
            "response_time" => $responseTime,
        ];
    }

    public static function finalizeMatch(int $matchId): void
    {
        $match = MatchGame::findById($matchId);
        if (!$match || $match["status"] === "finished") {
            return;
        }

        $players = MatchGame::getPlayers($matchId);
        if (empty($players)) {
            MatchGame::finishMatch($matchId, null, null);
            return;
        }

        $db = Database::getConnection();
        $mode = $match["mode"];

        if ($mode === "1vs1") {
            self::finalize1vs1Match($matchId, $players);
        } elseif ($mode === "2vs2") {
            self::finalize2vs2Match($matchId, $players);
        } else {
            // Modo custom o individual
            self::finalizeCustomMatch($matchId, $players);
        }

        // Si provenia de una sala, actualizar la sala a terminada
        if (!empty($match["room_id"])) {
            GameRoom::updateStatus((int)$match["room_id"], "finished");
        }
    }

    private static function finalize1vs1Match(int $matchId, array $players): void
    {
        $p1 = $players[0];
        $p2 = $players[1] ?? null;

        $p1Score = (int)$p1["score"];
        $p2Score = $p2 ? (int)$p2["score"] : 0;

        $winnerUserId = null;
        $scoreA = 0.5;

        if ($p1Score > $p2Score) {
            $winnerUserId = (int)$p1["user_id"];
            $scoreA = 1.0;
        } elseif ($p2 && $p2Score > $p1Score) {
            $winnerUserId = (int)$p2["user_id"];
            $scoreA = 0.0;
        }

        // Calculo ELO
        $p1Rating = (int)$p1["rating_before"];
        $p2Rating = $p2 ? (int)$p2["rating_before"] : 1200;

        $eloResult = EloRatingService::calculate1vs1($p1Rating, $p2Rating, $scoreA);

        $db = Database::getConnection();

        // Actualizar jugador 1
        $p1Change = $eloResult["playerA"]["change"];
        $p1New = $eloResult["playerA"]["new"];
        $p1Won = ($winnerUserId === (int)$p1["user_id"]);
        $p1Tied = ($winnerUserId === null);
        $p1Xp = $p1Won ? App::BASE_XP_WIN : ($p1Tied ? App::BASE_XP_TIE : App::BASE_XP_LOSS);
        $p1Xp += ((int)$p1["correct_answers"] * 10);

        MatchGame::updatePlayerResult($matchId, (int)$p1["user_id"], $p1New, $p1Change, $p1Xp);
        self::updatePlayerPostGame((int)$p1["user_id"], "1vs1", $p1New, $p1Won, $p1Tied, (int)$p1["correct_answers"], $p1Score, $p1Xp, $matchId);

        // Actualizar jugador 2
        if ($p2) {
            $p2Change = $eloResult["playerB"]["change"];
            $p2New = $eloResult["playerB"]["new"];
            $p2Won = ($winnerUserId === (int)$p2["user_id"]);
            $p2Tied = ($winnerUserId === null);
            $p2Xp = $p2Won ? App::BASE_XP_WIN : ($p2Tied ? App::BASE_XP_TIE : App::BASE_XP_LOSS);
            $p2Xp += ((int)$p2["correct_answers"] * 10);

            MatchGame::updatePlayerResult($matchId, (int)$p2["user_id"], $p2New, $p2Change, $p2Xp);
            self::updatePlayerPostGame((int)$p2["user_id"], "1vs1", $p2New, $p2Won, $p2Tied, (int)$p2["correct_answers"], $p2Score, $p2Xp, $matchId);
        }

        MatchGame::finishMatch($matchId, $winnerUserId, null);
    }

    private static function finalize2vs2Match(int $matchId, array $players): void
    {
        $teamAScore = 0;
        $teamBScore = 0;
        $teamARatings = [];
        $teamBRatings = [];

        foreach ($players as $p) {
            if ($p["team"] === "A") {
                $teamAScore += (int)$p["score"];
                $teamARatings[(int)$p["user_id"]] = (int)$p["rating_before"];
            } else {
                $teamBScore += (int)$p["score"];
                $teamBRatings[(int)$p["user_id"]] = (int)$p["rating_before"];
            }
        }

        $winnerTeam = "tie";
        $scoreTeamA = 0.5;

        if ($teamAScore > $teamBScore) {
            $winnerTeam = "A";
            $scoreTeamA = 1.0;
        } elseif ($teamBScore > $teamAScore) {
            $winnerTeam = "B";
            $scoreTeamA = 0.0;
        }

        $eloResults = EloRatingService::calculate2vs2($teamARatings, $teamBRatings, $scoreTeamA);

        foreach ($players as $p) {
            $uid = (int)$p["user_id"];
            $tm = $p["team"];
            $calc = ($tm === "A") ? $eloResults["teamA"][$uid] : $eloResults["teamB"][$uid];
            $won = ($winnerTeam === $tm);
            $tied = ($winnerTeam === "tie");

            $xp = $won ? App::BASE_XP_WIN : ($tied ? App::BASE_XP_TIE : App::BASE_XP_LOSS);
            $xp += ((int)$p["correct_answers"] * 10);

            MatchGame::updatePlayerResult($matchId, $uid, $calc["new"], $calc["change"], $xp);
            self::updatePlayerPostGame($uid, "2vs2", $calc["new"], $won, $tied, (int)$p["correct_answers"], (int)$p["score"], $xp, $matchId);
        }

        MatchGame::finishMatch($matchId, null, $winnerTeam);
    }

    private static function finalizeCustomMatch(int $matchId, array $players): void
    {
        usort($players, fn($a, $b) => (int)$b["score"] <=> (int)$a["score"]);
        $highestScore = (int)$players[0]["score"];
        $winnerUserId = (int)$players[0]["user_id"];

        foreach ($players as $p) {
            $uid = (int)$p["user_id"];
            $won = ($winnerUserId === $uid);
            $xp = $won ? App::BASE_XP_WIN : App::BASE_XP_LOSS;
            $xp += ((int)$p["correct_answers"] * 10);

            MatchGame::updatePlayerResult($matchId, $uid, (int)$p["rating_before"], 0, $xp);
            self::updatePlayerPostGame($uid, "1vs1", (int)$p["rating_before"], $won, false, (int)$p["correct_answers"], (int)$p["score"], $xp, $matchId);
        }

        MatchGame::finishMatch($matchId, $winnerUserId, null);
    }

    private static function updatePlayerPostGame(
        int $userId,
        string $mode,
        int $newElo,
        bool $won,
        bool $tied,
        int $correctAnswers,
        int $points,
        int $xpGained,
        int $matchId
    ): void {
        $db = Database::getConnection();

        // 1. Actualizar player_ratings
        $stmt = $db->prepare(
            "UPDATE player_ratings
             SET elo_rating = :elo1,
                 peak_elo_rating = GREATEST(peak_elo_rating, :elo2)
             WHERE user_id = :uid AND mode = :md"
        );
        $stmt->execute([
            ":elo1" => $newElo,
            ":elo2" => $newElo,
            ":uid" => $userId,
            ":md" => $mode,
        ]);

        // 2. Actualizar estadisticas y total_points
        PlayerProfile::updateStatsAfterMatch($userId, $won, $tied, $correctAnswers, $points);

        // 3. Otorgar experiencia
        PlayerProfile::addExperience($userId, $xpGained, "Partida multijugador #" . $matchId, $matchId);

        // 4. Evaluar logros
        // Buscar menor tiempo de respuesta en la partida
        $timeStmt = $db->prepare(
            "SELECT MIN(response_time_seconds) FROM player_answers WHERE match_id = :mid AND user_id = :uid AND is_correct = 1"
        );
        $timeStmt->execute([":mid" => $matchId, ":uid" => $userId]);
        $minTime = (float)($timeStmt->fetchColumn() ?: 0.0);

        AchievementService::checkAfterMatch($userId, $won, $correctAnswers, $minTime);

        // 5. Restablecer estado a 'online'
        User::updateConnectionStatus($userId, "online");
    }
}
