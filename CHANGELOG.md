# Registro de Cambios (Changelog)

Todos los cambios notables realizados en este proyecto se documentan en este archivo.

El formato se basa en las directrices de Keep a Changelog y este proyecto se adhiere a Semantic Versioning (SemVer).

## [1.2.0] - 2026-11-04

### Añadido
- Módulo de presentación interactiva en pantalla panorámica 16:9 (".presentacion/index.html") con navegación por teclado, temporizador para exposición académica y menú modal de diapositivas.
- Códigos QR de alta resolución integrados en las diapositivas correspondientes a "Cookie Clicker" (https://cookie.pirulug.com/), "VS Cultura General" (https://cultura.pirulug.com/) y el portal oficial "PIRULUG S.A.C." (https://pirulug.com/).
- Módulo de impresión física en formato A4 (".presentacion/imprimir.html") configurado para imprimir un código QR gigante por cada página (3 páginas en total), con marcos estilizados, fichas técnicas completas y ficha de créditos oficiales para el autor Guido Euclides Laime Espejo y la empresa PIRULUG S.A.C.
- Enlace directo a la hoja de impresión A4 integrado en la barra de controles de la presentación.

### Modificado
- Reorientación del guion y contenido de la presentación hacia un enfoque motivacional y de orientación vocacional para incentivar la inscripción en la carrera de Computación e Informática del IESTP Santiago Antúnez de Mayolo, empleando los sistemas desarrollados como casos de éxito reales.
- Ampliación de las dimensiones visuales de los códigos QR en las diapositivas a 135px x 135px y 95px x 95px en la diapositiva de cierre para facilitar su captura desde teléfonos móviles a distancia.

## [1.1.0] - 2026-11-04

### Modificado
- Reestructuración del Front Controller moviendo el punto de entrada principal a la raíz del proyecto ("index.php"), eliminando la dependencia de subcarpetas públicas secundarias.
- Configuración de reglas de reescritura en ".htaccess" en la raíz del proyecto para redirigir las solicitudes dinámicas al Front Controller y permitir la carga directa de archivos estáticos en carpetas "/css", "/js" y "/assets".
- Armonización estética integral de la interfaz de usuario con paleta visual de modo oscuro inspirada en gaming competitivo.
- Aplicación de la regla de estilo estricta con radio de curvatura máximo de 2px ("border-radius: 2px !important;") en todos los componentes de la interfaz.

## [1.0.0] - 2026-11-03

### Añadido
- Lanzamiento inicial de la plataforma multijugador de cultura general del Perú.
- Arquitectura backend Modelo-Vista-Controlador (MVC) modular implementada enteramente en PHP 8.5 nativo en modo estricto ("declare(strict_types=1)"), sin frameworks de terceros.
- Núcleo del sistema ("App\Core"): Router declarativo, Request, Response, Session, CSRF y conexión Singleton a PDO.
- Esquema de base de datos relacional MySQL 8 normalizado con 23 tablas, soporte de transacciones ACID y script de migración automática ("database/run_migrations.php").
- Motor de juego con autoridad en servidor ("Server-Authoritative Game Engine") que valida tiempos y respuestas correctas sin exponerlas en el navegador del cliente.
- Modalidad individual: Duelos 1 vs 1 con emparejamiento automático por nivel de habilidad y 10 preguntas por partida.
- Modalidad por equipos: Competencia 2 vs 2 con marcador sincronizado y sumatoria grupal de puntos en tiempo real.
- Modalidad personalizada: Creación de salas privadas con código de acceso único, configuración de anfitrión, tiempo por pregunta y selección de categorías.
- Algoritmo matemático ELO para el cálculo de rangos competitivos en partidas individuales y promedio ponderado para equipos.
- Banco de preguntas estructurado con 18 categorías de historia, geografía, tradiciones, gastronomía, fauna, flora y patrimonio nacional peruano.
- Sistema de progresión del jugador con niveles de experiencia (XP), medallas y desbloqueo de insignias por logros.
- Módulos de clasificación: Rankings globales, semanales, mensuales y por categoría temática.
- Panel administrativo completo ("/admin") con módulos de Dashboard, Preguntas (con soporte de importación vía CSV), Categorías, Usuarios, Partidas y Temporadas.
- Documentación técnica inicial: Arquitectura del sistema ("docs/ARCHITECTURE.md"), diagrama entidad-relación ("docs/ERD.md") y manual de instalación ("docs/MANUAL_INSTALACION.md").

