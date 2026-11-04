<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\User;

class AuthService
{
    public static function register(string $username, string $email, string $password, string $fullName): array
    {
        $username = trim($username);
        $email = trim(strtolower($email));
        $fullName = trim($fullName);

        if (strlen($username) < 3 || strlen($username) > 30) {
            return ["success" => false, "message" => "El nombre de usuario debe tener entre 3 y 30 caracteres."];
        }

        if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
            return ["success" => false, "message" => "El usuario solo puede contener letras, números y guiones bajos."];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "El correo electrónico no es válido."];
        }

        if (strlen($password) < 6) {
            return ["success" => false, "message" => "La contraseña debe tener al menos 6 caracteres."];
        }

        if (empty($fullName)) {
            return ["success" => false, "message" => "El nombre completo es obligatorio."];
        }

        if (User::findByUsername($username)) {
            return ["success" => false, "message" => "El nombre de usuario ya está en uso."];
        }

        if (User::findByEmail($email)) {
            return ["success" => false, "message" => "El correo electrónico ya está registrado."];
        }

        try {
            $userId = User::create($username, $email, $password, $fullName);
            $user = User::findById($userId);
            self::setUserSession($user);
            return ["success" => true, "user" => $user];
        } catch (\Throwable $e) {
            error_log("Error en registro: " . $e->getMessage());
            return ["success" => false, "message" => "Ocurrió un error al procesar el registro."];
        }
    }

    public static function login(string $usernameOrEmail, string $password): array
    {
        $identifier = trim($usernameOrEmail);

        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? User::findByEmail($identifier)
            : User::findByUsername($identifier);

        if (!$user) {
            return ["success" => false, "message" => "Credenciales incorrectas."];
        }

        if ($user["status"] !== "active") {
            return ["success" => false, "message" => "Tu cuenta se encuentra suspendida o deshabilitada."];
        }

        if (!password_verify($password, $user["password_hash"])) {
            return ["success" => false, "message" => "Credenciales incorrectas."];
        }

        $fullUser = User::findById((int)$user["id"]);
        self::setUserSession($fullUser);
        User::updateConnectionStatus((int)$user["id"], "online");

        return ["success" => true, "user" => $fullUser];
    }

    public static function logout(): void
    {
        $user = Session::get("user");
        if ($user) {
            User::updateConnectionStatus((int)$user["id"], "offline");
        }
        Session::destroy();
    }

    public static function setUserSession(array $user): void
    {
        Session::regenerate();
        Session::set("user", [
            "id" => (int)$user["id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => $user["role"],
            "full_name" => $user["full_name"] ?? $user["username"],
            "avatar" => $user["avatar"] ?? "default_avatar.png",
            "level" => (int)($user["level"] ?? 1),
            "total_points" => (int)($user["total_points"] ?? 0),
        ]);
    }

    public static function refreshUserSession(int $userId): void
    {
        $fresh = User::findById($userId);
        if ($fresh) {
            self::setUserSession($fresh);
        }
    }
}

