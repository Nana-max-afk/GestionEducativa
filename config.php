<?php
// Configuración general del sistema

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'gestion_educativa');
define('DB_SOCKET', getenv('DB_SOCKET') ?: '');

define('SITE_TITLE', getenv('SITE_TITLE') ?: 'Gestión Educativa');
define('AUTH_TIMEOUT', (int)(getenv('AUTH_TIMEOUT') ?: 3600)); // segundos de inactividad antes de cerrar sesión
define('PASSWORD_MIN_LENGTH', (int)(getenv('PASSWORD_MIN_LENGTH') ?: 8));
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('BASE_URL', rtrim(getenv('BASE_URL') ?: '', '/'));
