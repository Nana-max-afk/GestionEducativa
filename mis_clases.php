<?php
require_once 'functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user'])) {
    redirect('login.php');
}
include 'header.php';
?>
<div class="dashboard-container">
  <div class="dashboard-hero">
    <div>
      <h1>Mis clases</h1>
      <p>Consulta tus sesiones virtuales, recursos y próximos encuentros.</p>
    </div>
    <div class="hero-badge">Estudiante</div>
  </div>
  <div class="chart-container">
    <div class="table-container">
      <table>
        <thead><tr><th>Clase</th><th>Docente</th><th>Horario</th><th>Acceso</th></tr></thead>
        <tbody>
          <tr><td>Matemática aplicada</td><td>Ana García</td><td>09:00 AM</td><td><a href="#">Unirse</a></td></tr>
          <tr><td>Programación</td><td>Luis Pérez</td><td>11:30 AM</td><td><a href="#">Unirse</a></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
