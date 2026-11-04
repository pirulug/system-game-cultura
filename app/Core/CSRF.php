<?php
declare(strict_types=1);

namespace App\Core;

class CSRF
{
    private const SESSION_KEY = "_csrf_token";

    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        return $token;
    }

    public static function getToken(): string
    {
        $token = Session::get(self::SESSION_KEY);
        if (!$token) {
            $token = self::generateToken();
        }
        return $token;
    }

    public static function validate(?string $token): bool
    {
        $sessionToken = Session::get(self::SESSION_KEY);
        if (!$sessionToken || !$token) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function field(): string
    {
        $token = self::getToken();
        return "<input type=\"hidden\" name=\"csrf_token\" value=\"" . htmlspecialchars($token, ENT_QUOTES, "UTF-8") . "\">";
    }
}

