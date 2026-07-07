<?php
require_once("functions.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user'])) {
    redirect('landing.php');
}
$user = current_user();
if (($user['Rol'] ?? '') === 'Estudiante') {
    redirect('student_dashboard.php');
}
if (($user['Rol'] ?? '') === 'Docente') {
    redirect('teacher_dashboard.php');
}

function get_count($sql) {
    $result = db_query($sql);
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return (int)($row['total'] ?? 0);
}

$count_est = get_count("SELECT COUNT(*) as total FROM estudiantes");
$count_cur = get_count("SELECT COUNT(*) as total FROM cursos");
$count_usu = get_count("SELECT COUNT(*) as total FROM usuarios WHERE Status = 'Activo'");
$count_ins = get_count("SELECT COUNT(*) as total FROM inscripciones");
$count_cohorte = get_count("SELECT COUNT(*) as total FROM cohortes");

$total_ingresos = (float)(mysqli_fetch_assoc(db_query("SELECT COALESCE(SUM(Monto), 0) as total FROM ingresos"))['total'] ?? 0);
$total_gastos = (float)(mysqli_fetch_assoc(db_query("SELECT COALESCE(SUM(Monto), 0) as total FROM gastos"))['total'] ?? 0);
$total_global = $total_ingresos + $total_gastos;
$porcentaje_ingresos = $total_global > 0 ? round(($total_ingresos / $total_global) * 100, 1) : 0;
$porcentaje_gastos = $total_global > 0 ? round(($total_gastos / $total_global) * 100, 1) : 0;
$attendance_total = get_count("SELECT COUNT(*) as total FROM asistenciaestudiante");
$attendance_present = get_count("SELECT COUNT(*) as total FROM asistenciaestudiante WHERE Asistencia = 'Presente'");
$attendance_rate = $attendance_total > 0 ? round(($attendance_present / $attendance_total) * 100, 1) : 0;
$attendance_absent = max(0, $attendance_total - $attendance_present);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include("header.php"); ?>
<style>
.dashboard-container { padding: 40px; animation: fadeIn 0.6s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.dashboard-hero { background: linear-gradient(135deg, var(--surface-strong) 0%, var(--surface) 100%); border: 1px solid var(--border-color); border-radius: 28px; padding: 30px 32px; margin-bottom: 24px; box-shadow: var(--glass-shadow); display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.dashboard-hero h1 { margin: 0 0 8px; font-size: 1.9rem; color: var(--text-main); }
.dashboard-hero p { margin: 0; color: var(--text-muted); max-width: 740px; }
.dashboard-hero .hero-badge { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 10px 16px; border-radius: 999px; font-weight: 700; box-shadow: 0 10px 24px rgba(14,165,233,0.2); }
.dashboard-toolbar { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:24px; flex-wrap:wrap; }
.toolbar-title { display:flex; align-items:center; gap:8px; color:var(--text-main); font-weight:700; }
.view-switcher { display:flex; gap:10px; flex-wrap:wrap; }
.view-btn { border:1px solid var(--border-color); background:var(--surface); color:var(--text-main); padding:10px 14px; border-radius:999px; font-weight:600; cursor:pointer; transition:all .2s ease; }
.view-btn.active, .view-btn:hover { background: linear-gradient(135deg, var(--primary), var(--secondary)); color:white; box-shadow: 0 8px 18px rgba(14,165,233,0.18); }
.cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px; }
.card { background: var(--surface); backdrop-filter: blur(14px); border: 1px solid var(--border-color); padding: 24px; border-radius: 22px; box-shadow: var(--glass-shadow); display: flex; justify-content: space-between; align-items: center; transition: all 0.3s ease; position: relative; overflow: hidden; }
.card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--primary), var(--secondary)); opacity: 0; transition: opacity 0.3s; }
.card:hover { transform: translateY(-4px); box-shadow: 0 18px 45px var(--shadow-color); background: var(--surface-strong); }
.card:hover::before { opacity: 1; }
.card-info h2 { margin: 0; font-size: 34px; font-weight: 800; color: var(--text-main); letter-spacing: -0.8px; }
.card-info p { margin: 6px 0 0; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.08em; }
.card-icon { font-size: 44px; color: var(--primary); opacity: 0.92; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0 4px 8px rgba(79, 70, 229, 0.18)); }
.view-panel { display:none; gap:20px; flex-wrap:wrap; }
.view-panel.active { display:flex; }
.chart-card, .insights-card { flex:1; min-width:300px; background: var(--surface); backdrop-filter: blur(14px); border: 1px solid var(--border-color); border-radius: 22px; padding: 24px; box-shadow: var(--glass-shadow); }
.chart-card h3, .insights-card h3 { margin:0 0 16px; color:var(--text-main); font-size:1.2rem; }
.chart-wrapper { position: relative; height: 320px; }
.insights-list { display:grid; gap:12px; }
.insight-item { display:flex; justify-content:space-between; align-items:center; padding:12px 14px; border-radius:14px; background:var(--surface-soft); color:var(--text-main); }
.insight-item strong { color:var(--primary); }
@media (max-width: 720px) { .dashboard-hero { flex-direction: column; align-items: flex-start; } .dashboard-container { padding: 24px 16px 40px; } }
</style>

