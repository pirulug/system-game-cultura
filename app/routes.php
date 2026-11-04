<?php
declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\ApiGameController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\FriendsController;
use App\Controllers\GameController;
use App\Controllers\HomeController;
use App\Controllers\MatchmakingController;
use App\Controllers\ProfileController;
use App\Controllers\RankingController;
use App\Controllers\RoomController;
use App\Core\Router;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$router = new Router();

// Rutas publicas
$router->get("/", [HomeController::class, "index"]);
$router->get("/ranking", [RankingController::class, "index"]);
$router->get("/profile/{username}", [ProfileController::class, "view"]);

// Rutas de invitados (Login / Registro)
$router->get("/login", [AuthController::class, "loginView"], [GuestMiddleware::class]);
$router->post("/login", [AuthController::class, "loginSubmit"], [GuestMiddleware::class]);
$router->get("/register", [AuthController::class, "registerView"], [GuestMiddleware::class]);
$router->post("/register", [AuthController::class, "registerSubmit"], [GuestMiddleware::class]);

// Rutas protegidas de autenticacion
$router->post("/logout", [AuthController::class, "logout"], [AuthMiddleware::class]);
$router->get("/dashboard", [DashboardController::class, "index"], [AuthMiddleware::class]);

// Modalidades y Arena de juego
$router->get("/game/modes", [GameController::class, "modes"], [AuthMiddleware::class]);
$router->get("/game/match/{id}", [GameController::class, "arena"], [AuthMiddleware::class]);
$router->get("/game/match/{id}/results", [GameController::class, "results"], [AuthMiddleware::class]);

// Salas personalizadas
$router->get("/game/room/create", [RoomController::class, "create"], [AuthMiddleware::class]);
$router->post("/game/room/create", [RoomController::class, "store"], [AuthMiddleware::class]);
$router->get("/game/room/{code}", [RoomController::class, "lobby"], [AuthMiddleware::class]);
$router->post("/game/room/join", [RoomController::class, "joinByCode"], [AuthMiddleware::class]);
$router->post("/api/room/ready", [RoomController::class, "toggleReady"], [AuthMiddleware::class]);
$router->post("/api/room/team", [RoomController::class, "changeTeam"], [AuthMiddleware::class]);
$router->post("/api/room/kick", [RoomController::class, "kick"], [AuthMiddleware::class]);
$router->post("/api/room/start", [RoomController::class, "start"], [AuthMiddleware::class]);

// Matchmaking
$router->get("/game/matchmaking", [MatchmakingController::class, "searchView"], [AuthMiddleware::class]);
$router->post("/api/matchmaking/join", [MatchmakingController::class, "join"], [AuthMiddleware::class]);
$router->get("/api/matchmaking/status", [MatchmakingController::class, "status"], [AuthMiddleware::class]);
$router->post("/api/matchmaking/cancel", [MatchmakingController::class, "cancel"], [AuthMiddleware::class]);

// API en tiempo real del juego
$router->get("/api/match/{id}/status", [ApiGameController::class, "matchStatus"], [AuthMiddleware::class]);
$router->post("/api/match/{id}/answer", [ApiGameController::class, "submitAnswer"], [AuthMiddleware::class]);
$router->get("/api/room/{code}/status", [ApiGameController::class, "roomStatus"], [AuthMiddleware::class]);

// Perfil de usuario
$router->get("/profile/edit", [ProfileController::class, "editView"], [AuthMiddleware::class]);
$router->post("/profile/edit", [ProfileController::class, "update"], [AuthMiddleware::class]);

// Amigos y desafios
$router->get("/friends", [FriendsController::class, "index"], [AuthMiddleware::class]);
$router->post("/api/friends/request", [FriendsController::class, "sendRequest"], [AuthMiddleware::class]);
$router->post("/api/friends/respond", [FriendsController::class, "respond"], [AuthMiddleware::class]);
$router->post("/api/friends/remove", [FriendsController::class, "remove"], [AuthMiddleware::class]);

// Panel Administrativo
$router->get("/admin", [AdminController::class, "dashboard"], [AdminMiddleware::class]);
$router->get("/admin/questions", [AdminController::class, "questions"], [AdminMiddleware::class]);
$router->get("/admin/questions/create", [AdminController::class, "questionCreate"], [AdminMiddleware::class]);
$router->post("/admin/questions/create", [AdminController::class, "questionStore"], [AdminMiddleware::class]);
$router->get("/admin/questions/{id}/edit", [AdminController::class, "questionEdit"], [AdminMiddleware::class]);
$router->post("/admin/questions/{id}/edit", [AdminController::class, "questionUpdate"], [AdminMiddleware::class]);
$router->post("/admin/questions/{id}/delete", [AdminController::class, "questionDelete"], [AdminMiddleware::class]);
$router->get("/admin/questions/import", [AdminController::class, "questionsImportView"], [AdminMiddleware::class]);
$router->post("/admin/questions/import", [AdminController::class, "questionsImportSubmit"], [AdminMiddleware::class]);

$router->get("/admin/categories", [AdminController::class, "categories"], [AdminMiddleware::class]);
$router->post("/admin/categories/create", [AdminController::class, "categoryStore"], [AdminMiddleware::class]);
$router->post("/admin/categories/{id}/toggle", [AdminController::class, "categoryToggle"], [AdminMiddleware::class]);

$router->get("/admin/users", [AdminController::class, "users"], [AdminMiddleware::class]);
$router->post("/admin/users/{id}/toggle-status", [AdminController::class, "userToggleStatus"], [AdminMiddleware::class]);

$router->get("/admin/matches", [AdminController::class, "matches"], [AdminMiddleware::class]);
$router->get("/admin/rankings", [AdminController::class, "rankings"], [AdminMiddleware::class]);
$router->post("/admin/season/reset", [AdminController::class, "seasonReset"], [AdminMiddleware::class]);

return $router;

