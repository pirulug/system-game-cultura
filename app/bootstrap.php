<?php
declare(strict_types=1);

date_default_timezone_set("America/Lima");
error_reporting(E_ALL);
ini_set("display_errors", "1");

// Autoloader nativo compatible con PSR-4
spl_autoload_register(function (string $class): void {
    $prefix = "App\\";
    $baseDir = __DIR__ . "/";

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace("\\", "/", $relativeClass) . ".php";

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Session;

Session::start();

