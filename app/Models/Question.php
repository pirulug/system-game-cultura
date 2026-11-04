<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Question extends Model
{
    public static function getRandomQuestionsForMatch(
        int $count,
        ?int $categoryId = null,
        ?string $difficulty = null,
        array $excludeQuestionIds = []
    ): array {
        $conditions = ["q.is_active = 1"];
        $params = [];

        if ($categoryId !== null && $categoryId > 0) {
            $conditions[] = "q.category_id = :cat_id";
            $params[":cat_id"] = $categoryId;
        }

        if ($difficulty !== null && $difficulty !== "" && $difficulty !== "all") {
            $conditions[] = "q.difficulty = :diff";
            $params[":diff"] = $difficulty;
        }

        if (!empty($excludeQuestionIds)) {
            // Generar placeholders para excluir preguntas recientes
            $placeholders = [];
            foreach ($excludeQuestionIds as $idx => $id) {
                $pName = ":ex_" . $idx;
                $placeholders[] = $pName;
                $params[$pName] = (int)$id;
            }
            $conditions[] = "q.id NOT IN (" . implode(",", $placeholders) . ")";
        }

        $whereClause = implode(" AND ", $conditions);

        $sql = "SELECT q.id, q.category_id, q.question_text, q.difficulty, q.explanation, q.source_reference,
                       c.name AS category_name, c.icon_class AS category_icon
                FROM questions q
                JOIN categories c ON c.id = q.category_id
                WHERE {$whereClause}
                ORDER BY RAND()
                LIMIT :limit";

        $stmt = self::db()->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(":limit", $count, PDO::PARAM_INT);
        $stmt->execute();

        $questions = $stmt->fetchAll();

        // Si no se alcanzaron suficientes preguntas debido a la exclusion, buscar sin exclusion
        if (count($questions) < $count && !empty($excludeQuestionIds)) {
            return self::getRandomQuestionsForMatch($count, $categoryId, $difficulty, []);
        }

        return $questions;
    }

    public static function getOptionsByQuestionId(int $questionId, bool $hideCorrect = false): array
    {
        $stmt = self::db()->prepare(
            "SELECT id, question_id, option_text, " . ($hideCorrect ? "0 AS is_correct" : "is_correct") . ", option_order
             FROM question_options
             WHERE question_id = :qid
             ORDER BY option_order ASC"
        );
        $stmt->execute([":qid" => $questionId]);
        return $stmt->fetchAll();
    }

    public static function findByIdWithOptions(int $id): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT q.*, c.name AS category_name
             FROM questions q
             JOIN categories c ON c.id = q.category_id
             WHERE q.id = :id LIMIT 1"
        );
        $stmt->execute([":id" => $id]);
        $question = $stmt->fetch();
        if (!$question) {
            return null;
        }

        $question["options"] = self::getOptionsByQuestionId($id, false);
        return $question;
    }

    public static function createWithOptions(
        int $categoryId,
        string $questionText,
        string $difficulty,
        string $explanation,
        ?string $sourceReference,
        array $options
    ): int {
        self::beginTransaction();
        try {
            $stmt = self::db()->prepare(
                "INSERT INTO questions (category_id, question_text, difficulty, explanation, source_reference, is_active)
                 VALUES (:cat, :txt, :diff, :exp, :src, 1)"
            );
            $stmt->execute([
                ":cat" => $categoryId,
                ":txt" => $questionText,
                ":diff" => $difficulty,
                ":exp" => $explanation,
                ":src" => $sourceReference,
            ]);
            $questionId = (int)self::db()->lastInsertId();

            $optStmt = self::db()->prepare(
                "INSERT INTO question_options (question_id, option_text, is_correct, option_order)
                 VALUES (:qid, :otxt, :corr, :ord)"
            );

            foreach ($options as $order => $opt) {
                $optStmt->execute([
                    ":qid" => $questionId,
                    ":otxt" => $opt["text"],
                    ":corr" => !empty($opt["is_correct"]) ? 1 : 0,
                    ":ord" => $order + 1,
                ]);
            }

            self::commit();
            return $questionId;
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }

    public static function updateWithOptions(
        int $id,
        int $categoryId,
        string $questionText,
        string $difficulty,
        string $explanation,
        ?string $sourceReference,
        bool $isActive,
        array $options
    ): bool {
        self::beginTransaction();
        try {
            $stmt = self::db()->prepare(
                "UPDATE questions
                 SET category_id = :cat, question_text = :txt, difficulty = :diff, explanation = :exp,
                     source_reference = :src, is_active = :act
                 WHERE id = :id"
            );
            $stmt->execute([
                ":cat" => $categoryId,
                ":txt" => $questionText,
                ":diff" => $difficulty,
                ":exp" => $explanation,
                ":src" => $sourceReference,
                ":act" => $isActive ? 1 : 0,
                ":id" => $id,
            ]);

            // Reemplazar alternativas
            $delOpts = self::db()->prepare("DELETE FROM question_options WHERE question_id = :qid");
            $delOpts->execute([":qid" => $id]);

            $optStmt = self::db()->prepare(
                "INSERT INTO question_options (question_id, option_text, is_correct, option_order)
                 VALUES (:qid, :otxt, :corr, :ord)"
            );

            foreach ($options as $order => $opt) {
                $optStmt->execute([
                    ":qid" => $id,
                    ":otxt" => $opt["text"],
                    ":corr" => !empty($opt["is_correct"]) ? 1 : 0,
                    ":ord" => $order + 1,
                ]);
            }

            self::commit();
            return true;
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM questions WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public static function toggleActive(int $id): bool
    {
        $stmt = self::db()->prepare("UPDATE questions SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public static function getAllPaginated(int $limit = 20, int $offset = 0, ?int $categoryId = null, ?string $search = null): array
    {
        $sql = "SELECT q.*, c.name AS category_name
                FROM questions q
                JOIN categories c ON c.id = q.category_id";
        $conditions = [];
        $params = [];

        if ($categoryId !== null && $categoryId > 0) {
            $conditions[] = "q.category_id = :cat_id";
            $params[":cat_id"] = $categoryId;
        }

        if ($search !== null && $search !== "") {
            $conditions[] = "(q.question_text LIKE :s1 OR q.explanation LIKE :s2)";
            $params[":s1"] = "%{$search}%";
            $params[":s2"] = "%{$search}%";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY q.id DESC LIMIT :limit OFFSET :offset";

        $stmt = self::db()->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function countAll(?int $categoryId = null, ?string $search = null): int
    {
        $sql = "SELECT COUNT(*) FROM questions q";
        $conditions = [];
        $params = [];

        if ($categoryId !== null && $categoryId > 0) {
            $conditions[] = "q.category_id = :cat_id";
            $params[":cat_id"] = $categoryId;
        }

        if ($search !== null && $search !== "") {
            $conditions[] = "(q.question_text LIKE :s1 OR q.explanation LIKE :s2)";
            $params[":s1"] = "%{$search}%";
            $params[":s2"] = "%{$search}%";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function getRecentQuestionIdsForPlayers(array $userIds, int $limit = 30): array
    {
        if (empty($userIds)) {
            return [];
        }

        $placeholders = implode(",", array_fill(0, count($userIds), "?"));
        $sql = "SELECT DISTINCT mq.question_id
                FROM player_answers pa
                JOIN match_questions mq ON mq.id = pa.match_question_id
                WHERE pa.user_id IN ({$placeholders})
                ORDER BY pa.id DESC
                LIMIT " . (int)$limit;

        $stmt = self::db()->prepare($sql);
        $stmt->execute($userIds);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
}

