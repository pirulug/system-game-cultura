<?php
declare(strict_types=1);

namespace App\Config;

class App
{
    public const APP_NAME = "Cultura Perú - Multijugador";
    public const APP_VERSION = "1.0.0";
    public const TIMEZONE = "America/Lima";
    public const SESSION_LIFETIME = 86400;

    // Parametros del juego
    public const DEFAULT_QUESTIONS_COUNT = 10;
    public const DEFAULT_SECONDS_PER_QUESTION = 15;
    public const BASE_POINTS_PER_CORRECT = 100;
    public const MAX_SPEED_BONUS = 50;
    public const BASE_XP_WIN = 150;
    public const BASE_XP_LOSS = 50;
    public const BASE_XP_TIE = 80;

    // Algoritmo ELO
    public const ELO_K_FACTOR = 32;
    public const ELO_DEFAULT = 1200;

    public static function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"] ?? "localhost";
        
        $scriptName = $_SERVER["SCRIPT_NAME"] ?? "";
        $scriptDir = dirname($scriptName);
        $scriptDir = str_replace("\\", "/", $scriptDir);
        $scriptDir = trim($scriptDir, "/.");

        return $protocol . "://" . $host . ($scriptDir !== "" ? "/" . $scriptDir : "");
    }
}
