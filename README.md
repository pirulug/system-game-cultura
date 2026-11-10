# Plataforma Multijugador de Cultura General del Perú

Plataforma web de videojuegos de preguntas y respuestas sobre cultura general del Perú, diseñada para permitir la competencia interactiva en tiempo real entre usuarios mediante partidas individuales, competencias por equipos y salas privadas personalizadas.

El sistema pone a prueba los conocimientos sobre historia, geografía, cultura, tradiciones, gastronomía, turismo, personajes ilustres, literatura, arte, deportes, flora y fauna del Perú bajo una experiencia moderna, competitiva y adaptada a cualquier dispositivo.

## 1. Características Principales

### 1.1. Modalidades de Juego
- Duelo individual 1 vs 1: Emparejamiento por nivel de habilidad con 10 preguntas por partida, temporizadores sincronizados, bonificaciones por velocidad de respuesta y desempate automático.
- Competencia por equipos 2 vs 2: Dos equipos de dos integrantes respondiendo en simultáneo con sumatoria de puntos grupal y marcador en tiempo real.
- Salas personalizadas privadas: Creación de salas con código de acceso único, selección de categorías, dificultad, tiempo por pregunta y control para el anfitrión (iniciar partida o expulsar participantes).

### 1.2. Motor de Juego en Servidor (Server-Authoritative)
- Validación estricta en servidor: El cliente nunca recibe las respuestas correctas de forma anticipada.
- Prevención anti-trampas: Registro del tiempo de respuesta exacto y cálculo de puntos con bonificación por rapidez.
- Selección inteligente de preguntas: Algoritmo que evita la repetición de preguntas utilizadas recientemente por los participantes.

### 1.3. Banco de Preguntas y Categorías
- 18 categorías peruanas estructuradas: Historia del Perú, Geografía, Cultura y Tradiciones, Gastronomía, Turismo, Personajes Históricos, Literatura, Arte y Música, Deportes, Actualidad Nacional, Símbolos Patrios, Regiones y Departamentos, Civilizaciones Preincaicas, Imperio Incaico, Independencia del Perú, Ciencia y Tecnología, Flora y Fauna, Festividades y Danzas Típicas.
- Banco de datos verificado con 4 alternativas por pregunta, respuesta correcta, retroalimentación explicativa, nivel de dificultad y fuente de referencia.

### 1.4. Sistema de Clasificación y Progresión
- Algoritmo matemático ELO para partidas 1 vs 1 y cálculo ponderado para equipos 2 vs 2.
- Sistema de experiencia (XP) y niveles de jugador.
- Medallas, insignias y logros automáticos.
- Rankings competitivos: Clasificación global, semanal, mensual y clasificaciones por categoría.

### 1.5. Panel de Administración (/admin)
- Panel de control con métricas en tiempo real.
- Módulo de preguntas: Creación, edición, filtrado y carga masiva mediante archivo CSV.
- Módulo de categorías: Activación y gestión de contenidos temáticos.
- Módulo de usuarios: Monitoreo, suspensión y reactivación de cuentas.
- Módulo de partidas: Historial detallado y auditoría de rondas.
- Módulo de temporadas: Control de periodos y reinicio estructurado de rankings.

### 1.6. Módulo de Exposición y Material Imprimible
- Presentación interactiva en pantalla panorámica 16:9 con controles por teclado, cronómetro de exposición e índice rápido.
- Hojas imprimibles en formato A4 con códigos QR de gran tamaño individuales para probar los sistemas en vivo desde teléfonos móviles.

## 2. Tecnologías Empleadas

El proyecto está desarrollado enteramente con tecnologías web estándar y nativas, prescindiendo deliberadamente de frameworks monolíticos:

- Backend: PHP 8.5 nativo con modo estricto ("declare(strict_types=1)"), arquitectura MVC modular orientada a objetos y principios SOLID.
- Base de datos: MySQL 8 con motor InnoDB, juego de caracteres utf8mb4, transacciones ACID y consultas preparadas mediante PDO.
- Frontend: HTML5 semántico, CSS3 y Bootstrap 5 para el sistema de cuadrícula responsivo.
- JavaScript: JavaScript Vanilla (ES6+) con Fetch API y polling ligero para la sincronización en tiempo real. Sin dependencias de jQuery, React ni Vue.
- Servidor web: Compatible con Apache (con mod_rewrite activado vía .htaccess en la raíz) o Nginx.

