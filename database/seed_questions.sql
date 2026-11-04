-- Banco de Preguntas de Cultura General del Peru
-- Categorias: 1 a 18 con 4 alternativas y 1 correcta

-- 1. Pregunta 1 (Historia)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(1, 1, "¿En qué batalla se selló definitivamente la independencia del Perú y de América del Sur en 1824?", "facil", "La Batalla de Ayacucho, librada el 9 de diciembre de 1824 en la pampa de la Quinua, selló la independencia del Perú y el fin del dominio español en América del Sur con la firma de la Capitulación de Ayacucho.", "Historia de la República del Perú - Jorge Basadre", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(1, "Batalla de Ayacucho", 1, 1),
(1, "Batalla de Junín", 0, 2),
(1, "Combate de Angamos", 0, 3),
(1, "Batalla de Miraflores", 0, 4);

-- 2. Pregunta 2 (Geografia)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(2, 2, "¿Cuál es el nevado más alto del Perú con una altitud de 6,768 m s. n. m.?", "facil", "El Huascarán, ubicado en la Cordillera Blanca en la región Áncash, es la cumbre más alta del Perú y la quinta de América del Sur.", "Instituto Geográfico Nacional (IGN)", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(2, "Huascarán", 1, 1),
(2, "Alpamayo", 0, 2),
(2, "Yerupajá", 0, 3),
(2, "Coropuna", 0, 4);

-- 3. Pregunta 3 (Gastronomia)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(3, 4, "¿Qué plato tradicional peruano fue declarado Patrimonio Cultural Inmaterial de la Humanidad por la UNESCO en 2023?", "facil", "En diciembre de 2023, las prácticas y los significados asociados a la preparación y el consumo del cebiche tradicional peruano fueron inscritos en la lista de Patrimonio Cultural Inmaterial de la UNESCO.", "UNESCO / Ministerio de Cultura", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(3, "El Cebiche tradicional", 1, 1),
(3, "La Causa limeña", 0, 2),
(3, "El Lomo saltado", 0, 3),
(3, "La Pachamanca", 0, 4);

-- 4. Pregunta 4 (Civilizaciones Preincaicas)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(4, 13, "¿Cuál es la civilización más antigua de América, desarrollada hace más de 5,000 años en el valle de Supe?", "intermedio", "Caral es considerada la civilización más antigua de América con más de 5,000 años de antigüedad (3000 a. C.), coetánea con las civilizaciones de Egipto, Mesopotamia y la India.", "Dra. Ruth Shady / Zona Arqueológica Caral", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(4, "Caral", 1, 1),
(4, "Chavín", 0, 2),
(4, "Mochica", 0, 3),
(4, "Paracas", 0, 4);

-- 5. Pregunta 5 (Imperio Incaico)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(5, 14, "¿Qué Inca transformó el Curacazgo del Cusco en el gran Imperio del Tahuantinsuyo tras vencer a los Chancas?", "intermedio", "Pachacútec (Cusi Yupanqui) asumió el liderazgo militar tras derrotar a los invasores chancas y refundó el Estado incaico transformándolo en un gran imperio expansivo.", "Comentarios Reales de los Incas - Inca Garcilaso de la Vega", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(5, "Pachacútec", 1, 1),
(5, "Túpac Yupanqui", 0, 2),
(5, "Huayna Cápac", 0, 3),
(5, "Manco Cápac", 0, 4);

-- 6. Pregunta 6 (Turismo y lugares)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(6, 5, "¿En qué región peruana se ubica la imponente fortaleza de Kuélap construida por la cultura Chachapoyas?", "intermedio", "Kuélap es un importante sitio arqueológico preinca edificado por la cultura Chachapoyas en la provincia de Luya, departamento de Amazonas.", "Ministerio de Comercio Exterior y Turismo (MINCETUR)", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(6, "Amazonas", 1, 1),
(6, "San Martín", 0, 2),
(6, "Cajamarca", 0, 3),
(6, "Loreto", 0, 4);

-- 7. Pregunta 7 (Literatura)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(7, 7, "¿Quién es el autor de los célebres 'Heraldos Negros' y 'Trilce', cumbre de la poesía vanguardista peruana?", "facil", "César Vallejo, nacido en Santiago de Chuco (La Libertad) en 1892, es considerado uno de los poetas más innovadores de la literatura universal del siglo XX.", "Casa de la Literatura Peruana", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(7, "César Vallejo", 1, 1),
(7, "José María Arguedas", 0, 2),
(7, "Mario Vargas Llosa", 0, 3),
(7, "Ciro Alegría", 0, 4);

-- 8. Pregunta 8 (Personajes Historicos)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(8, 6, "¿Qué heroico militar pronunció la frase 'Tengo deberes sagrados que cumplir y los cumpliré hasta quemar el último cartucho'?", "facil", "El coronel Francisco Bolognesi Cervantes pronunció esta célebre respuesta ante los emisarios chilenos antes de la Batalla de Arica del 7 de junio de 1880.", "Centro de Estudios Histórico Militares del Perú", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(8, "Francisco Bolognesi", 1, 1),
(8, "Miguel Grau", 0, 2),
(8, "Alfonso Ugarte", 0, 3),
(8, "Andrés Avelino Cáceres", 0, 4);

-- 9. Pregunta 9 (Simbolos Patrios)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(9, 11, "¿Qué elemento del Escudo Nacional del Perú simboliza la riqueza del reino vegetal?", "facil", "El árbol de la quina (Cinchona officinalis) ocupa el campo superior derecho en fondo blanco y representa la rica flora y las virtudes medicinales del reino vegetal del Perú.", "Constitución y Ley Orgánica de Símbolos Patrios", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(9, "El árbol de la quina", 1, 1),
(9, "La vicuña", 0, 2),
(9, "La cornucopia", 0, 3),
(9, "La palma y el laurel", 0, 4);

-- 10. Pregunta 10 (Deportes)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(10, 9, "¿Quién obtuvo la primera y única medalla de oro olímpica para el Perú en tiro foso en Londres 1948?", "dificil", "Edwin Vásquez Cam logró la histórica medalla de oro en la prueba de pistola libre a 50 metros en los Juegos Olímpicos de Londres 1948.", "Comité Olímpico Peruano", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(10, "Edwin Vásquez Cam", 1, 1),
(10, "Francisco Boza", 0, 2),
(10, "Juan Giha", 0, 3),
(10, "Stefano Peschiera", 0, 4);

-- 11. Pregunta 11 (Flora y Fauna)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(11, 17, "¿Cuál es el ave nacional del Perú, reconocida por el brillante plumaje anaranjado de sus machos?", "facil", "El Tunqui o Gallito de las Rocas (Rupicola peruvianus) fue declarado ave nacional del Perú en 1941 mediante resolución suprema.", "SERNANP", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(11, "Gallito de las Rocas", 1, 1),
(11, "Cóndor Andino", 0, 2),
(11, "Pava Aliblanca", 0, 3),
(11, "Colibrí Maravilloso", 0, 4);

-- 12. Pregunta 12 (Festividades y Danzas)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(12, 18, "¿En qué ciudad del Perú se celebra cada febrero la monumental Festividad de la Virgen de la Candelaria?", "facil", "La Festividad de la Virgen de la Candelaria tiene lugar en Puno y fue declarada Patrimonio Cultural Inmaterial de la Humanidad por la UNESCO en 2014.", "UNESCO / DDC Puno", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(12, "Puno", 1, 1),
(12, "Cusco", 0, 2),
(12, "Ayacucho", 0, 3),
(12, "Huancayo", 0, 4);

-- 13. Pregunta 13 (Regiones y Provincias)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(13, 12, "¿Cuál es el departamento más extenso territorialmente del Perú?", "facil", "Loreto cuenta con una superficie de más de 368,000 kilómetros cuadrados, lo que representa cerca del 28% de todo el territorio peruano.", "Instituto Nacional de Estadística e Informática (INEI)", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(13, "Loreto", 1, 1),
(13, "Ucayali", 0, 2),
(13, "Madre de Dios", 0, 3),
(13, "Cusco", 0, 4);

-- 14. Pregunta 14 (Ciencia y Tecnologia)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(14, 16, "¿Qué científico e ingeniero arequipeño es reconocido mundialmente como pionero de la astronáutica y la propulsión por cohetes?", "intermedio", "Pedro Paulet Mostajo (1874-1945) diseñó el primer motor cohete a combustible líquido y el prototipo de avión cohete en Europa a finales del siglo XIX.", "Sociedad Espacial Peruana / CONCYTEC", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(14, "Pedro Paulet", 1, 1),
(14, "Santiago Antúnez de Mayolo", 0, 2),
(14, "Daniel Alcides Carrión", 0, 3),
(14, "Hipólito Unanue", 0, 4);

-- 15. Pregunta 15 (Arte y Musica)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(15, 8, "¿Quién compuso la célebre melodía 'El cóndor pasa' en 1913, declarada Patrimonio Cultural de la Nación?", "intermedio", "Daniel Alomía Robles, compositor e investigador musical huanuqueño, compuso la zarzuela 'El cóndor pasa' estrenada en el Teatro Mazzi de Lima en 1913.", "Biblioteca Nacional del Perú", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(15, "Daniel Alomía Robles", 1, 1),
(15, "Felipe Pinglo Alva", 0, 2),
(15, "Chabuca Granda", 0, 3),
(15, "Augusto Polo Campos", 0, 4);

-- 16. Pregunta 16 (Cultura y Tradiciones)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(16, 3, "¿Qué danza tradicional andina se caracteriza por la destreza acrobática y el entrechocar rítmico de láminas de hierro?", "intermedio", "La Danza de las Tijeras es una manifestación ritual originaria de Huancavelica, Ayacucho y Apurímac, inscrita como Patrimonio Cultural de la Humanidad por la UNESCO.", "Ministerio de Cultura", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(16, "Danza de las Tijeras", 1, 1),
(16, "Huaylarsh", 0, 2),
(16, "Diablada", 0, 3),
(16, "Morenada", 0, 4);

-- 17. Pregunta 17 (Civilizaciones Preincaicas)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(17, 13, "¿Qué cultura preincaica destacó por sus extraordinarias trepanaciones craneanas y mantos funerarios polícromos?", "facil", "La cultura Paracas, desarrollada en la península de Paracas (Ica), destacó en sus periodos Cavernas y Necrópolis por sus avanzados conocimientos quirúrgicos y textiles.", "Museo Nacional de Arqueología, Antropología e Historia", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(17, "Paracas", 1, 1),
(17, "Mochica", 0, 2),
(17, "Nazca", 0, 3),
(17, "Chimú", 0, 4);

-- 18. Pregunta 18 (Independencia)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(18, 15, "¿En qué fecha exacta don José de San Martín proclamó la Independencia del Perú en la Plaza Mayor de Lima?", "facil", "El general don José de San Martín proclamó la independencia el sábado 28 de julio de 1821 con las memorables palabras: 'El Perú es, desde este momento, libre e independiente por la voluntad general de los pueblos...'", "Acta de la Independencia del Perú - Archivo General de la Nación", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(18, "28 de julio de 1821", 1, 1),
(18, "15 de julio de 1821", 0, 2),
(18, "6 de agosto de 1824", 0, 3),
(18, "9 de diciembre de 1824", 0, 4);

-- 19. Pregunta 19 (Geografia)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(19, 2, "¿Cuál es el lago navegable más alto del mundo que el Perú comparte con Bolivia?", "facil", "El Lago Titicaca, situado a 3,812 m s. n. m. en el altiplano andino, es el lago navegable a mayor altitud del planeta.", "Autoridad Binacional Autónoma del Lago Titicaca", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(19, "Lago Titicaca", 1, 1),
(19, "Lago Junín (Chinchaycocha)", 0, 2),
(19, "Laguna de Llanganuco", 0, 3),
(19, "Laguna de Huacachina", 0, 4);

-- 20. Pregunta 20 (Actualidad Nacional)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(20, 10, "¿Qué institución constitucional autónoma tiene la función primordial de defender la legalidad y los intereses públicos tutelados por la ley?", "intermedio", "El Ministerio Público - Fiscalía de la Nación es el órgano autónomo encargado de la persecución penal y la representación de la sociedad.", "Constitución Política del Perú de 1993, Art. 158", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(20, "Ministerio Público", 1, 1),
(20, "Defensoría del Pueblo", 0, 2),
(20, "Tribunal Constitucional", 0, 3),
(20, "Contraloría General", 0, 4);

-- 21. Pregunta 21 (Imperio Incaico)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(21, 14, "¿Cómo se llamaba la red vial prehispánica que unía los cuatro suyos del Imperio Incaico?", "intermedio", "El Qhapaq Ñan (Camino Principal Andino) integró más de 30,000 km de caminos estructurados a lo largo de seis países de Sudamérica y es Patrimonio Mundial de la UNESCO.", "Ministerio de Cultura - Proyecto Qhapaq Ñan", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(21, "Qhapaq Ñan", 1, 1),
(21, "Chasqui Wasi", 0, 2),
(21, "Acllahuasi", 0, 3),
(21, "Tambomachay", 0, 4);

-- 22. Pregunta 22 (Personajes Historicos)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(22, 6, "¿Con qué apelativo de honor es recordado el almirante Miguel Grau Seminario por su caballerosidad durante la Guerra del Pacífico?", "facil", "Miguel Grau es recordado como 'El Caballero de los Mares' debido al respeto, hidalguía y auxilio que brindó a los náufragos y viudas de los oficiales caídos.", "Marina de Guerra del Perú", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(22, "El Caballero de los Mares", 1, 1),
(22, "El Brujo de los Andes", 0, 2),
(22, "El Solitario de Sayán", 0, 3),
(22, "El Patriarca de las Letras", 0, 4);

-- 23. Pregunta 23 (Gastronomia)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(23, 4, "¿Qué licor y aguardiente de uva de denominación de origen nacional es el ingrediente base del Pisco Sour?", "facil", "El Pisco peruano es un aguardiente de uva puro y transparente cuya denominación de origen está protegida legalmente por el Estado peruano.", "INDECOPI", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(23, "Pisco", 1, 1),
(23, "Cañazo", 0, 2),
(23, "Chicha de jora", 0, 3),
(23, "Macerado de coca", 0, 4);

-- 24. Pregunta 24 (Civilizaciones Preincaicas)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(24, 13, "¿Qué gobernante moches del siglo III fue hallado intacto en su tumba real de Huaca Rajada en 1987 por Walter Alva?", "intermedio", "El hallazgo del Señor de Sipán en 1987 es considerado uno de los hitos arqueológicos más importantes del siglo XX en el continente americano.", "Museo Tumbas Reales de Sipán", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(24, "El Señor de Sipán", 1, 1),
(24, "El Señor de Sicán", 0, 2),
(24, "La Dama de Cao", 0, 3),
(24, "El Sacerdote de Huaca de la Luna", 0, 4);

-- 25. Pregunta 25 (Literatura)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `difficulty`, `explanation`, `source_reference`, `is_active`) VALUES
(25, 7, "¿Qué escritor arequipeño recibió el Premio Nobel de Literatura en el año 2010?", "facil", "Mario Vargas Llosa recibió el Premio Nobel de Literatura en 2010 por su cartografía de las estructuras de poder y sus afiladas imágenes de la resistencia individual.", "Real Academia Sueca", 1);
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(25, "Mario Vargas Llosa", 1, 1),
(25, "Julio Ramón Ribeyro", 0, 2),
(25, "Alfredo Bryce Echenique", 0, 3),
(25, "José María Arguedas", 0, 4);

