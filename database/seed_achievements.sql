-- Insercion de Logros e Insignias del Sistema

INSERT INTO `achievements` (`id`, `code`, `name`, `description`, `category`, `badge_icon`, `points_reward`, `xp_reward`) VALUES
(1, "primera_victoria", "Primera Victoria", "Consigue tu primera victoria en cualquier modalidad de juego.", "victorias", "bi-trophy", 50, 100),
(2, "veterano_10", "Veterano de Batalla", "Alcanza 10 victorias en partidas multijugador.", "victorias", "bi-award", 150, 300),
(3, "mariscal_50", "Gran Mariscal del Bicentenario", "Consigue 50 victorias en la plataforma.", "victorias", "bi-shield-shaded", 500, 1000),
(4, "experto_historia", "Historiador Nacional", "Acierta preguntas clave sobre la historia del Peru.", "conocimiento", "bi-hourglass-split", 200, 400),
(5, "maestro_geografia", "Geógrafo de los Andes", "Demuestra dominio del relieve y pisos ecologicos peruanos.", "conocimiento", "bi-compass", 200, 400),
(6, "racha_5", "Racha Imparable", "Logra una racha de 5 victorias consecutivas sin derrotas.", "racha", "bi-fire", 250, 500),
(7, "respuestas_100", "Enciclopedia Viviente", "Acumula 100 respuestas correctas en el sistema.", "precision", "bi-check-all", 300, 600),
(8, "paladar_oro", "Gourmet Peruano", "Demuestra excelencia en preguntas de gastronomia tradicional.", "conocimiento", "bi-egg-fried", 150, 300),
(9, "velocidad_pura", "Reflejos Chaski", "Responde correctamente a una pregunta en menos de 4 segundos.", "velocidad", "bi-lightning-charge", 100, 200),
(10, "imperio_solar", "Hijo del Sol", "Acierta multiples preguntas del Imperio Incaico y culturas preincaicas.", "conocimiento", "bi-sun", 200, 400)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `points_reward`=VALUES(`points_reward`), `xp_reward`=VALUES(`xp_reward`);

