<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\AdminLog;
use App\Models\Category;
use App\Models\MatchGame;
use App\Models\Question;
use App\Models\User;
use App\Services\AdminService;
use App\Services\RankingService;

class AdminController extends Controller
{
    public function dashboard(Request $request, array $params = []): void
    {
        $metrics = AdminService::getDashboardMetrics();
        $recentLogs = AdminLog::getRecent(10);
        $recentMatches = MatchGame::getRecentMatches(5);

        $this->render("admin/dashboard", [
            "title" => "Panel Administrativo - Cultura Perú",
            "metrics" => $metrics,
            "recentLogs" => $recentLogs,
            "recentMatches" => $recentMatches,
        ], "admin");
    }

    public function questions(Request $request, array $params = []): void
    {
        $categoryId = (int)$request->input("category_id", 0);
        $search = trim((string)$request->input("q", ""));
        $page = max(1, (int)$request->input("page", 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $questions = Question::getAllPaginated($limit, $offset, $categoryId > 0 ? $categoryId : null, $search);
        $totalQuestions = Question::countAll($categoryId > 0 ? $categoryId : null, $search);
        $totalPages = (int)ceil($totalQuestions / $limit);

        $categories = Category::getAll(false);

        $this->render("admin/questions", [
            "title" => "Gestión de Preguntas - Admin",
            "questions" => $questions,
            "totalQuestions" => $totalQuestions,
            "totalPages" => $totalPages,
            "currentPage" => $page,
            "categoryId" => $categoryId,
            "search" => $search,
            "categories" => $categories,
        ], "admin");
    }

    public function questionCreate(Request $request, array $params = []): void
    {
        $categories = Category::getAll(true);

        $this->render("admin/questions_form", [
            "title" => "Nueva Pregunta - Admin",
            "categories" => $categories,
            "question" => null,
        ], "admin");
    }

    public function questionStore(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token CSRF inválido.");
            $this->redirect("/admin/questions/create");
            return;
        }

        $adminId = $this->getCurrentUserId();
        $categoryId = (int)$request->input("category_id", 1);
        $questionText = trim((string)$request->input("question_text", ""));
        $difficulty = (string)$request->input("difficulty", "intermedio");
        $explanation = trim((string)$request->input("explanation", ""));
        $sourceReference = trim((string)$request->input("source_reference", ""));

        $opt1 = trim((string)$request->input("option_1", ""));
        $opt2 = trim((string)$request->input("option_2", ""));
        $opt3 = trim((string)$request->input("option_3", ""));
        $opt4 = trim((string)$request->input("option_4", ""));
        $correctIndex = (int)$request->input("correct_option", 1);

        if (empty($questionText) || empty($opt1) || empty($opt2) || empty($opt3) || empty($opt4)) {
            Session::setFlash("error", "Por favor completa la pregunta y las 4 alternativas.");
            $this->redirect("/admin/questions/create");
            return;
        }

        $options = [
            ["text" => $opt1, "is_correct" => ($correctIndex === 1)],
            ["text" => $opt2, "is_correct" => ($correctIndex === 2)],
            ["text" => $opt3, "is_correct" => ($correctIndex === 3)],
            ["text" => $opt4, "is_correct" => ($correctIndex === 4)],
        ];

        $qid = Question::createWithOptions($categoryId, $questionText, $difficulty, $explanation, $sourceReference, $options);
        AdminLog::log($adminId, "create_question", "questions", $qid, "Pregunta creada: " . substr($questionText, 0, 50));

        Session::setFlash("success", "Pregunta creada exitosamente.");
        $this->redirect("/admin/questions");
    }

    public function questionEdit(Request $request, array $params = []): void
    {
        $id = (int)($params["id"] ?? 0);
        $question = Question::findByIdWithOptions($id);

        if (!$question) {
            Session::setFlash("error", "Pregunta no encontrada.");
            $this->redirect("/admin/questions");
            return;
        }

        $categories = Category::getAll(false);

        $this->render("admin/questions_form", [
            "title" => "Editar Pregunta #" . $id . " - Admin",
            "categories" => $categories,
            "question" => $question,
        ], "admin");
    }

    public function questionUpdate(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token CSRF inválido.");
            $this->redirect("/admin/questions");
            return;
        }

        $id = (int)($params["id"] ?? 0);
        $adminId = $this->getCurrentUserId();
        $categoryId = (int)$request->input("category_id", 1);
        $questionText = trim((string)$request->input("question_text", ""));
        $difficulty = (string)$request->input("difficulty", "intermedio");
        $explanation = trim((string)$request->input("explanation", ""));
        $sourceReference = trim((string)$request->input("source_reference", ""));
        $isActive = (bool)$request->input("is_active", true);

        $opt1 = trim((string)$request->input("option_1", ""));
        $opt2 = trim((string)$request->input("option_2", ""));
        $opt3 = trim((string)$request->input("option_3", ""));
        $opt4 = trim((string)$request->input("option_4", ""));
        $correctIndex = (int)$request->input("correct_option", 1);

        $options = [
            ["text" => $opt1, "is_correct" => ($correctIndex === 1)],
            ["text" => $opt2, "is_correct" => ($correctIndex === 2)],
            ["text" => $opt3, "is_correct" => ($correctIndex === 3)],
            ["text" => $opt4, "is_correct" => ($correctIndex === 4)],
        ];

        Question::updateWithOptions($id, $categoryId, $questionText, $difficulty, $explanation, $sourceReference, $isActive, $options);
        AdminLog::log($adminId, "update_question", "questions", $id, "Pregunta actualizada.");

        Session::setFlash("success", "Pregunta actualizada correctamente.");
        $this->redirect("/admin/questions");
    }

