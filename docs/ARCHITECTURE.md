# ARQUITECTURA DEL SISTEMA - CULTURA PERU

## 1. Descripcion General

Cultura Peru es una plataforma web multijugador en tiempo real de trivia y competencia basada en la cultura general de la Republica del Peru. La arquitectura se basa en el patron Modelo-Vista-Controlador (MVC) modular implementado enteramente en PHP 8.5 nativo, respetando estandares modernos de desarrollo, principios SOLID y separacion estricta de responsabilidades sin dependencias de frameworks externos.

## 2. Tecnologias Empleadas

Backend:
- PHP 8.5 nativo en modo estricto (declare(strict_types=1)).
- PDO MySQL con transacciones ACID y consultas preparadas.
- Sesiones seguras con regeneracion de identificador y cookies HttpOnly / SameSite.

Base de Datos:
- MySQL 8 con motor InnoDB y juego de caracteres utf8mb4.
- Claves primarias bigint sin signo, restricciones de integridad referencial e indices optimizados.

Frontend:
- HTML5 semantico.
- CSS3 y Bootstrap 5 para el sistema de cuadricula, utilidades y adaptabilidad movil.
- Diseno visual inspirado en gaming competitivo con paleta rojo peruano (#C8102E), dorado andino (#FFB81C) y fondos oscuros de alta fidelidad (#0F172A, #1E293B).
- JavaScript Vanilla modular (ES6+) con Fetch API y controladores de eventos.

Comunicacion y Tiempo Real:
- Motor de autorizacion y sincronizacion en servidor (Server-Authoritative State Engine).
- Polling optimizado mediante endpoints ligeros JSON con control de marcas temporales y proteccion contra trampas.

## 3. Capas de la Arquitectura

### 3.1. Capa de Ruteo y Nucleo (App\Core)
- Router: Gestiona las rutas declarativas con soporte para parametros dinamicos, metodos HTTP (GET, POST) y aplicacion automatica de Middlewares.
- Request: Abstrae el entorno HTTP, cabeceras, parametros query, cuerpo POST sanitizado y datos de sesion.
- Response: Controla la emision de vistas HTML y respuestas JSON con cabeceras de seguridad y codigos de estado apropiados.
- Session: Maneja de forma segura el ciclo de vida de la sesion, mensajes flash y datos del usuario autenticado.
- CSRF: Genera, almacena y valida tokens criptograficos aleatorios para prevenir ataques Cross-Site Request Forgery.
- Controller: Controlador base con metodos para renderizar vistas, emitir respuestas JSON y verificar permisos.
- Model: Modelo base que interactua con el Singleton de Database PDO.

### 3.2. Capa de Middleware (App\Middleware)
- AuthMiddleware: Protege rutas de jugadores asegurando que solo usuarios autenticados accedan.
- AdminMiddleware: Asegura que unicamente usuarios con rol 'admin' puedan acceder a la administracion.
- GuestMiddleware: Evita que usuarios ya autenticados accedan a login o registro.

### 3.3. Capa de Servicios (App\Services)
La logica de negocio compleja reside en servicios desacoplados:
- AuthService: Registro, hash seguro de contrasenas con Argon2id/Bcrypt, autenticacion y verificacion de credenciales.
- MatchmakingService: Gestiona colas de espera concurrentes para duelos 1 vs 1 y 2 vs 2.
- GameEngineService: Autoridad de la partida; crea partidas, selecciona preguntas aleatorias no repetidas recientemente, computa temporizadores de ronda, valida respuestas, calcula puntos con bonificacion por velocidad y determina ganadores.
- EloRatingService: Algoritmo matematico ELO para calcular el cambio de puntuacion en 1 vs 1 y promedio ponderado para equipos 2 vs 2.
- AchievementService: Evalua logros y medallas tras cada partida y asigna recompensas.
- RankingService: Genera clasificaciones globales, semanales, mensuales y por categoria.
- AdminService: Provee auditoria, gestion de usuarios, reporte de preguntas e importacion de preguntas via CSV.

### 3.4. Capa de Modelos (App\Models)
Entidades fuertemente tipadas que representan las tablas relacionales y sus consultas optimizadas.

### 3.5. Capa de Vistas y Presentacion (App\Views)
Vistas en PHP con plantillas compartidas, sanitizacion contra XSS (htmlspecialchars) y componentes interactivos.

## 4. Flujo de Comunicacion y Seguridad

1. Petición entrante procesada por index.php en la raíz del proyecto.
2. Inicializacion de sesion segura y carga de variables de entorno.
3. Ruteo e inspeccion de Middlewares correspondientes.
4. Ejecucion del controlador respectivo.
5. Invocacion de servicios de negocio y modelos de persistencia.
6. Emision de respuesta HTTP con cabeceras de proteccion X-Frame-Options, X-Content-Type-Options y Content-Security-Policy.

