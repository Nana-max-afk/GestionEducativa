<?php
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    redirect('index.php');
}

$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['_csrf'] ?? '';

    if (!validate_csrf($csrf)) {
        $error = 'Token de seguridad inválido. Intenta de nuevo.';
    } elseif ($username === '' || $password === '') {
        $error = 'Complete el usuario y la contraseña.';
    } else {
        $stmt = db_prepare('SELECT Usuario_ID, Username, PasswordHash, Rol, Fecha, Status FROM usuarios WHERE Username = ? AND Status = "Activo" LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && isset($user['PasswordHash']) && password_verify($password, $user['PasswordHash'])) {
                unset($user['PasswordHash']);
                login_user($user);
                redirect('index.php');
            }
        }
        $error = 'Usuario o contraseña incorrectos.';
    }

    if ($error) {
        flash($error, 'danger');
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="theme.js"></script>
    <title>Ingresar - <?php echo SITE_TITLE; ?></title>
    <link rel="stylesheet" href="estilos_globales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--bg-gradient); }
        body::before { content: ''; position: fixed; inset: 0; background: radial-gradient(circle at top left, var(--bg-radial-a), transparent 35%), radial-gradient(circle at bottom right, var(--bg-radial-b), transparent 40%); z-index: -1; }
        .login-shell { width: min(460px, 95%); }
        .login-panel { background: var(--surface-strong); border: 1px solid var(--border-color); border-radius: 24px; padding: 36px 32px; box-shadow: 0 30px 80px var(--shadow-color); backdrop-filter: blur(16px); }
        .login-panel h1 { margin-bottom: 1rem; font-size: 2rem; color: var(--text-main); }
        .login-panel p { color: var(--text-muted); margin-bottom: 24px; }
        .login-panel label { display:block; margin: 16px 0 8px; font-weight: 600; color: var(--text-main); }
        .login-panel input { width: 100%; padding: 14px 16px; border-radius: 14px; border: 1px solid var(--border-color); background: var(--input-bg); font-size: 0.95rem; color: var(--text-main); }
        .login-panel button { width: 100%; margin-top: 24px; padding: 14px; border-radius: 14px; border: none; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; font-weight: 700; cursor: pointer; }
        .login-panel .message { margin-bottom: 16px; padding: 14px 16px; border-radius: 14px; background: rgba(251,191,36,0.15); color: #92400e; }
        .theme-toggle { margin-bottom: 16px; display: inline-flex; align-items: center; gap: 8px; background: var(--surface-soft); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 999px; padding: 8px 12px; cursor: pointer; font-weight: 600; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .theme-toggle:hover { transform: translateY(-1px); box-shadow: 0 8px 20px var(--shadow-color); }
    </style>
</head>
<body>
    <div class="login-shell">
        <button type="button" class="theme-toggle" id="themeToggle" aria-label="Cambiar tema"><i class="fa-solid fa-moon"></i> <span>Modo oscuro</span></button>
        <div class="login-panel">
            <h1>Iniciar sesión</h1>
            <p>Accede al panel administrativo de <?php echo SITE_TITLE; ?> con credenciales seguras.</p>
            <?php if ($flash): ?>
                <div class="message"><?php echo escape($flash['message']); ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" autocomplete="username" required value="<?php echo escape($_POST['username'] ?? ''); ?>">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
                <button type="submit">Ingresar</button>
            </form>
        </div>
    </div>
</body>
</html>
