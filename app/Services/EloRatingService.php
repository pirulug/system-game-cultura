<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\App;

class EloRatingService
{
    public static function calculate1vs1(int $ratingA, int $ratingB, float $actualScoreA, int $kFactor = App::ELO_K_FACTOR): array
    {
        // $actualScoreA: 1.0 para victoria de A, 0.5 para empate, 0.0 para derrota de A
        $actualScoreB = 1.0 - $actualScoreA;

        // Puntuacion esperada
        $expectedA = 1.0 / (1.0 + pow(10, ($ratingB - $ratingA) / 400.0));
        $expectedB = 1.0 / (1.0 + pow(10, ($ratingA - $ratingB) / 400.0));

        // Nuevas puntuaciones
        $changeA = (int)round($kFactor * ($actualScoreA - $expectedA));
        $changeB = (int)round($kFactor * ($actualScoreB - $expectedB));

        // Evitar que el rating caiga por debajo de 500
        $newRatingA = max(500, $ratingA + $changeA);
        $newRatingB = max(500, $ratingB + $changeB);

        return [
            "playerA" => [
                "old" => $ratingA,
                "new" => $newRatingA,
                "change" => $changeA,
            ],
            "playerB" => [
                "old" => $ratingB,
                "new" => $newRatingB,
                "change" => $changeB,
            ],
        ];
    }

    public static function calculate2vs2(array $teamARatings, array $teamBRatings, float $actualScoreTeamA, int $kFactor = App::ELO_K_FACTOR): array
    {
        // Promedios de ELO por equipo
        $avgA = (int)round(array_sum($teamARatings) / count($teamARatings));
        $avgB = (int)round(array_sum($teamBRatings) / count($teamBRatings));

        $actualScoreTeamB = 1.0 - $actualScoreTeamA;

        $expectedA = 1.0 / (1.0 + pow(10, ($avgB - $avgA) / 400.0));
        $expectedB = 1.0 / (1.0 + pow(10, ($avgA - $avgB) / 400.0));

        $teamChangeA = (int)round($kFactor * ($actualScoreTeamA - $expectedA));
        $teamChangeB = (int)round($kFactor * ($actualScoreTeamB - $expectedB));

        $resultsA = [];
        foreach ($teamARatings as $uid => $r) {
            $newR = max(500, $r + $teamChangeA);
            $resultsA[$uid] = ["old" => $r, "new" => $newR, "change" => $teamChangeA];
        }

        $resultsB = [];
        foreach ($teamBRatings as $uid => $r) {
            $newR = max(500, $r + $teamChangeB);
            $resultsB[$uid] = ["old" => $r, "new" => $newR, "change" => $teamChangeB];
        }

        return [
            "teamA" => $resultsA,
            "teamB" => $resultsB,
        ];
    }
}

