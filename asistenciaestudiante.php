<?php 
require_once("conexion.php");


// --- CARGAR DATOS PARA LOS SELECTS ---
$res_estudiantes = mysqli_query($conn, "SELECT Estudiantes_ID, Nombre, Apellido, CI FROM ESTUDIANTES");
$res_cursos = mysqli_query($conn, "SELECT Curso_ID, Diplomado FROM CURSOS");

if (isset($_GET['eliminar_est']) && isset($_GET['eliminar_cur'])) {
    $id_est = (int)$_GET['eliminar_est'];
    $id_cur = (int)$_GET['eliminar_cur'];
    mysqli_query($conn, "DELETE FROM asistenciaestudiante WHERE Estudiante_ID = $id_est AND Curso_ID = $id_cur");
    header("Location: asistenciaestudiante.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estudiante_id'])) {
    $estudiante_id = $_POST['estudiante_id'];
    $curso_id = $_POST['curso_id'];
    $asistencia = $_POST['asistencia'];
    
    $old_est = $_POST['old_estudiante_id'];
    $old_cur = $_POST['old_curso_id'];

    if (!empty($old_est) && !empty($old_cur)) {
        $sql = "UPDATE asistenciaestudiante SET Estudiante_ID='$estudiante_id', Curso_ID='$curso_id', Asistencia='$asistencia' WHERE Estudiante_ID=$old_est AND Curso_ID=$old_cur";
    } else {
        $sql = "INSERT INTO asistenciaestudiante (Estudiante_ID, Curso_ID, Asistencia) VALUES ('$estudiante_id', '$curso_id', '$asistencia')";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: asistenciaestudiante.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

    <?php include("header.php"); ?>
<div class="header-actions">
        <button class="add-button" onclick="openModal()">
            <i class="fa-solid fa-clipboard-user"></i> Nueva Asistencia
        </button>
    </div>

    <h1>Lista de Asistencia de Estudiantes</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Curso / Diplomado</th>
                    <th>Asistencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $query_main = "SELECT a.*, e.Nombre, e.Apellido, e.CI, c.Diplomado 
                               FROM asistenciaestudiante a
                               LEFT JOIN ESTUDIANTES e ON a.Estudiante_ID = e.Estudiantes_ID
                               LEFT JOIN CURSOS c ON a.Curso_ID = c.Curso_ID";
                $result = mysqli_query($conn, $query_main);
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)) { 
                        $statusClass = $row["Asistencia"] == 'Presente' ? 'success' : 'danger';
                ?>
                    <tr>
                        <td><?php echo $row["Nombre"] ? $row["Nombre"].' '.$row["Apellido"].' ('.$row["CI"].')' : '<span style="color:#aaa;">N/A</span>'; ?></td>
                        <td><?php echo $row["Diplomado"] ? $row["Diplomado"] : '<span style="color:#aaa;">N/A</span>'; ?></td>
                        <td><span class="badge <?php echo $statusClass; ?>"><?php echo $row["Asistencia"]; ?></span></td>
                        <td>
                            <i class="fa-solid fa-pen-to-square" onclick='editAsistencia(<?php echo json_encode($row); ?>)'></i>
                            &nbsp;&nbsp;
                            <a href="asistenciaestudiante.php?eliminar_est=<?php echo $row['Estudiante_ID']; ?>&eliminar_cur=<?php echo $row['Curso_ID']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta asistencia?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } 
                } else {
                    echo "<tr><td colspan='4'>No hay asistencia de estudiantes registrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nueva Asistencia Estudiante</h2>
            <hr>
            <form method="post" action="">
                <input type="hidden" id="old_estudiante_id" name="old_estudiante_id">
                <input type="hidden" id="old_curso_id" name="old_curso_id">
                
                <label for="estudiante_id">Estudiante:</label>
                <select id="input_estudiante_id" name="estudiante_id" required>
                    <option value="">Seleccione un estudiante...</option>
                    <?php mysqli_data_seek($res_estudiantes, 0); while($e = mysqli_fetch_assoc($res_estudiantes)) { ?>
                        <option value="<?php echo $e['Estudiantes_ID']; ?>"><?php echo $e['Nombre'].' '.$e['Apellido'].' ('.$e['CI'].')'; ?></option>
                    <?php } ?>
                </select>
                
                <label for="curso_id">Curso / Diplomado:</label>
                <select id="input_curso_id" name="curso_id" required>
                    <option value="">Seleccione un curso...</option>
                    <?php mysqli_data_seek($res_cursos, 0); while($c = mysqli_fetch_assoc($res_cursos)) { ?>
                        <option value="<?php echo $c['Curso_ID']; ?>"><?php echo $c['Diplomado']; ?></option>
                    <?php } ?>
                </select>
                
                <label for="asistencia">Asistencia:</label>
                <select id="input_asistencia" name="asistencia" required>
                    <option value="Presente">Presente</option>
                    <option value="Ausente">Ausente</option>
                </select>
                
                <input type="submit" id="submitBtn" value="Guardar Asistencia">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('addModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Registrar Asistencia";
            document.getElementById('old_estudiante_id').value = "";
            document.getElementById('old_curso_id').value = "";
            document.getElementById('input_estudiante_id').value = "";
            document.getElementById('input_curso_id').value = "";
            document.getElementById('input_asistencia').value = "Presente";
            document.getElementById('submitBtn').value = "Guardar Asistencia";
            
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editAsistencia(datos) {
            document.getElementById('modalTitle').innerText = "Editar Asistencia";
            document.getElementById('old_estudiante_id').value = datos.Estudiante_ID;
            document.getElementById('old_curso_id').value = datos.Curso_ID;
            document.getElementById('input_estudiante_id').value = datos.Estudiante_ID;
            document.getElementById('input_curso_id').value = datos.Curso_ID;
            document.getElementById('input_asistencia').value = datos.Asistencia;
            document.getElementById('submitBtn').value = "Actualizar Cambios";
            
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeModal() { 
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }
        window.onclick = function(event) { if (event.target == modal) closeModal(); }
    </script>

<?php include("footer.php"); ?>

