<?php
declare(strict_types=1);

require_once __DIR__ . "/app/bootstrap.php";

use App\Core\Request;

// Cargar rutas del sistema
$router = require_once __DIR__ . "/app/routes.php";

// Despachar peticion entrante
$request = new Request();
$router->dispatch($request);
