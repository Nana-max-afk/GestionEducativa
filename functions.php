<?php
require_once __DIR__ . '/config.php';

function create_connection($database = null) {
    $socket = DB_SOCKET !== '' ? DB_SOCKET : null;
    return new mysqli(DB_HOST, DB_USER, DB_PASS, $database, DB_PORT, $socket);
}

function db_connect() {
    static $db = null;

    if ($db === null) {
        $db = create_connection(DB_NAME);
        if ($db->connect_errno) {
            $fallback = create_connection('');
            if (!$fallback->connect_errno) {
                $fallback->query("CREATE DATABASE IF NOT EXISTS `" . str_replace('`', '', DB_NAME) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                $fallback->close();
                $db = create_connection(DB_NAME);
            }
        }

        if ($db->connect_errno) {
            error_log('Database connection failed: ' . $db->connect_error);
            die('No se pudo conectar a la base de datos. Revise los datos de conexión o ejecute el asistente de instalación.');
        }

        $db->set_charset('utf8mb4');
    }

    return $db;
}

function ensure_schema() {
    $conn = db_connect();
    $result = $conn->query('SHOW TABLES');
    if ($result && $result->num_rows > 0) {
        return true;
    }

    $schema_path = __DIR__ . '/gestion_educativa.sql';
    if (!is_file($schema_path)) {
        return false;
    }

    $sql = file_get_contents($schema_path);
    if ($sql === false) {
        return false;
    }

    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS `[^`]+`.*?;/i', '', $sql);
    $sql = preg_replace('/USE\s+`?[^`\s;]+`?/i', '', $sql);
    $sql = str_replace('`gestion_educativa`', '`' . DB_NAME . '`', $sql);

    if (!$conn->multi_query($sql)) {
        return false;
    }

    do {
        if ($conn->errno) {
            return false;
        }
    } while ($conn->more_results() && $conn->next_result());

    return true;
}

function db_query($sql) {
    return db_connect()->query($sql);
}

function db_prepare($sql) {
    return db_connect()->prepare($sql);
}

function db_escape($value) {
    return db_connect()->real_escape_string($value);
}

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

function flash($message, $type = 'info') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type,
    ];
}

function get_flash() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function validate_csrf($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function login_user($user) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_regenerate_id(true);
    $_SESSION['user'] = $user;
    $_SESSION['last_activity'] = time();
}

function current_user() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }

    if (time() - ($_SESSION['last_activity'] ?? 0) > AUTH_TIMEOUT) {
        session_unset();
        session_destroy();
        redirect('login.php');
    }

    $_SESSION['last_activity'] = time();
}

function is_admin() {
    $user = current_user();
    return $user && isset($user['Rol']) && $user['Rol'] === 'Administrador';
}

function user_has_role($roles) {
    $user = current_user();
    if (!$user) {
        return false;
    }
    $roles = is_array($roles) ? $roles : [$roles];
    return in_array($user['Rol'], $roles, true);
}

function role_home($role) {
    $role = trim((string)($role ?? ''));
    if ($role === 'Estudiante') {
        return 'student_dashboard.php';
    }
    if ($role === 'Docente') {
        return 'teacher_dashboard.php';
    }
    return 'index.php';
}