## 3. Estructura del Proyecto

```text
pre/
├── app/
│   ├── Config/          # Configuración de base de datos, sesiones y rutas
│   ├── Controllers/     # Controladores Web y API (MVC)
│   ├── Core/            # Núcleo del framework nativo (Router, Request, Response, CSRF, Database)
│   ├── Middleware/      # Filtros de autenticación, rol de administrador y visitantes
│   ├── Models/          # Modelos de datos y consultas SQL optimizadas
│   ├── Services/        # Lógica de negocio (GameEngine, Matchmaking, ELO, AuthService)
│   └── Views/           # Plantillas de vistas HTML y parciales
├── assets/              # Imágenes, avatares e iconos
├── css/                 # Hojas de estilo personalizadas
├── database/            # Scripts SQL (schema, seeds) y ejecutor de migraciones
├── docs/                # Documentación de arquitectura, modelo relacional y manuales
├── js/                  # Módulos JavaScript Vanilla para cliente y juego
├── .presentacion/        # Presentación interactiva 16:9 y hojas de impresión A4
├── .gitignore           # Exclusiones de control de versiones
├── .htaccess            # Configuración de reescritura de URLs para Apache
├── CHANGELOG.md         # Historial cronológico de cambios y versiones
├── index.php            # Front Controller principal en la raíz
└── README.md            # Documentación general del proyecto
```

## 4. Requisitos de Instalación

- Servidor web Apache 2.4 o Nginx con reescritura de URLs habilitada.
- PHP 8.5 o superior con extensiones activas: pdo, pdo_mysql, mbstring, json y session.
- Servidor MySQL 8.0 o MariaDB 10.5+.

## 5. Puesta en Marcha

### Paso 1: Configurar la Base de Datos
1. Iniciar los servicios de Apache y MySQL en el entorno de desarrollo (por ejemplo WampServer o XAMPP).
2. Abrir una terminal en la carpeta raíz del proyecto y ejecutar el asistente de migraciones:
   ```bash
   php database/run_migrations.php
   ```
   Este comando creará la base de datos "cultura_peru" con las 23 tablas normalizadas, las categorías oficiales, las preguntas iniciales, los logros y las cuentas preconfiguradas.

### Paso 2: Ejecutar en el Servidor Web
- Si se utiliza WampServer en la ruta "c:\PiruWamp\www\pre", acceder directamente en el navegador a:
  ```text
  http://localhost/pre/
  ```
- Si se prefiere utilizar el servidor embebido de PHP para desarrollo:
  ```bash
  php -S 127.0.0.1:8000
  ```
  y abrir `http://127.0.0.1:8000/`.

## 6. Credenciales de Prueba

### Cuenta de Administrador
- Usuario: `admin`
- Correo: `admin@culturaperu.pe`
- Contraseña: `Admin123*`
- Acceso: `http://localhost/pre/admin`

### Cuentas de Jugadores para Pruebas Multijugador
- Jugador 1: `chasqui_veloz` / Contraseña: `Jugador123*`
- Jugador 2: `inca_sabio` / Contraseña: `Jugador123*`
- Jugador 3: `vicuna_andina` / Contraseña: `Jugador123*`

## 7. Módulos Adicionales Incluidos

- Presentación Interactiva (10 diapositivas):
  Accesible en `.presentacion/index.html` con material didáctico y motivacional sobre desarrollo de software, videojuegos y emprendimiento tecnológico.
- Hojas Imprimibles A4 con Códigos QR:
  Accesible en `.presentacion/imprimir.html` para la generación de afiches de prueba en vivo para exposiciones y ferias académicas.

## 8. Créditos y Autoría

- Desarrollador: Guido Euclides Laime Espejo
- Empresa: PIRULUG S.A.C.
- Carrera Profesional: Computación e Informática
- Institución: IESTP Santiago Antúnez de Mayolo

