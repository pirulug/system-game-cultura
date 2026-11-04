-- Insercion de las 18 Categorias Fundamentales de Cultura Peruana

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon_class`, `display_order`, `is_active`) VALUES
(1, "Historia del Perú", "historia-del-peru", "Acontecimientos trascendentales, epocas y procesos historicos de la nacion peruana.", "bi-hourglass-split", 1, 1),
(2, "Geografía del Perú", "geografia-del-peru", "Relieve, hidrografia, cordilleras, cuencas, climas y pisos ecologicos peruanos.", "bi-compass", 2, 1),
(3, "Cultura y tradiciones", "cultura-y-tradiciones", "Usos, costumbres, saberes ancestrales y patrimonio cultural inmaterial del Peru.", "bi-people", 3, 1),
(4, "Gastronomía peruana", "gastronomia-peruana", "Platos tipicos, insumos originarios, tecnicas culinarias y maestros de la cocina.", "bi-egg-fried", 4, 1),
(5, "Turismo y lugares turísticos", "turismo-y-lugares-turisticos", "Maravillas del mundo, parques arqueologicos, reservas naturales y circuitos de viaje.", "bi-geo-alt", 5, 1),
(6, "Personajes históricos", "personajes-historicos", "Proceres, heroes civiles y militares, lideres y figuras tutelares del pais.", "bi-person-badge", 6, 1),
(7, "Literatura peruana", "literatura-peruana", "Escritores, cronistas, poetas, obras maestras y corrientes literarias.", "bi-book", 7, 1),
(8, "Arte y música", "arte-y-musica", "Pintura, escultura, ceramica, musica criolla, andina, afroperuana y contemporanea.", "bi-music-note-beamed", 8, 1),
(9, "Deportes peruanos", "deportes-peruanos", "Logros olimpicos, futbol nacional, voleibol, surf, ajedrez y glorias deportivas.", "bi-trophy", 9, 1),
(10, "Actualidad nacional", "actualidad-nacional", "Constitucion, institucionalidad democratica, economia e infraestructura contemporanea.", "bi-newspaper", 10, 1),
(11, "Símbolos patrios", "simbolos-patrios", "Bandera, Escudo, Himno Nacional, escarapela y simbolos representativos del Estado.", "bi-flag", 11, 1),
(12, "Regiones, departamentos y provincias", "regiones-departamentos-y-provincias", "Division politica, capitales departamentales, provincias, distritos y georreferencia.", "bi-map", 12, 1),
(13, "Civilizaciones preincaicas", "civilizaciones-preincaicas", "Caral, Chavin, Paracas, Mochica, Nazca, Wari, Chimu y culturas originarias.", "bi-bank", 13, 1),
(14, "Imperio incaico", "imperio-incaico", "Tahuantinsuyo, Incas gobernantes, organizacion social, red vial Qhapaq Nan y cosmovision.", "bi-sun", 14, 1),
(15, "Independencia del Perú", "independencia-del-peru", "Rebeliones precursoras, Expedicion Libertadora, batallas de Junin y Ayacucho.", "bi-shield-check", 15, 1),
(16, "Ciencia y tecnología", "ciencia-y-tecnologia", "Cientificos peruanos, inventos, astronomia ancestral y aportes a la investigacion.", "bi-cpu", 16, 1),
(17, "Flora y fauna del Perú", "flora-y-fauna-del-peru", "Biodiversidad, especies endemicas, fauna silvestre y areas naturales protegidas.", "bi-tree", 17, 1),
(18, "Festividades y danzas típicas", "festividades-y-danzas-tipicas", "Danza de tijeras, marinera, huaylas, Candelaria, Inti Raymi y celebraciones patronales.", "bi-stars", 18, 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `icon_class`=VALUES(`icon_class`), `display_order`=VALUES(`display_order`);

