<?php
require_once 'functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user'])) {
    redirect('login.php');
}
$user = current_user();
if (($user['Rol'] ?? '') !== 'Estudiante') {
    redirect('index.php');
}
include 'header.php';
?>
<style>
.student-shell { display:grid; gap:20px; }
.student-hero { background: linear-gradient(135deg, var(--surface-strong) 0%, var(--surface) 100%); border: 1px solid var(--border-color); border-radius: 26px; padding: 28px; box-shadow: var(--glass-shadow); display:flex; justify-content:space-between; align-items:center; gap:1rem; }
.student-hero h1 { margin:0 0 8px; font-size:1.8rem; }
.student-hero p { margin:0; color:var(--text-muted); }
.student-badge { background: linear-gradient(135deg, var(--primary), var(--secondary)); color:white; padding:10px 14px; border-radius:999px; font-weight:700; }
.student-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:20px; }
.student-card { background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px; padding: 20px; box-shadow: var(--glass-shadow); }
.student-card h3 { margin:0 0 10px; font-size:1rem; color:var(--text-main); }
.student-card .value { font-size: 1.7rem; font-weight: 800; color: var(--primary); }
.student-card .meta { color: var(--text-muted); font-size:0.95rem; }
.student-panel { background: var(--surface); border: 1px solid var(--border-color); border-radius: 22px; padding: 24px; box-shadow: var(--glass-shadow); }
.student-panel h3 { margin-bottom: 14px; }
.student-list { display:grid; gap:10px; }
.student-list-item { display:flex; justify-content:space-between; align-items:center; padding:12px 14px; border-radius: 14px; background: var(--surface-soft); }
.status-pill { display:inline-block; padding:6px 10px; border-radius:999px; background: rgba(16,185,129,0.14); color: var(--success); font-weight:700; font-size:0.8rem; }
@media (max-width: 720px) { .student-hero { flex-direction: column; align-items:flex-start; } }
</style>
<div class="dashboard-container">
    <div class="student-shell">
        <div class="student-hero">
            <div>
                <h1>Hola, <?php echo escape($user['Username'] ?? 'estudiante'); ?></h1>
                <p>Tu portal personal para revisar clases, asistencia, pagos y tu progreso en un solo lugar.</p>
            </div>
            <div class="student-badge">Portal del estudiante</div>
        </div>

        <div class="student-grid">
            <div class="student-card"><h3>Clases activas</h3><div class="value">08</div><div class="meta">Próximas sesiones esta semana</div></div>
            <div class="student-card"><h3>Asistencia</h3><div class="value">96%</div><div class="meta">Excelente seguimiento mensual</div></div>
            <div class="student-card"><h3>Pagos</h3><div class="value">Al día</div><div class="meta">Sin pendientes registrados</div></div>
            <div class="student-card"><h3>Progreso</h3><div class="value">+12%</div><div class="meta">Comparado con el mes anterior</div></div>
        </div>

        <div class="student-panel">
            <h3>Próximas clases</h3>
            <div class="table-container">
                <table>
                    <thead><tr><th>Curso</th><th>Docente</th><th>Hora</th><th>Acción</th></tr></thead>
                    <tbody>
                        <tr><td>Matemática aplicada</td><td>Prof. Ana García</td><td>09:00 AM</td><td><a href="#">Entrar</a></td></tr>
                        <tr><td>Programación</td><td>Prof. Luis Pérez</td><td>11:30 AM</td><td><a href="#">Entrar</a></td></tr>
                        <tr><td>Inglés</td><td>Prof. Carla Ríos</td><td>03:00 PM</td><td><a href="#">Entrar</a></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="student-panel">
            <h3>Resumen del día</h3>
            <div class="student-list">
                <div class="student-list-item"><span>Revisión de ejercicios</span><span class="status-pill">Listo</span></div>
                <div class="student-list-item"><span>Asistencia marcada</span><span class="status-pill">Confirmada</span></div>
                <div class="student-list-item"><span>Pago mensual</span><span class="status-pill">Actualizado</span></div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