<div class="dashboard-container">
    <div class="dashboard-hero">
        <div>
            <h1>Centro de control educativo</h1>
            <p>Monitorea matrículas, asistencias, finanzas, usuarios y actividades clave desde una vista moderna y rápida.</p>
        </div>
        <div class="hero-badge">Panel administrativo</div>
    </div>

    <div class="dashboard-toolbar">
        <div class="toolbar-title"><i class="fa-solid fa-sliders"></i> Vista dinámica</div>
        <div class="view-switcher">
            <button class="view-btn active" data-view="general">General</button>
            <button class="view-btn" data-view="finanzas">Finanzas</button>
            <button class="view-btn" data-view="asistencia">Asistencia</button>
            <button class="view-btn" data-view="operaciones">Operaciones</button>
        </div>
    </div>

    <div class="cards-grid">
        <div class="card"><div class="card-info"><h2><?php echo $count_est; ?></h2><p>Estudiantes</p></div><div class="card-icon"><i class="fa-solid fa-graduation-cap"></i></div></div>
        <div class="card"><div class="card-info"><h2><?php echo $count_cur; ?></h2><p>Cursos activos</p></div><div class="card-icon"><i class="fa-solid fa-book-open"></i></div></div>
        <div class="card"><div class="card-info"><h2><?php echo $count_ins; ?></h2><p>Inscripciones</p></div><div class="card-icon"><i class="fa-solid fa-file-signature"></i></div></div>
        <div class="card"><div class="card-info"><h2><?php echo $count_usu; ?></h2><p>Usuarios activos</p></div><div class="card-icon"><i class="fa-solid fa-user-shield"></i></div></div>
        <div class="card"><div class="card-info"><h2><?php echo $count_cohorte; ?></h2><p>Cohortes</p></div><div class="card-icon"><i class="fa-solid fa-layer-group"></i></div></div>
        <div class="card"><div class="card-info"><h2><?php echo $attendance_rate; ?>%</h2><p>Tasa de asistencia</p></div><div class="card-icon"><i class="fa-solid fa-chart-line"></i></div></div>
        <div class="card"><div class="card-info"><h2>$<?php echo number_format($total_ingresos, 2); ?></h2><p>Ingresos</p></div><div class="card-icon"><i class="fa-solid fa-arrow-trend-up"></i></div></div>
        <div class="card"><div class="card-info"><h2>$<?php echo number_format($total_gastos, 2); ?></h2><p>Gastos</p></div><div class="card-icon"><i class="fa-solid fa-arrow-trend-down"></i></div></div>
    </div>

    <div class="view-panel active" id="panel-general">
        <div class="chart-card">
            <h3>Resumen operativo</h3>
            <div class="chart-wrapper"><canvas id="overviewChart"></canvas></div>
        </div>
        <div class="insights-card">
            <h3>Indicadores clave</h3>
            <div class="insights-list">
                <div class="insight-item"><span>Estudiantes</span><strong><?php echo $count_est; ?></strong></div>
                <div class="insight-item"><span>Cursos activos</span><strong><?php echo $count_cur; ?></strong></div>
                <div class="insight-item"><span>Inscripciones</span><strong><?php echo $count_ins; ?></strong></div>
                <div class="insight-item"><span>Asistencia</span><strong><?php echo $attendance_rate; ?>%</strong></div>
            </div>
        </div>
    </div>

    <div class="view-panel" id="panel-finanzas">
        <div class="chart-card">
            <h3>Balance financiero</h3>
            <div class="chart-wrapper"><canvas id="financialChart"></canvas></div>
        </div>
        <div class="insights-card">
            <h3>Detalle</h3>
            <div class="insights-list">
                <div class="insight-item"><span>Ingresos</span><strong>$<?php echo number_format($total_ingresos, 2); ?></strong></div>
                <div class="insight-item"><span>Gastos</span><strong>$<?php echo number_format($total_gastos, 2); ?></strong></div>
                <div class="insight-item"><span>Participación de ingresos</span><strong><?php echo $porcentaje_ingresos; ?>%</strong></div>
                <div class="insight-item"><span>Participación de gastos</span><strong><?php echo $porcentaje_gastos; ?>%</strong></div>
            </div>
        </div>
    </div>

    <div class="view-panel" id="panel-asistencia">
        <div class="chart-card">
            <h3>Asistencia registrada</h3>
            <div class="chart-wrapper"><canvas id="attendanceChart"></canvas></div>
        </div>
        <div class="insights-card">
            <h3>Resumen</h3>
            <div class="insights-list">
                <div class="insight-item"><span>Registros</span><strong><?php echo $attendance_total; ?></strong></div>
                <div class="insight-item"><span>Presentes</span><strong><?php echo $attendance_present; ?></strong></div>
                <div class="insight-item"><span>Ausentes</span><strong><?php echo $attendance_absent; ?></strong></div>
                <div class="insight-item"><span>Rendimiento</span><strong><?php echo $attendance_rate; ?>%</strong></div>
            </div>
        </div>
    </div>

    <div class="view-panel" id="panel-operaciones">
        <div class="chart-card">
            <h3>Operaciones del sistema</h3>
            <div class="chart-wrapper"><canvas id="operationsChart"></canvas></div>
        </div>
        <div class="insights-card">
            <h3>Estado</h3>
            <div class="insights-list">
                <div class="insight-item"><span>Usuarios activos</span><strong><?php echo $count_usu; ?></strong></div>
                <div class="insight-item"><span>Cursos</span><strong><?php echo $count_cur; ?></strong></div>
                <div class="insight-item"><span>Cohortes</span><strong><?php echo $count_cohorte; ?></strong></div>
                <div class="insight-item"><span>Inscripciones</span><strong><?php echo $count_ins; ?></strong></div>
            </div>
        </div>
    </div>
