<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use App\Models\AdminLog;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use PDO;

class AdminService
{
    public static function getDashboardMetrics(): array
    {
        $db = Database::getConnection();

        $totalUsers = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $onlineUsers = (int)$db->query("SELECT COUNT(*) FROM player_profiles WHERE connection_status != 'offline'")->fetchColumn();
        $totalMatches = (int)$db->query("SELECT COUNT(*) FROM matches WHERE status = 'finished'")->fetchColumn();
        $activeMatches = (int)$db->query("SELECT COUNT(*) FROM matches WHERE status = 'in_progress'")->fetchColumn();
        $totalQuestions = (int)$db->query("SELECT COUNT(*) FROM questions")->fetchColumn();
        $totalCategories = (int)$db->query("SELECT COUNT(*) FROM categories WHERE is_active = 1")->fetchColumn();

        return [
            "total_users" => $totalUsers,
            "online_users" => $onlineUsers,
            "total_matches" => $totalMatches,
            "active_matches" => $activeMatches,
            "total_questions" => $totalQuestions,
            "total_categories" => $totalCategories,
        ];
    }

    public static function importQuestionsFromCsv(string $filePath, int $adminUserId): array
    {
        if (!file_exists($filePath)) {
            return ["success" => false, "message" => "Archivo CSV no encontrado."];
        }

        $handle = fopen($filePath, "r");
        if ($handle === false) {
            return ["success" => false, "message" => "No se pudo abrir el archivo CSV."];
        }

        // Leer cabecera
        $header = fgetcsv($handle, 2000, ",");
        $importedCount = 0;
        $errorCount = 0;

        while (($row = fgetcsv($handle, 2000, ",")) !== false) {
            if (count($row) < 9) {
                $errorCount++;
                continue;
            }

            // Columnas esperadas:
            // 0: category_id, 1: question_text, 2: difficulty, 3: explanation, 4: source,
            // 5: opt1, 6: opt2, 7: opt3, 8: opt4, 9: correct_index (1 a 4)
            $catId = (int)$row[0];
            $text = trim($row[1]);
            $difficulty = strtolower(trim($row[2]));
            $explanation = trim($row[3]);
            $source = !empty($row[4]) ? trim($row[4]) : "Referencia General";
            $opt1 = trim($row[5]);
            $opt2 = trim($row[6]);
            $opt3 = trim($row[7]);
            $opt4 = trim($row[8]);
            $correctIndex = isset($row[9]) ? (int)$row[9] : 1;

            if (empty($text) || empty($opt1) || empty($opt2)) {
                $errorCount++;
                continue;
            }

            $options = [
                ["text" => $opt1, "is_correct" => ($correctIndex === 1)],
                ["text" => $opt2, "is_correct" => ($correctIndex === 2)],
                ["text" => $opt3, "is_correct" => ($correctIndex === 3)],
                ["text" => $opt4, "is_correct" => ($correctIndex === 4)],
            ];

            try {
                Question::createWithOptions($catId, $text, $difficulty, $explanation, $source, $options);
                $importedCount++;
            } catch (\Throwable $e) {
                $errorCount++;
            }
        }

        fclose($handle);

        AdminLog::log($adminUserId, "import_questions_csv", "questions", null, "Importadas {$importedCount} preguntas, {$errorCount} errores.");

        return [
            "success" => true,
            "imported_count" => $importedCount,
            "error_count" => $errorCount,
        ];
    }

    public static function toggleUserStatus(int $userId, int $adminUserId): array
    {
        $user = User::findById($userId);
        if (!$user) {
            return ["success" => false, "message" => "Usuario no encontrado."];
        }

        if ($user["role"] === "admin") {
            return ["success" => false, "message" => "No se puede suspender una cuenta de administrador."];
        }

        $newStatus = ($user["status"] === "active") ? "suspended" : "active";
        User::updateStatus($userId, $newStatus);

        AdminLog::log($adminUserId, "toggle_user_status", "users", $userId, "Estado cambiado a {$newStatus}");

        return ["success" => true, "new_status" => $newStatus];
    }

    public static function resetSeason(int $adminUserId, string $seasonName): void
    {
        $db = Database::getConnection();

        // 1. Guardar temporada anterior
        $stmt = $db->prepare(
            "INSERT INTO seasons (name, start_date, end_date, is_active)
             VALUES (:nm, DATE_SUB(CURDATE(), INTERVAL 90 DAY), CURDATE(), 0)"
        );
        $stmt->execute([":nm" => $seasonName]);

        // 2. Reiniciar ELO suave hacia 1200: nuevo = 1200 + round((actual - 1200) * 0.5)
        $db->exec(
            "UPDATE player_ratings
             SET elo_rating = 1200 + ROUND((elo_rating - 1200) * 0.50)"
        );

        AdminLog::log($adminUserId, "reset_season", "seasons", null, "Temporada reiniciada: {$seasonName}");
    }
}

