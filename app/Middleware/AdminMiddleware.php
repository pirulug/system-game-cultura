<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AdminMiddleware
{
    public function handle(Request $request): bool
    {
        $user = Session::get("user");
        if (!$user) {
            Session::setFlash("error", "Acceso restringido para administradores.");
            Response::redirect("/login");
            return false;
        }

        if ($user["role"] !== "admin" && $user["role"] !== "moderator") {
            Response::forbidden("Acceso reservado exclusivamente para la administración.");
            return false;
        }

        return true;
    }
}

