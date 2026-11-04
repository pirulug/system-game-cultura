<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\App;

class Response
{
    public static function setSecurityHeaders(): void
    {
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: SAMEORIGIN");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
    }

    public static function json(array $data, int $statusCode = 200): void
    {
        self::setSecurityHeaders();
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function redirect(string $path): void
    {
        $baseUrl = App::getBaseUrl();
        $target = str_starts_with($path, "http") ? $path : $baseUrl . "/" . ltrim($path, "/");
        header("Location: " . $target);
        exit;
    }

    public static function notFound(string $message = "Página no encontrada"): void
    {
        self::setSecurityHeaders();
        http_response_code(404);
        echo "<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>404 No Encontrado</title><link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css\"></head><body class=\"bg-dark text-white d-flex align-items-center justify-content-center vh-100\"><div class=\"text-center\"><h1 class=\"display-1 fw-bold text-danger\">404</h1><h2 class=\"mb-3\">{$message}</h2><p class=\"text-secondary\">El recurso solicitado no existe o ha sido movido.</p><a href=\"" . App::getBaseUrl() . "/\" class=\"btn btn-outline-light mt-3\">Regresar al Inicio</a></div></body></html>";
        exit;
    }

    public static function forbidden(string $message = "Acceso Denegado"): void
    {
        self::setSecurityHeaders();
        http_response_code(403);
        echo "<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>403 Prohibido</title><link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css\"></head><body class=\"bg-dark text-white d-flex align-items-center justify-content-center vh-100\"><div class=\"text-center\"><h1 class=\"display-1 fw-bold text-warning\">403</h1><h2 class=\"mb-3\">{$message}</h2><p class=\"text-secondary\">No tienes los privilegios necesarios para esta acción.</p><a href=\"" . App::getBaseUrl() . "/\" class=\"btn btn-outline-light mt-3\">Regresar al Inicio</a></div></body></html>";
        exit;
    }
}

