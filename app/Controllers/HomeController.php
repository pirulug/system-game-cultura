<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use App\Services\RankingService;

class HomeController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $categories = Category::getAll(true);
        $topPlayers = RankingService::getGlobalRanking("1vs1", 5);
        $totalUsers = User::countAll();
        $totalQuestions = Question::countAll();

        $this->render("home/index", [
            "title" => "Cultura Perú - Plataforma Multijugador de Conocimiento",
            "categories" => $categories,
            "topPlayers" => $topPlayers,
            "totalUsers" => $totalUsers,
            "totalQuestions" => $totalQuestions,
        ]);
    }
}

