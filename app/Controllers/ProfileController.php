<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Achievement;
use App\Models\MatchGame;
use App\Models\PlayerProfile;
use App\Models\User;
use App\Services\AuthService;

class ProfileController extends Controller
{
    public function view(Request $request, array $params = []): void
    {
        $username = trim((string)($params["username"] ?? ""));
        $user = User::findByUsername($username);

        if (!$user) {
            Session::setFlash("error", "El jugador no existe.");
            $this->redirect("/ranking");
            return;
        }

        $userId = (int)$user["id"];
        $profile = PlayerProfile::getByUserId($userId);
        $matches = MatchGame::getPlayerHistory($userId, 10);
        $achievements = Achievement::getUserAchievements($userId);

        $isOwnProfile = ($this->getCurrentUserId() === $userId);

        $this->render("profile/view", [
            "title" => "Perfil de " . htmlspecialchars($user["username"]) . " - Cultura Perú",
            "user" => $user,
            "profile" => $profile,
            "matches" => $matches,
            "achievements" => $achievements,
            "isOwnProfile" => $isOwnProfile,
        ]);
    }

    public function editView(Request $request, array $params = []): void
    {
        $userId = $this->getCurrentUserId();
        $profile = PlayerProfile::getByUserId($userId);

        $this->render("profile/edit", [
            "title" => "Editar Mi Perfil - Cultura Perú",
            "profile" => $profile,
        ]);
    }

    public function update(Request $request, array $params = []): void
    {
        if (!$request->validateCSRF()) {
            Session::setFlash("error", "Token de seguridad inválido.");
            $this->redirect("/profile/edit");
            return;
        }

        $userId = $this->getCurrentUserId();
        $fullName = trim((string)$request->input("full_name", ""));
        $bio = trim((string)$request->input("bio", ""));
        $avatar = trim((string)$request->input("avatar", "default_avatar.png"));

        if (empty($fullName)) {
            Session::setFlash("error", "El nombre completo es obligatorio.");
            $this->redirect("/profile/edit");
            return;
        }

        User::updateProfile($userId, $fullName, $bio, $avatar);
        AuthService::refreshUserSession($userId);

        Session::setFlash("success", "Perfil actualizado con éxito.");
        $currentUser = $this->getCurrentUser();
        $this->redirect("/profile/" . $currentUser["username"]);
    }
}