</div>

<script>
    const viewButtons = document.querySelectorAll('.view-btn');
    const panels = document.querySelectorAll('.view-panel');
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            panels.forEach(panel => panel.classList.remove('active'));
            button.classList.add('active');
            document.getElementById('panel-' + button.dataset.view).classList.add('active');
        });
    });

    const overviewChart = new Chart(document.getElementById('overviewChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Estudiantes', 'Cursos', 'Inscripciones', 'Usuarios'],
            datasets: [{
                label: 'Volumen',
                data: [<?php echo $count_est; ?>, <?php echo $count_cur; ?>, <?php echo $count_ins; ?>, <?php echo $count_usu; ?>],
                backgroundColor: ['rgba(14,165,233,0.8)', 'rgba(236,72,153,0.8)', 'rgba(16,185,129,0.8)', 'rgba(249,115,22,0.8)'],
                borderRadius: 12
            }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
    });

    const financialChart = new Chart(document.getElementById('financialChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Ingresos', 'Gastos'],
            datasets: [{
                label: 'Montos',
                data: [<?php echo $total_ingresos; ?>, <?php echo $total_gastos; ?>],
                backgroundColor: ['rgba(34,197,94,0.85)', 'rgba(239,68,68,0.85)'],
                borderColor: ['rgba(16,185,129,1)', 'rgba(239,68,68,1)'],
                borderWidth: 2,
                hoverOffset: 12
            }]
        },
        options: { responsive:true, maintainAspectRatio:false, cutout:'65%', plugins:{legend:{position:'bottom'}} }
    });

    const attendanceChart = new Chart(document.getElementById('attendanceChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Presentes', 'Ausentes'],
            datasets: [{
                data: [<?php echo $attendance_present; ?>, <?php echo $attendance_absent; ?>],
                backgroundColor: ['rgba(34,197,94,0.9)', 'rgba(248,113,113,0.9)']
            }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
    });

    const operationsChart = new Chart(document.getElementById('operationsChart').getContext('2d'), {
        type: 'radar',
        data: {
            labels: ['Usuarios', 'Cursos', 'Cohortes', 'Inscripciones'],
            datasets: [{
                label: 'Indicadores',
                data: [<?php echo $count_usu; ?>, <?php echo $count_cur; ?>, <?php echo $count_cohorte; ?>, <?php echo $count_ins; ?>],
                backgroundColor: 'rgba(14,165,233,0.25)',
                borderColor: 'rgba(14,165,233,1)',
                borderWidth: 2
            }]
        },
        options: { responsive:true, maintainAspectRatio:false }
    });
</script>

<?php include("footer.php"); ?>
