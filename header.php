<?php
require_once __DIR__ . '/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (basename($_SERVER['PHP_SELF']) !== 'login.php' && basename($_SERVER['PHP_SELF']) !== 'setup.php' && basename($_SERVER['PHP_SELF']) !== 'landing.php') {
    require_login();
}
$user = current_user();
$role = $user['Rol'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
$admin_pages = ['estudiantes.php','docentes.php','usuarios.php','cursos.php','cohortes.php','horarios.php','sedes.php','empresa.php','inscripciones.php','asistenciaestudiante.php','asistenciadocente.php','pagosestudiantes.php','pagosdocente.php','ingresos.php','gastos.php','all_data.php','list_tables.php'];
if ($role !== 'Administrador' && in_array($current_page, $admin_pages, true)) {
    redirect(role_home($role));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="theme.js"></script>
    <title><?php echo escape(SITE_TITLE); ?></title>
    <link rel="stylesheet" href="table.css">
    <link rel="stylesheet" href="estilos_globales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root { --sidebar-width: 280px; }
        body { margin: 0; display: flex; font-family: 'Inter', sans-serif; min-height: 100vh; background: transparent; }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px var(--shadow-color);
            z-index: 10;
        }

        .sidebar-header {
            padding: 28px 24px;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            border-bottom: 1px solid var(--sidebar-border);
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-menu { list-style: none; padding: 20px 12px; margin: 0; overflow-y: auto; }
        .sidebar-menu li { margin-bottom: 5px; }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 14px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: transform 0.25s ease, background 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .sidebar-menu li a::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(14,165,233,0.12), rgba(236,72,153,0.12));
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a:focus-visible {
            background: var(--surface-strong);
            color: var(--primary);
            box-shadow: 0 6px 24px var(--shadow-color);
            transform: translateX(4px);
        }
        .sidebar-menu li a:hover::before,
        .sidebar-menu li a:focus-visible::before {
            opacity: 1;
        }
        .sidebar-menu i { width: 32px; font-size: 1.1rem; }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            position: relative;
            min-height: 100vh;
        }

        .top-navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(10px);
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            border-bottom: 1px solid var(--navbar-border);
            position: sticky;
            top: 0;
            z-index: 5;
            gap: 1rem;
        }
        .navbar-info { display: flex; align-items: center; gap: 14px; }
        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 9999px;
            background: rgba(14,165,233,0.14);
            color: var(--text-main);
            font-weight: 600;
            border: 1px solid rgba(14,165,233,0.18);
        }
        .accent-pill {
            background: rgba(236,72,153,0.16);
            color: var(--secondary);
        }
        .theme-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--surface-strong);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 999px;
            padding: 10px 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .theme-toggle:hover { transform: translateY(-1px); box-shadow: 0 8px 20px var(--shadow-color); }
        .content-padding { padding: 40px; max-width: 1400px; margin: 0 auto; width: 100%; }

        @media (max-width: 900px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--sidebar-border); }
            .top-navbar { padding: 16px 20px; flex-wrap: wrap; }
            .content-padding { padding: 24px 20px 40px; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><span><?php echo escape(SITE_TITLE); ?></span></div>
        <ul class="sidebar-menu">
            <?php if ($role === 'Administrador'): ?>
                <li><a href="index.php"><i class="fa-solid fa-house"></i><span>Inicio</span></a></li>
                <li><a href="estudiantes.php"><i class="fa-solid fa-users"></i><span>Estudiantes</span></a></li>
                <li><a href="docentes.php"><i class="fa-solid fa-chalkboard-user"></i> <span>Docentes</span></a></li>
                <li><a href="usuarios.php"><i class="fa-solid fa-user"></i> <span>Usuarios</span></a></li>
                <li><a href="cursos.php"><i class="fa-solid fa-box"></i><span>Cursos</span></a></li>
                <li><a href="cohortes.php"><i class="fa-solid fa-layer-group"></i><span>Cohortes</span></a></li>
                <li><a href="horarios.php"><i class="fa-solid fa-clock"></i><span>Horarios</span></a></li>
                <li><a href="sedes.php"><i class="fa-solid fa-building"></i><span>Sedes</span></a></li>
                <li><a href="empresa.php"><i class="fa-solid fa-city"></i><span>Empresa</span></a></li>
                <li><a href="inscripciones.php"><i class="fa-solid fa-file-signature"></i><span>Inscripciones</span></a></li>
                <li><a href="asistenciaestudiante.php"><i class="fa-solid fa-clipboard-user"></i><span>Asistencia Estudiantes</span></a></li>
                <li><a href="asistenciadocente.php"><i class="fa-solid fa-clipboard-check"></i><span>Asistencia Docentes</span></a></li>
                <li><a href="pagosestudiantes.php"><i class="fa-solid fa-money-check-dollar"></i><span>Pagos Estudiantes</span></a></li>
                <li><a href="pagosdocente.php"><i class="fa-solid fa-money-check"></i><span>Pagos Docentes</span></a></li>
                <li><a href="ingresos.php"><i class="fa-solid fa-arrow-trend-up"></i><span>Ingresos</span></a></li>
                <li><a href="gastos.php"><i class="fa-solid fa-arrow-trend-down"></i> <span>Gastos</span></a></li>
                <li><a href="all_data.php"><i class="fa-solid fa-database"></i><span>Todos los Datos</span></a></li>
            <?php elseif ($role === 'Docente'): ?>
                <li><a href="index.php"><i class="fa-solid fa-house"></i><span>Inicio</span></a></li>
                <li><a href="teacher_dashboard.php"><i class="fa-solid fa-chalkboard"></i><span>Mi panel</span></a></li>
                <li><a href="mis_cursos.php"><i class="fa-solid fa-book-open"></i><span>Mis cursos</span></a></li>
                <li><a href="asistenciadocente.php"><i class="fa-solid fa-clipboard-check"></i><span>Asistencia</span></a></li>
            <?php elseif ($role === 'Estudiante'): ?>
                <li><a href="index.php"><i class="fa-solid fa-house"></i><span>Inicio</span></a></li>
                <li><a href="student_dashboard.php"><i class="fa-solid fa-graduation-cap"></i><span>Mi panel</span></a></li>
                <li><a href="mis_clases.php"><i class="fa-solid fa-video"></i><span>Mis clases</span></a></li>
                <li><a href="asistenciaestudiante.php"><i class="fa-solid fa-clipboard-user"></i><span>Asistencia</span></a></li>
                <li><a href="pagosestudiantes.php"><i class="fa-solid fa-money-check-dollar"></i><span>Pagos</span></a></li>
            <?php endif; ?>
            <li style="margin-top: auto;"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>Cerrar Sesión</span></a></li>
        </ul>
    </aside>
    <main class="main-content">
        <header class="top-navbar">
            <div class="navbar-info">
                <div class="user-pill">
                    <i class="fa-solid fa-user-check"></i>
                    <?php echo escape($user['Username'] ?? $user['Rol'] ?? 'Sin sesión'); ?> • <?php echo escape($user['Rol'] ?? 'Usuario'); ?>
                </div>
            </div>
            <div class="navbar-info">
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="Cambiar tema">
                    <i class="fa-solid fa-moon"></i>
                    <span>Modo oscuro</span>
                </button>
                <span class="user-pill accent-pill"><?php echo escape($role ?: 'Usuario'); ?></span>
            </div>
        </header>
        <div class="content-padding">