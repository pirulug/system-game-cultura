<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\App;

abstract class Controller
{
    protected function render(string $viewPath, array $data = [], string $layout = "main"): void
    {
        Response::setSecurityHeaders();

        // Extraer datos a variables locales
        extract($data);

        // Variables globales para vistas
        $baseUrl = App::getBaseUrl();
        $appName = App::APP_NAME;
        $currentUser = Session::get("user");
        $csrfToken = CSRF::getToken();
        $flashSuccess = Session::getFlash("success");
        $flashError = Session::getFlash("error");
        $flashInfo = Session::getFlash("info");

        // Cargar vista a buffer
        $viewFile = __DIR__ . "/../Views/" . $viewPath . ".php";
        if (!file_exists($viewFile)) {
            die("Vista no encontrada: " . htmlspecialchars($viewPath));
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Cargar layout si corresponde
        if ($layout === "none") {
            echo $content;
            return;
        }

        $layoutFile = __DIR__ . "/../Views/layouts/" . $layout . ".php";
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }

    protected function redirect(string $path): void
    {
        Response::redirect($path);
    }

    protected function getCurrentUser(): ?array
    {
        return Session::get("user");
    }

    protected function getCurrentUserId(): ?int
    {
        $user = Session::get("user");
        return $user ? (int)$user["id"] : null;
    }
}

