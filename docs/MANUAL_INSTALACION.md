# MANUAL DE INSTALACIÓN Y GUÍA DE OPERACIÓN

## 1. Requisitos del Sistema

Servidor y Entorno:
- Sistema Operativo: Windows, Linux o macOS.
- Servidor Web: Apache 2.4 o Nginx con soporte para reescritura de URL (mod_rewrite).
- Lenguaje: PHP 8.5 nativo o superior con extensiones habilitadas:
  - pdo
  - pdo_mysql
  - mbstring
  - json
  - session
- Base de Datos: MySQL 8.0 o MariaDB 10.5+.

## 2. Configuración de Base de Datos

El sistema incluye un script automatizado para la creación del esquema y la carga de datos iniciales:

1. Asegurarse de que el servicio MySQL se encuentre activo en el puerto 3306.
2. Ejecutar el script de migraciones por terminal:
   php database/run_migrations.php

Este comando creará la base de datos "cultura_peru" con las 23 tablas normalizadas, las 18 categorías oficiales de cultura peruana, el banco de preguntas verificadas, las insignias de logros y los usuarios iniciales.

## 3. Cuentas de Demostración Preconfiguradas

Cuenta de Administrador:
- Usuario: admin
- Correo: admin@culturaperu.pe
- Contraseña: Admin123*
- Rol: admin (acceso al panel /admin)

Cuentas de Jugadores para Pruebas Multijugador:
- Usuario: chasqui_veloz / Contraseña: Jugador123*
- Usuario: inca_sabio / Contraseña: Jugador123*
- Usuario: vicuna_andina / Contraseña: Jugador123*

## 4. Despliegue en Servidor Local o de Producción

En Apache / WampServer:
- La carpeta del proyecto se ubica en c:\PiruWamp\www\pre.
- El Front Controller principal se ejecuta directamente en la raíz: c:\PiruWamp\www\pre\index.php.
- Puede ser accedida directamente a través del navegador web mediante la URL:
  http://localhost/pre/
- El archivo .htaccess en la raíz redirige las peticiones al Front Controller (index.php), sirviendo de forma directa los recursos estáticos existentes en /css, /js y /assets.

En Servidor Integrado de PHP:
- Puede ejecutarse un servidor de desarrollo ligero desde la raíz del proyecto ejecutando:
  php -S 127.0.0.1:8000
- Y acceder en: http://127.0.0.1:8000/

## 5. Arquitectura y Mecánicas de Juego

Motor en Tiempo Real (Server-Authoritative):
- Todas las validaciones de tiempo y respuestas correctas se ejecutan estrictamente en el backend (App\Services\GameEngineService).
- El navegador web del cliente no conoce la alternativa correcta hasta que la ronda concluye su temporizador regresivo o todos los participantes han enviado su respuesta.
- Se implementa el algoritmo matemático ELO para el cálculo de rangos competitivos en partidas 1 vs 1 y promedio ponderado para equipos 2 vs 2.
- El sistema de experiencia (XP) ajusta progresivamente el nivel del jugador y desbloquea insignias tras cada partida.

Panel Administrativo (/admin):
- Módulo Dashboard con métricas globales en tiempo real.
- Módulo de Preguntas con creación manual, edición con 4 alternativas y carga masiva mediante archivo CSV.
- Módulo de Categorías con activación y gestión de iconos representativos.
- Módulo de Usuarios para suspensión y reactivación de cuentas.
- Módulo de Partidas para auditoría y supervisión de marcadores.
- Módulo de Clasificación con reinicio controlado de temporadas competitivas.