    public function questionDelete(Request $request, array $params = []): void
    {
        $id = (int)($params["id"] ?? 0);
        $adminId = $this->getCurrentUserId();

        Question::delete($id);
        AdminLog::log($adminId, "delete_question", "questions", $id, "Pregunta eliminada.");

        Session::setFlash("success", "Pregunta eliminada correctamente.");
        $this->redirect("/admin/questions");
    }

    public function questionsImportView(Request $request, array $params = []): void
    {
        $this->render("admin/questions_import", [
            "title" => "Importar Preguntas vía CSV - Admin",
        ], "admin");
    }

    public function questionsImportSubmit(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token CSRF inválido.");
            $this->redirect("/admin/questions/import");
            return;
        }

        if (empty($_FILES["csv_file"]["tmp_name"])) {
            Session::setFlash("error", "Debes seleccionar un archivo CSV válido.");
            $this->redirect("/admin/questions/import");
            return;
        }

        $adminId = $this->getCurrentUserId();
        $tmpFile = $_FILES["csv_file"]["tmp_name"];

        $result = AdminService::importQuestionsFromCsv($tmpFile, $adminId);

        if (!$result["success"]) {
            Session::setFlash("error", $result["message"]);
            $this->redirect("/admin/questions/import");
            return;
        }

        Session::setFlash("success", "Importación completada: " . $result["imported_count"] . " preguntas agregadas con éxito.");
        $this->redirect("/admin/questions");
    }

    public function categories(Request $request, array $params = []): void
    {
        $categories = Category::getAll(false);

        $this->render("admin/categories", [
            "title" => "Gestión de Categorías - Admin",
            "categories" => $categories,
        ], "admin");
    }

    public function categoryStore(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token inválido.");
            $this->redirect("/admin/categories");
            return;
        }

        $name = trim((string)$request->input("name", ""));
        $slug = trim((string)$request->input("slug", ""));
        $description = trim((string)$request->input("description", ""));
        $iconClass = trim((string)$request->input("icon_class", "bi-book"));
        $order = (int)$request->input("display_order", 0);

        if (empty($name)) {
            Session::setFlash("error", "El nombre de la categoría es requerido.");
            $this->redirect("/admin/categories");
            return;
        }

        if (empty($slug)) {
            $slug = strtolower(preg_replace("/[^A-Za-z0-9-]+/", "-", $name));
        }

        Category::create($name, $slug, $description, $iconClass, $order);
        AdminLog::log($this->getCurrentUserId(), "create_category", "categories", null, "Categoría creada: " . $name);

        Session::setFlash("success", "Categoría creada con éxito.");
        $this->redirect("/admin/categories");
    }

    public function categoryToggle(Request $request, array $params = []): void
    {
        $id = (int)($params["id"] ?? 0);
        Category::toggleActive($id);
        AdminLog::log($this->getCurrentUserId(), "toggle_category", "categories", $id, "Estado de categoría conmutado.");

        Session::setFlash("success", "Estado de la categoría actualizado.");
        $this->redirect("/admin/categories");
    }

    public function users(Request $request, array $params = []): void
    {
        $users = User::getAllWithStats(100, 0);

        $this->render("admin/users", [
            "title" => "Gestión de Usuarios - Admin",
            "users" => $users,
        ], "admin");
    }

    public function userToggleStatus(Request $request, array $params = []): void
    {
        $id = (int)($params["id"] ?? 0);
        $adminId = $this->getCurrentUserId();

        $result = AdminService::toggleUserStatus($id, $adminId);

        if (!$result["success"]) {
            Session::setFlash("error", $result["message"]);
        } else {
            Session::setFlash("success", "Estado del usuario actualizado a: " . $result["new_status"]);
        }

        $this->redirect("/admin/users");
    }

    public function matches(Request $request, array $params = []): void
    {
        $matches = MatchGame::getRecentMatches(50);

        $this->render("admin/matches", [
            "title" => "Historial de Partidas - Admin",
            "matches" => $matches,
        ], "admin");
    }

    public function rankings(Request $request, array $params = []): void
    {
        $rankings = RankingService::getGlobalRanking("1vs1", 50);

        $this->render("admin/rankings", [
            "title" => "Gestión de Rankings y Temporadas - Admin",
            "rankings" => $rankings,
        ], "admin");
    }

    public function seasonReset(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token inválido.");
            $this->redirect("/admin/rankings");
            return;
        }

        $seasonName = trim((string)$request->input("season_name", "Temporada Bicentenario"));
        AdminService::resetSeason($this->getCurrentUserId(), $seasonName);

        Session::setFlash("success", "Temporada reiniciada y archivada correctamente.");
        $this->redirect("/admin/rankings");
    }
}

