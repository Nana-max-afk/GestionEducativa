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
      <h1>Mis cursos</h1>
      <p>Visualiza todo lo asignado a tu labor docente y mantén un seguimiento claro.</p>
    </div>
    <div class="hero-badge">Docente</div>
  </div>
  <div class="chart-container">
    <div class="table-container">
      <table>
        <thead><tr><th>Curso</th><th>Grupo</th><th>Horario</th><th>Estado</th></tr></thead>
        <tbody>
          <tr><td>Programación</td><td>Grupo A</td><td>Lunes 09:00</td><td><span class="badge success">Asignado</span></td></tr>
          <tr><td>Inglés</td><td>Grupo B</td><td>Miércoles 14:00</td><td><span class="badge info">Activo</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
