<?php
require_once 'functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user'])) {
    redirect('login.php');
}
$user = current_user();
if (($user['Rol'] ?? '') !== 'Docente') {
    redirect('index.php');
}
include 'header.php';
?>
<style>
.teacher-shell { display:grid; gap:20px; }
.teacher-hero { background: linear-gradient(135deg, var(--surface-strong) 0%, var(--surface) 100%); border: 1px solid var(--border-color); border-radius: 26px; padding: 28px; box-shadow: var(--glass-shadow); display:flex; justify-content:space-between; align-items:center; gap:1rem; }
.teacher-hero h1 { margin:0 0 8px; font-size:1.8rem; }
.teacher-hero p { margin:0; color:var(--text-muted); }
.teacher-badge { background: linear-gradient(135deg, var(--primary), var(--secondary)); color:white; padding:10px 14px; border-radius:999px; font-weight:700; }
.teacher-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:20px; }
.teacher-card { background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px; padding: 20px; box-shadow: var(--glass-shadow); }
.teacher-card h3 { margin:0 0 10px; font-size:1rem; color:var(--text-main); }
.teacher-card .value { font-size: 1.7rem; font-weight: 800; color: var(--secondary); }
.teacher-card .meta { color: var(--text-muted); font-size:0.95rem; }
.teacher-panel { background: var(--surface); border: 1px solid var(--border-color); border-radius: 22px; padding:24px; box-shadow: var(--glass-shadow); }
.teacher-panel h3 { margin-bottom:14px; }
.teacher-list { display:grid; gap:10px; }
.teacher-list-item { display:flex; justify-content:space-between; align-items:center; padding:12px 14px; border-radius:14px; background: var(--surface-soft); }
@media (max-width: 720px) { .teacher-hero { flex-direction: column; align-items:flex-start; } }
</style>
<div class="dashboard-container">
    <div class="teacher-shell">
        <div class="teacher-hero">
            <div>
                <h1>Panel docente</h1>
                <p>Gestiona tus clases, asistencia y progreso del grupo de una forma clara, ordenada y visualmente agradable.</p>
            </div>
            <div class="teacher-badge">Portal del docente</div>
        </div>

        <div class="teacher-grid">
            <div class="teacher-card"><h3>Cursos asignados</h3><div class="value">04</div><div class="meta">Programación, Inglés, Matemáticas</div></div>
            <div class="teacher-card"><h3>Estudiantes</h3><div class="value">24</div><div class="meta">En seguimiento activo</div></div>
            <div class="teacher-card"><h3>Clases hoy</h3><div class="value">02</div><div class="meta">Una pendiente de confirmar</div></div>
            <div class="teacher-card"><h3>Asistencias</h3><div class="value">91%</div><div class="meta">Promedio del mes</div></div>
        </div>

        <div class="teacher-panel">
            <h3>Calendario de clases</h3>
            <div class="table-container">
                <table>
                    <thead><tr><th>Curso</th><th>Grupo</th><th>Horario</th><th>Estado</th></tr></thead>
                    <tbody>
                        <tr><td>Programación</td><td>Grupo A</td><td>09:00 AM</td><td><span class="badge success">Activo</span></td></tr>
                        <tr><td>Inglés</td><td>Grupo B</td><td>02:00 PM</td><td><span class="badge warning">Pendiente</span></td></tr>
                        <tr><td>Matemáticas</td><td>Grupo C</td><td>04:30 PM</td><td><span class="badge info">Próxima</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="teacher-panel">
            <h3>Acciones rápidas</h3>
            <div class="teacher-list">
                <div class="teacher-list-item"><span>Tomar asistencia</span><span class="badge info">Disponible</span></div>
                <div class="teacher-list-item"><span>Enviar recordatorio</span><span class="badge success">Enviado</span></div>
                <div class="teacher-list-item"><span>Actualizar agenda</span><span class="badge warning">Pendiente</span></div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
