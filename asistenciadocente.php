<?php 
require_once("conexion.php");


// --- CARGAR DATOS PARA LOS SELECTS ---
$res_docentes = mysqli_query($conn, "SELECT Docente_ID, Nombre, Apellido, CI FROM DOCENTES");
$res_cursos = mysqli_query($conn, "SELECT Curso_ID, Diplomado FROM CURSOS");

if (isset($_GET['eliminar_doc']) && isset($_GET['eliminar_cur'])) {
    $id_doc = (int)$_GET['eliminar_doc'];
    $id_cur = (int)$_GET['eliminar_cur'];
    mysqli_query($conn, "DELETE FROM asistenciadocente WHERE Docente_ID = $id_doc AND Curso_ID = $id_cur");
    header("Location: asistenciadocente.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['docente_id'])) {
    $docente_id = $_POST['docente_id'];
    $curso_id = $_POST['curso_id'];
    $clase_vista = $_POST['clase_vista'];
    $actividad = $_POST['actividad'];
    $compromiso = $_POST['compromiso'];
    
    $old_doc = $_POST['old_docente_id'];
    $old_cur = $_POST['old_curso_id'];

    if (!empty($old_doc) && !empty($old_cur)) {
        $sql = "UPDATE asistenciadocente SET Docente_ID='$docente_id', Curso_ID='$curso_id', Clase_vista='$clase_vista', Actividad='$actividad', Compromiso='$compromiso' WHERE Docente_ID=$old_doc AND Curso_ID=$old_cur";
    } else {
        $sql = "INSERT INTO asistenciadocente (Docente_ID, Curso_ID, Clase_vista, Actividad, Compromiso) VALUES ('$docente_id', '$curso_id', '$clase_vista', '$actividad', '$compromiso')";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: asistenciadocente.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

    <?php include("header.php"); ?>
<div class="header-actions">
        <button class="add-button" onclick="openModal()">
            <i class="fa-solid fa-clipboard-check"></i> Nueva Asistencia Docente
        </button>
    </div>

    <h1>Lista de Asistencia de Docentes</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Curso / Diplomado</th>
                    <th>Clase Vista</th>
                    <th>Actividad</th>
                    <th>Compromiso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $query_main = "SELECT a.*, d.Nombre, d.Apellido, d.CI, c.Diplomado 
                               FROM asistenciadocente a
                               LEFT JOIN DOCENTES d ON a.Docente_ID = d.Docente_ID
                               LEFT JOIN CURSOS c ON a.Curso_ID = c.Curso_ID";
                $result = mysqli_query($conn, $query_main);
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row["Nombre"] ? $row["Nombre"].' '.$row["Apellido"].' ('.$row["CI"].')' : '<span style="color:#aaa;">N/A</span>'; ?></td>
                        <td><?php echo $row["Diplomado"] ? $row["Diplomado"] : '<span style="color:#aaa;">N/A</span>'; ?></td>
                        <td><?php echo $row["Clase_vista"]; ?></td>
                        <td><?php echo $row["Actividad"]; ?></td>
                        <td><?php echo $row["Compromiso"]; ?></td>
                        <td>
                            <i class="fa-solid fa-pen-to-square" onclick='editAsistencia(<?php echo json_encode($row); ?>)'></i>
                            &nbsp;&nbsp;
                            <a href="asistenciadocente.php?eliminar_doc=<?php echo $row['Docente_ID']; ?>&eliminar_cur=<?php echo $row['Curso_ID']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta asistencia?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } 
                } else {
                    echo "<tr><td colspan='6'>No hay asistencia de docentes registrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nueva Asistencia Docente</h2>
            <hr>
            <form method="post" action="">
                <input type="hidden" id="old_docente_id" name="old_docente_id">
                <input type="hidden" id="old_curso_id" name="old_curso_id">
                
                <label for="docente_id">Docente:</label>
                <select id="input_docente_id" name="docente_id" required>
                    <option value="">Seleccione un docente...</option>
                    <?php mysqli_data_seek($res_docentes, 0); while($d = mysqli_fetch_assoc($res_docentes)) { ?>
                        <option value="<?php echo $d['Docente_ID']; ?>"><?php echo $d['Nombre'].' '.$d['Apellido'].' ('.$d['CI'].')'; ?></option>
                    <?php } ?>
                </select>
                
                <label for="curso_id">Curso / Diplomado:</label>
                <select id="input_curso_id" name="curso_id" required>
                    <option value="">Seleccione un curso...</option>
                    <?php mysqli_data_seek($res_cursos, 0); while($c = mysqli_fetch_assoc($res_cursos)) { ?>
                        <option value="<?php echo $c['Curso_ID']; ?>"><?php echo $c['Diplomado']; ?></option>
                    <?php } ?>
                </select>
                
                <label for="clase_vista">Clase Vista:</label>
                <input type="text" id="input_clase_vista" name="clase_vista" placeholder="Ej. Introducción a HTML" required>
                
                <label for="actividad">Actividad:</label>
                <input type="text" id="input_actividad" name="actividad" placeholder="Ej. Taller Práctico" required>
                
                <label for="compromiso">Compromiso:</label>
                <input type="text" id="input_compromiso" name="compromiso" placeholder="Ej. Traer material la próxima clase" required>
                
                <input type="submit" id="submitBtn" value="Guardar Asistencia">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('addModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Registrar Asistencia";
            document.getElementById('old_docente_id').value = "";
            document.getElementById('old_curso_id').value = "";
            document.getElementById('input_docente_id').value = "";
            document.getElementById('input_curso_id').value = "";
            document.getElementById('input_clase_vista').value = "";
            document.getElementById('input_actividad').value = "";
            document.getElementById('input_compromiso').value = "";
            document.getElementById('submitBtn').value = "Guardar Asistencia";
            
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editAsistencia(datos) {
            document.getElementById('modalTitle').innerText = "Editar Asistencia";
            document.getElementById('old_docente_id').value = datos.Docente_ID;
            document.getElementById('old_curso_id').value = datos.Curso_ID;
            document.getElementById('input_docente_id').value = datos.Docente_ID;
            document.getElementById('input_curso_id').value = datos.Curso_ID;
            document.getElementById('input_clase_vista').value = datos.Clase_vista;
            document.getElementById('input_actividad').value = datos.Actividad;
            document.getElementById('input_compromiso').value = datos.Compromiso;
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

