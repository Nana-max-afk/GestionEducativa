<?php
require_once 'functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_SESSION['user'])) {
    redirect(role_home($_SESSION['user']['Rol'] ?? 'Administrador'));
}
include 'header.php';
?>
<style>
.landing-shell { padding: 24px 0 40px; }
.hero { display:grid; grid-template-columns:1.1fr .9fr; gap:24px; align-items:center; }
.hero-card, .feature-card { background: linear-gradient(135deg, var(--surface-strong) 0%, var(--surface) 100%); border:1px solid var(--border-color); border-radius:24px; box-shadow: var(--glass-shadow); padding:28px; }
.hero h1 { font-size:2.3rem; margin-bottom:16px; }
.hero p { color: var(--text-muted); font-size:1.05rem; line-height:1.7; margin-bottom:20px; }
.hero-actions a { display:inline-block; margin-right:12px; margin-bottom:10px; padding:12px 18px; border-radius:999px; text-decoration:none; font-weight:700; }
.hero-actions .primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); color:white; }
.hero-actions .secondary { background: var(--surface-strong); color: var(--text-main); border:1px solid var(--border-color); }
.features { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-top:24px; }
.feature-card h3 { margin-bottom:10px; color: var(--text-main); }
.feature-card p { color: var(--text-muted); }
@media (max-width: 900px) { .hero { grid-template-columns: 1fr; } }
</style>
<div class="landing-shell">
    <section class="hero">
        <div class="hero-card">
            <h1>Gestión educativa moderna para escuelas y academias</h1>
            <p>Administra estudiantes, docentes, cursos, pagos, asistencias y clases desde un entorno elegante, seguro y preparado para crecer.</p>
            <div class="hero-actions">
                <a class="primary" href="setup.php">Comenzar instalación</a>
                <a class="secondary" href="login.php">Ingresar al sistema</a>
            </div>
        </div>
        <div class="hero-card">
            <h3>Lo que incluye</h3>
            <ul style="padding-left:20px; color:var(--text-muted); line-height:1.8;">
                <li>Panel administrativo completo</li>
                <li>Vistas para estudiantes y docentes</li>
                <li>Seguimiento de asistencias y pagos</li>
                <li>Clases online con experiencia organizada</li>
            </ul>
        </div>
    </section>

    <section class="features">
        <div class="feature-card"><h3>Panel administrativo</h3><p>Control total de usuarios, cursos, sedes, inscripciones y reportes.</p></div>
        <div class="feature-card"><h3>Portal del estudiante</h3><p>Acceso rápido a clases, calendario, asistencias y pagos del alumno.</p></div>
        <div class="feature-card"><h3>Portal del docente</h3><p>Vista enfocada en sus cursos asignados, horarios y seguimiento.</p></div>
        <div class="feature-card"><h3>Diseño profesional</h3><p>Interfaz moderna con modo claro/oscuro y estética consistente.</p></div>
    </section>
</div>
<?php include 'footer.php'; ?>
