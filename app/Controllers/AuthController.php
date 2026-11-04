<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function loginView(Request $request, array $params = []): void
    {
        $this->render("auth/login", [
            "title" => "Iniciar Sesión - Cultura Perú",
        ]);
    }

    public function loginSubmit(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token de seguridad inválido. Recarga la página e intenta nuevamente.");
            $this->redirect("/login");
            return;
        }

        $identifier = (string)$request->input("identifier", "");
        $password = (string)$request->input("password", "");

        if (empty($identifier) || empty($password)) {
            Session::setFlash("error", "Por favor completa todos los campos.");
            $this->redirect("/login");
            return;
        }

        $result = AuthService::login($identifier, $password);

        if (!$result["success"]) {
            Session::setFlash("error", $result["message"]);
            $this->redirect("/login");
            return;
        }

        Session::setFlash("success", "Bienvenido de vuelta, " . htmlspecialchars($result["user"]["username"]) . ".");
        $this->redirect("/dashboard");
    }

    public function registerView(Request $request, array $params = []): void
    {
        $this->render("auth/register", [
            "title" => "Crear Cuenta - Cultura Perú",
        ]);
    }

    public function registerSubmit(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token de seguridad inválido. Recarga la página e intenta nuevamente.");
            $this->redirect("/register");
            return;
        }

        $username = (string)$request->input("username", "");
        $email = (string)$request->input("email", "");
        $password = (string)$request->input("password", "");
        $passwordConfirm = (string)$request->input("password_confirm", "");
        $fullName = (string)$request->input("full_name", "");

        if ($password !== $passwordConfirm) {
            Session::setFlash("error", "Las contraseñas no coinciden.");
            $this->redirect("/register");
            return;
        }

        $result = AuthService::register($username, $email, $password, $fullName);

        if (!$result["success"]) {
            Session::setFlash("error", $result["message"]);
            $this->redirect("/register");
            return;
        }

        Session::setFlash("success", "¡Cuenta creada exitosamente! Bienvenido a Cultura Perú.");
        $this->redirect("/dashboard");
    }

    public function logout(Request $request, array $params = []): void
    {
        AuthService::logout();
        $this->redirect("/login");
    }
}

