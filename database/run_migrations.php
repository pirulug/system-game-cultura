<?php
declare(strict_types=1);

$host = "127.0.0.1";
$port = 3306;
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Conectado a MySQL con exito.\n";

    $targetDbs = ["pre", "cultura_peru"];
    foreach ($targetDbs as $dbName) {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");

        $files = [
            __DIR__ . "/schema.sql",
            __DIR__ . "/seed_categories.sql",
            __DIR__ . "/seed_achievements.sql",
            __DIR__ . "/seed_questions.sql",
        ];

        foreach ($files as $file) {
            if (!file_exists($file)) {
                echo "Error: Archivo no encontrado: {$file}\n";
                continue;
            }

            echo "Ejecutando en {$dbName}: " . basename($file) . "...\n";
            $sql = file_get_contents($file);
            $pdo->exec($sql);
        }

        // Crear usuario administrador por defecto
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([":u" => "admin"]);
        $admin = $stmt->fetch();

        if (!$admin) {
            $hash = password_hash("Admin123*", PASSWORD_BCRYPT);
            $insert = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (:u, :e, :p, 'admin', 'active')");
            $insert->execute([
                ":u" => "admin",
                ":e" => "admin@culturaperu.pe",
                ":p" => $hash,
            ]);
            $adminId = $pdo->lastInsertId();

            $profile = $pdo->prepare("INSERT INTO player_profiles (user_id, full_name, avatar, level, experience_points, total_points) VALUES (:uid, 'Administrador General', 'admin_avatar.png', 10, 5000, 2500)");
            $profile->execute([":uid" => $adminId]);

            $stats = $pdo->prepare("INSERT INTO player_statistics (user_id) VALUES (:uid)");
            $stats->execute([":uid" => $adminId]);

            $rating1 = $pdo->prepare("INSERT INTO player_ratings (user_id, mode, elo_rating, peak_elo_rating) VALUES (:uid, '1vs1', 1400, 1400)");
            $rating1->execute([":uid" => $adminId]);

            $rating2 = $pdo->prepare("INSERT INTO player_ratings (user_id, mode, elo_rating, peak_elo_rating) VALUES (:uid, '2vs2', 1400, 1400)");
            $rating2->execute([":uid" => $adminId]);

            echo "Usuario administrador creado en {$dbName} (admin / Admin123*)\n";
        }

        // Crear jugadores de prueba
        $testUsers = [
            ["username" => "chasqui_veloz", "email" => "chasqui@peru.pe", "name" => "Chasqui Veloz", "elo" => 1350, "wins" => 12, "losses" => 3],
            ["username" => "inca_sabio", "email" => "inca@peru.pe", "name" => "Inca Sabio", "elo" => 1420, "wins" => 18, "losses" => 2],
            ["username" => "vicuna_andina", "email" => "vicuna@peru.pe", "name" => "Vicuña Andina", "elo" => 1280, "wins" => 7, "losses" => 5]
        ];

        foreach ($testUsers as $tu) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
            $stmt->execute([":u" => $tu["username"]]);
            if (!$stmt->fetch()) {
                $h = password_hash("Jugador123*", PASSWORD_BCRYPT);
                $ins = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (:u, :e, :p, 'player', 'active')");
                $ins->execute([
                    ":u" => $tu["username"],
                    ":e" => $tu["email"],
                    ":p" => $h,
                ]);
                $uid = (int)$pdo->lastInsertId();

                $p = $pdo->prepare("INSERT INTO player_profiles (user_id, full_name, level, experience_points, total_points) VALUES (:uid, :fn, 3, 1200, 850)");
                $p->execute([":uid" => $uid, ":fn" => $tu["name"]]);

                $ratio = round(($tu["wins"] / ($tu["wins"] + $tu["losses"])) * 100, 2);
                $st = $pdo->prepare("INSERT INTO player_statistics (user_id, matches_played, matches_won, matches_lost, win_ratio) VALUES (:uid, :mp, :mw, :ml, :wr)");
                $st->execute([
                    ":uid" => $uid,
                    ":mp" => $tu["wins"] + $tu["losses"],
                    ":mw" => $tu["wins"],
                    ":ml" => $tu["losses"],
                    ":wr" => $ratio,
                ]);

                $r1 = $pdo->prepare("INSERT INTO player_ratings (user_id, mode, elo_rating, peak_elo_rating) VALUES (:uid, '1vs1', :elo, :elo)");
                $r1->execute([":uid" => $uid, ":elo" => $tu["elo"]]);

                $r2 = $pdo->prepare("INSERT INTO player_ratings (user_id, mode, elo_rating, peak_elo_rating) VALUES (:uid, '2vs2', :elo, :elo)");
                $r2->execute([":uid" => $uid, ":elo" => $tu["elo"]]);
                echo "Usuario de prueba creado en {$dbName}: {$tu["username"]}\n";
            }
        }
    }

    echo "Migraciones y semillas completadas exitosamente.\n";
} catch (Throwable $e) {
    echo "ERROR en migraciones: " . $e->getMessage() . "\n";
    exit(1);
}

