<?php
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = null;
$error = null;

function table_has_column($table, $column) {
    $schema = DB_NAME;
    $tableName = $table;
    $columnName = $column;
    $stmt = db_prepare('SELECT 1 FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('sss', $schema, $tableName, $columnName);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result && $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
    return false;
}

function get_user_by_username($username) {
    $stmt = db_prepare('SELECT Usuario_ID, Username, Rol, Status FROM usuarios WHERE Username = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $user;
    }
    return null;
}

function update_user_admin($id, $passwordHash, $rol, $fecha) {
    $status = 'Activo';
    $stmt = db_prepare('UPDATE usuarios SET PasswordHash = ?, Rol = ?, Status = ?, Fecha = ? WHERE Usuario_ID = ?');
    if ($stmt) {
        $stmt->bind_param('ssssi', $passwordHash, $rol, $status, $fecha, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    return false;
}

if (!function_exists('ensure_schema')) {
    require_once __DIR__ . '/functions.php';
}

try {
    ensure_schema();
} catch (Throwable $e) {
    $error = 'No se pudo inicializar la base de datos automáticamente: ' . $e->getMessage();
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = trim($_POST['rol'] ?? 'Administrador');
    $fecha = date('Y-m-d');

    if ($username === '' || $password === '' || strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Complete los datos y use una contraseña de al menos ' . PASSWORD_MIN_LENGTH . ' caracteres.';
    } else {
        if (!table_has_column('usuarios', 'Username')) {
            db_query("ALTER TABLE usuarios ADD COLUMN Username VARCHAR(100) NOT NULL UNIQUE AFTER Usuario_ID");
        }
        if (!table_has_column('usuarios', 'PasswordHash')) {
            db_query("ALTER TABLE usuarios ADD COLUMN PasswordHash VARCHAR(255) NOT NULL AFTER Username");
        }
        if (!table_has_column('usuarios', 'Status')) {
            db_query("ALTER TABLE usuarios ADD COLUMN Status ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' AFTER Rol");
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $existingUser = get_user_by_username($username);

        if ($existingUser) {
            if (update_user_admin($existingUser['Usuario_ID'], $passwordHash, $rol, $fecha)) {
                $message = 'El usuario "' . escape($username) . '" ya existía. Se actualizó la contraseña y el rol.';
            } else {
                $error = 'No se pudo actualizar el usuario existente. Verifique la base de datos.';
            }
        } else {
            $stmt = db_prepare('INSERT INTO usuarios (Username, PasswordHash, Rol, Fecha, Status) VALUES (?, ?, ?, ?, "Activo")');
            if ($stmt) {
                $stmt->bind_param('ssss', $username, $passwordHash, $rol, $fecha);
                if ($stmt->execute()) {
                    $message = 'Administrador creado correctamente. Ahora puedes iniciar sesión.';
                } else {
                    $error = 'No se pudo crear el usuario administrador: ' . db_connect()->error;
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Inicial - <?php echo SITE_TITLE; ?></title>
    <link rel="stylesheet" href="estilos_globales.css">
    <style>
        body { display:flex; justify-content:center; align-items:center; min-height:100vh; background: linear-gradient(135deg, #0f172a, #1e293b); }
        .setup-panel { width:min(520px,95%); background:rgba(255,255,255,0.93); border-radius:24px; padding:36px; box-shadow:0 30px 80px rgba(15,23,42,.2); }
        .setup-panel h1 { margin-bottom:1rem; }
        .setup-panel label { display:block; margin:20px 0 8px; font-weight:600; }
        .setup-panel input { width:100%; padding:14px 16px; border-radius:14px; border:1px solid rgba(15,23,42,.12); background:#f8fafc; }
        .setup-panel button { width:100%; margin-top:24px; padding:14px; border:none; border-radius:14px; background:linear-gradient(135deg,#0ea5e9,#ec4899); color:white; font-weight:700; cursor:pointer; }
        .message { padding:14px; border-radius:14px; margin-bottom:20px; }
        .success { background:rgba(16,185,129,.12); color:#166534; }
        .danger { background:rgba(239,68,68,.12); color:#7f1d1d; }
    </style>
</head>
<body>
    <div class="setup-panel">
        <h1>Configuración inicial</h1>
        <p>Este panel crea la primera cuenta administrativa y prepara la base de datos automáticamente si aún no está inicializada.</p>

        <?php if ($message): ?>
            <div class="message success"><?php echo escape($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message danger"><?php echo escape($error); ?></div>
        <?php endif; ?>

        <form method="post" action="setup.php">
            <label>Usuario administrador</label>
            <input type="text" name="username" required value="<?php echo escape($_POST['username'] ?? 'admin'); ?>">
            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="Admin123!">
            <label>Rol</label>
            <input type="text" name="rol" required value="Administrador">
            <button type="submit">Ejecutar migración y crear admin</button>
        </form>
    </div>
</body>
</html>
