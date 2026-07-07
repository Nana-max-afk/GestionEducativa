<?php 
require_once("conexion.php");

// --- CARGAR DATOS PARA LOS SELECTS ---
$res_cursos = mysqli_query($conn, "SELECT Curso_ID, Diplomado FROM CURSOS");

// --- LÓGICA PARA ELIMINAR (CON PREPARED STATEMENTS) ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    $stmt_del = mysqli_prepare($conn, "DELETE FROM HORARIOS WHERE Horario_ID = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $id_eliminar);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
    header("Location: horarios.php");
    exit();
}

// --- LÓGICA PARA INSERTAR O EDITAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['curso_id'])) {
    $curso_id = (int)$_POST['curso_id'];
    $dia_semana = $_POST['dia_semana'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    
    $fecha_inicio = $_POST['fecha_inicio'] == "" ? null : $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'] == "" ? null : $_POST['fecha_fin'];
    
    $id_edit = $_POST['horario_id_edit'];

    if (!empty($id_edit)) {
        $id_edit = (int)$id_edit;
        $sql = "UPDATE HORARIOS SET Curso_ID=?, Dia_Semana=?, Hora_Inicio=?, Hora_Fin=?, Fecha_Inicio=?, Fecha_Fin=? WHERE Horario_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssssi", $curso_id, $dia_semana, $hora_inicio, $hora_fin, $fecha_inicio, $fecha_fin, $id_edit);
    } else {
        $sql = "INSERT INTO HORARIOS (Curso_ID, Dia_Semana, Hora_Inicio, Hora_Fin, Fecha_Inicio, Fecha_Fin) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssss", $curso_id, $dia_semana, $hora_inicio, $hora_fin, $fecha_inicio, $fecha_fin);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: horarios.php");
        exit();
    } else {
        echo "Error al guardar el horario: " . mysqli_error($conn);
    }
}
?>

    <?php include("header.php"); ?>
    <div class="header-actions">
        <button class="add-button" onclick="openModal()">
            <i class="fa-solid fa-clock"></i> Nuevo Horario
        </button>
    </div>

    <h1>Lista de Horarios por Curso</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Horario ID</th>
                    <th>Curso / Diplomado</th>
                    <th>Día de la Semana</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $query_main = "SELECT h.*, c.Diplomado 
                               FROM HORARIOS h
                               LEFT JOIN CURSOS c ON h.Curso_ID = c.Curso_ID";
                $result = mysqli_query($conn, $query_main);
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)) { 
                        $f_inicio_fmt = $row["Fecha_Inicio"] ? date("d-m-Y", strtotime($row["Fecha_Inicio"])) : '<span style="color:#aaa;">Sin definir</span>';
                        $f_fin_fmt = $row["Fecha_Fin"] ? date("d-m-Y", strtotime($row["Fecha_Fin"])) : '<span style="color:#aaa;">Sin definir</span>';
                        ?>
                    <tr>
                        <td><?php echo $row["Horario_ID"]; ?></td>
                        <td><?php echo $row["Diplomado"] ? htmlspecialchars($row["Diplomado"], ENT_QUOTES, 'UTF-8') : '<span style="color:#aaa;">N/A</span>'; ?></td>
                        <td><span class="badge info"><?php echo htmlspecialchars($row["Dia_Semana"], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php echo htmlspecialchars($row["Hora_Inicio"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row["Hora_Fin"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo $f_inicio_fmt; ?></td>
                        <td><?php echo $f_fin_fmt; ?></td>
                        <td>
                            <i class="fa-solid fa-pen-to-square" onclick='editHorario(<?php echo json_encode($row); ?>)' style="color: #2196F3; cursor: pointer; margin-right: 10px;"></i>
                            <a href="horarios.php?eliminar=<?php echo $row['Horario_ID']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este horario?')" style="color: #e74c3c;">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } 
                } else {
                    echo "<tr><td colspan='8'>No hay horarios registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nuevo Horario</h2>
            <hr style="margin-bottom:15px; border:none; border-bottom:1px solid #eee;">
            
            <form method="post" action="horarios.php">
                <input type="hidden" id="horario_id_edit" name="horario_id_edit">
                
                <label for="input_curso_id">Curso / Diplomado:</label>
                <select id="input_curso_id" name="curso_id" required>
                    <option value="">Seleccione un curso...</option>
                    <?php mysqli_data_seek($res_cursos, 0); while($c = mysqli_fetch_assoc($res_cursos)) { ?>
                        <option value="<?php echo $c['Curso_ID']; ?>"><?php echo htmlspecialchars($c['Diplomado'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                </select>
                
                <label for="input_dia_semana">Día de la Semana:</label>
                <select id="input_dia_semana" name="dia_semana" required>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sábado">Sábado</option>
                    <option value="Domingo">Domingo</option>
                </select>
                
                <label for="input_hora_inicio">Hora de Inicio:</label>
                <input type="time" id="input_hora_inicio" name="hora_inicio" required>
                
                <label for="input_hora_fin">Hora de Fin:</label>
                <input type="time" id="input_hora_fin" name="hora_fin" required>

                <label for="input_fecha_inicio">Fecha de Inicio del Curso:</label>
                <input type="date" id="input_fecha_inicio" name="fecha_inicio">

                <label for="input_fecha_fin">Fecha de Finalización del Curso:</label>
                <input type="date" id="input_fecha_fin" name="fecha_fin">
                
                <input type="submit" id="submitBtn" class="btn-submit" value="Guardar Horario">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('addModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Registrar Horario";
            document.getElementById('horario_id_edit').value = "";
            document.getElementById('input_curso_id').value = "";
            document.getElementById('input_dia_semana').value = "Lunes";
            document.getElementById('input_hora_inicio').value = "";
            document.getElementById('input_hora_fin').value = "";
            document.getElementById('input_fecha_inicio').value = "";
            document.getElementById('input_fecha_fin').value = "";
            document.getElementById('submitBtn').value = "Guardar Horario";
            
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editHorario(datos) {
            document.getElementById('modalTitle').innerText = "Editar Horario";
            document.getElementById('horario_id_edit').value = datos.Horario_ID;
            document.getElementById('input_curso_id').value = datos.Curso_ID;
            document.getElementById('input_dia_semana').value = datos.Dia_Semana;
            document.getElementById('input_hora_inicio').value = datos.Hora_Inicio;
            document.getElementById('input_hora_fin').value = datos.Hora_Fin;
            document.getElementById('input_fecha_inicio').value = datos.Fecha_Inicio || "";
            document.getElementById('input_fecha_fin').value = datos.Fecha_Fin || "";
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
