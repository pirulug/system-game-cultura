<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Category;
use App\Services\RankingService;

class RankingController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $tab = (string)$request->input("tab", "1vs1");
        $categoryId = (int)$request->input("category_id", 1);

        $categories = Category::getAll(true);

        $rankings = match ($tab) {
            "2vs2" => RankingService::getGlobalRanking("2vs2", 50),
            "weekly" => RankingService::getWeeklyRanking(30),
            "monthly" => RankingService::getMonthlyRanking(30),
            "category" => RankingService::getCategoryRanking($categoryId, 30),
            default => RankingService::getGlobalRanking("1vs1", 50),
        };

        $this->render("ranking/index", [
            "title" => "Tabla de Clasificación y Ranking - Cultura Perú",
            "tab" => $tab,
            "categoryId" => $categoryId,
            "categories" => $categories,
            "rankings" => $rankings,
        ]);
    }
}

