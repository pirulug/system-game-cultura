<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        $user = Session::get("user");
        if (!$user) {
            if ($request->isAjax() || str_contains($request->getUri(), "/api/")) {
                Response::json(["success" => false, "message" => "No autenticado."], 401);
                return false;
            }

            Session::setFlash("error", "Debes iniciar sesión para acceder.");
            Response::redirect("/login");
            return false;
        }

        return true;
    }
}

