-- Esquema de Base de Datos para Juego Multijugador de Cultura General del Peru
-- Motor: MySQL 8 (InnoDB)
-- Codificacion: utf8mb4 / utf8mb4_unicode_ci

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tabla de usuarios
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM("player", "admin", "moderator") NOT NULL DEFAULT "player",
    `status` ENUM("active", "suspended", "banned") NOT NULL DEFAULT "active",
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role_status` (`role`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Perfil del jugador
DROP TABLE IF EXISTS `player_profiles`;
CREATE TABLE `player_profiles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `full_name` VARCHAR(120) NOT NULL,
    `avatar` VARCHAR(255) NOT NULL DEFAULT "default_avatar.png",
    `bio` TEXT NULL,
    `level` INT UNSIGNED NOT NULL DEFAULT 1,
    `experience_points` INT UNSIGNED NOT NULL DEFAULT 0,
    `total_points` INT UNSIGNED NOT NULL DEFAULT 0,
    `connection_status` ENUM("online", "in_game", "offline") NOT NULL DEFAULT "offline",
    `last_seen_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_player_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Categorias de preguntas
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `icon_class` VARCHAR(50) NOT NULL DEFAULT "bi-book",
    `display_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Preguntas
DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `question_text` TEXT NOT NULL,
    `difficulty` ENUM("facil", "intermedio", "dificil", "experto") NOT NULL DEFAULT "intermedio",
    `explanation` TEXT NOT NULL,
    `source_reference` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `times_played` INT UNSIGNED NOT NULL DEFAULT 0,
    `times_correct` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_questions_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    INDEX `idx_questions_cat_diff_active` (`category_id`, `difficulty`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Alternativas de preguntas
DROP TABLE IF EXISTS `question_options`;
CREATE TABLE `question_options` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `option_text` VARCHAR(255) NOT NULL,
    `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
    `option_order` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    CONSTRAINT `fk_question_options_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
    INDEX `idx_question_options_qid_correct` (`question_id`, `is_correct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Salas de juego
DROP TABLE IF EXISTS `game_rooms`;
CREATE TABLE `game_rooms` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `creator_id` BIGINT UNSIGNED NOT NULL,
    `room_code` VARCHAR(16) NOT NULL UNIQUE,
    `name` VARCHAR(80) NOT NULL,
    `mode` ENUM("1vs1", "2vs2", "custom") NOT NULL DEFAULT "1vs1",
    `is_private` TINYINT(1) NOT NULL DEFAULT 0,
    `password_hash` VARCHAR(255) NULL,
    `max_players` TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `questions_count` TINYINT UNSIGNED NOT NULL DEFAULT 10,
    `seconds_per_question` TINYINT UNSIGNED NOT NULL DEFAULT 15,
    `difficulty` ENUM("all", "facil", "intermedio", "dificil", "experto") NOT NULL DEFAULT "all",
    `allowed_category_id` BIGINT UNSIGNED NULL,
    `allow_spectators` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM("waiting", "in_game", "finished", "cancelled") NOT NULL DEFAULT "waiting",
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_game_rooms_creator` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_game_rooms_status_code` (`status`, `room_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Jugadores en la sala
DROP TABLE IF EXISTS `game_room_players`;
CREATE TABLE `game_room_players` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `room_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `team` ENUM("A", "B", "spectator") NOT NULL DEFAULT "A",
    `is_ready` TINYINT(1) NOT NULL DEFAULT 0,
    `is_host` TINYINT(1) NOT NULL DEFAULT 0,
    `joined_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_room_user` (`room_id`, `user_id`),
    CONSTRAINT `fk_game_room_players_room` FOREIGN KEY (`room_id`) REFERENCES `game_rooms` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_game_room_players_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Partidas
DROP TABLE IF EXISTS `matches`;
CREATE TABLE `matches` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `room_id` BIGINT UNSIGNED NULL,
    `mode` ENUM("1vs1", "2vs2", "custom") NOT NULL DEFAULT "1vs1",
    `status` ENUM("pending", "in_progress", "showing_result", "finished", "abandoned") NOT NULL DEFAULT "pending",
    `current_question_index` INT UNSIGNED NOT NULL DEFAULT 0,
    `total_questions` INT UNSIGNED NOT NULL DEFAULT 10,
    `seconds_per_question` INT UNSIGNED NOT NULL DEFAULT 15,
    `round_start_time` TIMESTAMP NULL,
    `round_end_time` TIMESTAMP NULL,
    `round_reveal_time` TIMESTAMP NULL,
    `winner_user_id` BIGINT UNSIGNED NULL,
    `winner_team` ENUM("A", "B", "tie") NULL,
    `started_at` TIMESTAMP NULL,
    `finished_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_matches_room` FOREIGN KEY (`room_id`) REFERENCES `game_rooms` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_matches_winner_user` FOREIGN KEY (`winner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_matches_status_started` (`status`, `started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Jugadores en la partida
DROP TABLE IF EXISTS `match_players`;
CREATE TABLE `match_players` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `match_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `team` ENUM("A", "B") NOT NULL DEFAULT "A",
    `score` INT NOT NULL DEFAULT 0,
    `correct_answers` INT UNSIGNED NOT NULL DEFAULT 0,
    `incorrect_answers` INT UNSIGNED NOT NULL DEFAULT 0,
    `average_response_time` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `rating_before` INT NOT NULL DEFAULT 1200,
    `rating_after` INT NOT NULL DEFAULT 1200,
    `rating_change` INT NOT NULL DEFAULT 0,
    `experience_gained` INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY `uk_match_player` (`match_id`, `user_id`),
    CONSTRAINT `fk_match_players_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_match_players_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Preguntas asignadas a la partida
DROP TABLE IF EXISTS `match_questions`;
CREATE TABLE `match_questions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `match_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `question_order` INT UNSIGNED NOT NULL,
    UNIQUE KEY `uk_match_question_order` (`match_id`, `question_order`),
    CONSTRAINT `fk_match_questions_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_match_questions_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Respuestas de los jugadores
DROP TABLE IF EXISTS `player_answers`;
CREATE TABLE `player_answers` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `match_id` BIGINT UNSIGNED NOT NULL,
    `match_question_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `selected_option_id` BIGINT UNSIGNED NULL,
    `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
    `points_awarded` INT NOT NULL DEFAULT 0,
    `response_time_seconds` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `answered_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_player_match_question` (`match_id`, `match_question_id`, `user_id`),
    CONSTRAINT `fk_player_answers_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_player_answers_mq` FOREIGN KEY (`match_question_id`) REFERENCES `match_questions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_player_answers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_player_answers_option` FOREIGN KEY (`selected_option_id`) REFERENCES `question_options` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Calificaciones ELO por jugador
DROP TABLE IF EXISTS `player_ratings`;
CREATE TABLE `player_ratings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `mode` ENUM("1vs1", "2vs2") NOT NULL DEFAULT "1vs1",
    `elo_rating` INT NOT NULL DEFAULT 1200,
    `peak_elo_rating` INT NOT NULL DEFAULT 1200,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_mode_rating` (`user_id`, `mode`),
    CONSTRAINT `fk_player_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Estadisticas generales del jugador
DROP TABLE IF EXISTS `player_statistics`;
CREATE TABLE `player_statistics` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `matches_played` INT UNSIGNED NOT NULL DEFAULT 0,
    `matches_won` INT UNSIGNED NOT NULL DEFAULT 0,
    `matches_lost` INT UNSIGNED NOT NULL DEFAULT 0,
    `matches_tied` INT UNSIGNED NOT NULL DEFAULT 0,
    `win_streak` INT UNSIGNED NOT NULL DEFAULT 0,
    `max_win_streak` INT UNSIGNED NOT NULL DEFAULT 0,
    `total_correct_answers` INT UNSIGNED NOT NULL DEFAULT 0,
    `total_questions_answered` INT UNSIGNED NOT NULL DEFAULT 0,
    `win_ratio` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_player_statistics_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Amistades confirmadas
DROP TABLE IF EXISTS `friendships`;
CREATE TABLE `friendships` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `friend_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_friendship_pair` (`user_id`, `friend_id`),
    CONSTRAINT `fk_friendships_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_friendships_friend` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Solicitudes de amistad
DROP TABLE IF EXISTS `friend_requests`;
CREATE TABLE `friend_requests` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `sender_id` BIGINT UNSIGNED NOT NULL,
    `receiver_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM("pending", "accepted", "rejected") NOT NULL DEFAULT "pending",
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_friend_request` (`sender_id`, `receiver_id`),
    CONSTRAINT `fk_friend_requests_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_friend_requests_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Notificaciones
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(120) NOT NULL,
    `message` TEXT NOT NULL,
    `data_json` JSON NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_notifications_user_read` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Logros del sistema
DROP TABLE IF EXISTS `achievements`;
CREATE TABLE `achievements` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `category` VARCHAR(50) NOT NULL DEFAULT "general",
    `badge_icon` VARCHAR(50) NOT NULL DEFAULT "bi-trophy",
    `points_reward` INT UNSIGNED NOT NULL DEFAULT 50,
    `xp_reward` INT UNSIGNED NOT NULL DEFAULT 100,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. Logros desbloqueados por jugadores
DROP TABLE IF EXISTS `player_achievements`;
CREATE TABLE `player_achievements` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `achievement_id` BIGINT UNSIGNED NOT NULL,
    `unlocked_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_player_achievement` (`user_id`, `achievement_id`),
    CONSTRAINT `fk_player_achievements_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_player_achievements_ach` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Historial de experiencia (XP)
DROP TABLE IF EXISTS `experience_history`;
CREATE TABLE `experience_history` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `match_id` BIGINT UNSIGNED NULL,
    `amount` INT NOT NULL,
    `source` VARCHAR(80) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_xp_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_xp_history_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. Temporadas competitivas
DROP TABLE IF EXISTS `seasons`;
CREATE TABLE `seasons` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. Reportes de preguntas o jugadores
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reporter_user_id` BIGINT UNSIGNED NOT NULL,
    `target_type` ENUM("question", "user") NOT NULL,
    `target_id` BIGINT UNSIGNED NOT NULL,
    `reason` VARCHAR(120) NOT NULL,
    `details` TEXT NULL,
    `status` ENUM("pending", "reviewed", "dismissed", "action_taken") NOT NULL DEFAULT "pending",
    `reviewed_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reports_reporter` FOREIGN KEY (`reporter_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_reports_admin` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. Registro de auditoria administrativa
DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE `admin_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_user_id` BIGINT UNSIGNED NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` BIGINT UNSIGNED NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_admin_logs_user` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cola de Matchmaking en memoria/tabla para emparejamiento rapido
DROP TABLE IF EXISTS `matchmaking_queue`;
CREATE TABLE `matchmaking_queue` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `mode` ENUM("1vs1", "2vs2") NOT NULL DEFAULT "1vs1",
    `elo_rating` INT NOT NULL DEFAULT 1200,
    `status` ENUM("searching", "matched", "cancelled") NOT NULL DEFAULT "searching",
    `matched_room_id` BIGINT UNSIGNED NULL,
    `joined_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mm_queue_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

