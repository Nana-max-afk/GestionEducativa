<?php 
require_once("conexion.php");


// --- 1. CARGAR SEDES PARA EL SELECT ---
$res_sedes = mysqli_query($conn, "SELECT Sede_ID, Sede FROM SEDES");

// --- 2. LÓGICA PARA ELIMINAR ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    // Se elimina el curso (ojo: si tiene alumnos inscritos, MySQL podría dar error por FK)
    mysqli_query($conn, "DELETE FROM CURSOS WHERE Curso_ID = $id_eliminar");
    header("Location: cursos.php");
    exit();
}

// --- 3. LÓGICA PARA INSERTAR O EDITAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['diplomado'])) {
    $diplomado = $_POST['diplomado'];
    $area = $_POST['area'];
    $duracion = $_POST['duracion'];
    $costo = $_POST['costo'];
    $sede_id = $_POST['sede_id'];
    $id_edit = $_POST['curso_id_edit'];

    if (!empty($id_edit)) {
        // ACTUALIZAR CURSO EXISTENTE
        $sql = "UPDATE CURSOS SET 
                Diplomado='$diplomado', Area='$area', Duracion='$duracion', 
                Costo='$costo', Sede_ID='$sede_id' 
                WHERE Curso_ID=$id_edit";
    } else {
        // NUEVO CURSO (Aquí no hace falta crear usuario previo)
        $sql = "INSERT INTO CURSOS (Diplomado, Area, Duracion, Costo, Sede_ID) 
                VALUES ('$diplomado', '$area', '$duracion', '$costo', '$sede_id')";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: cursos.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<?php include("header.php"); ?>
<div class="header-actions">
        <button class="add-button" onclick="openModal()">
            <i class="fa-solid fa-book"></i> Nuevo Curso
        </button>
    </div>

    <h1>Lista de Cursos y Diplomados</h1>

    <div class="table-container">        <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre / Diplomado</th>
                <th>Área</th>
                <th>Costo ($)</th>
                <th>Sede</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Relacionamos CURSOS con SEDES para ver el nombre de la sede
            $res_cursos = mysqli_query($conn, "SELECT C.*, S.Sede FROM CURSOS C LEFT JOIN SEDES S ON C.Sede_ID = S.Sede_ID");
            while($row = mysqli_fetch_assoc($res_cursos)) { ?>
            <tr>
                <td><?php echo $row["Curso_ID"]; ?></td>
                <td><?php echo $row["Diplomado"]; ?></td>
                <td><?php echo $row["Area"]; ?></td>
                <td><?php echo number_format($row["Costo"], 2); ?></td>
                <td><?php echo $row["Sede"]; ?></td>
                <td>
                    <i class="fa-solid fa-pen-to-square" style="color: #2196F3; cursor:pointer; margin-right: 15px;" 
                       onclick='editCurso(<?php echo json_encode($row); ?>)'></i>
                    
                    <a href="cursos.php?eliminar=<?php echo $row['Curso_ID']; ?>" onclick="return confirm('¿Eliminar este curso?')">
                        <i class="fa-solid fa-trash" style="color: #f44336;"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>    </div>

    <div id="cursoModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nuevo Curso</h2>
            <hr>
            
            <form method="post" action="">
                <input type="hidden" id="curso_id_edit" name="curso_id_edit">
                
                <label>Nombre del Diplomado</label>
                <input type="text" id="input_diplomado" name="diplomado" placeholder="Ej: Desarrollo Web" required>
                
                <label>Área</label>
                <input type="text" id="input_area" name="area" placeholder="Ej: Tecnología" required>
                
                <label>Duración</label>
                <input type="text" id="input_duracion" name="duracion" placeholder="Ej: 6 Meses" required>

                <label>Costo ($)</label>
                <input type="number" step="0.01" id="input_costo" name="costo" placeholder="0.00" required>

                <label>Sede Asignada</label>
                <select id="input_sede" name="sede_id">
                    <?php mysqli_data_seek($res_sedes, 0); ?>
                    <?php while($s = mysqli_fetch_assoc($res_sedes)) { ?>
                        <option value="<?php echo $s['Sede_ID']; ?>"><?php echo $s['Sede']; ?></option>
                    <?php } ?>
                </select>

                <input type="submit" id="submitBtn" value="Guardar Curso">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('cursoModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Crear Nuevo Curso";
            document.getElementById('curso_id_edit').value = "";
            document.getElementById('submitBtn').value = "Guardar Curso";
            // Limpiar
            document.getElementById('input_diplomado').value = "";
            document.getElementById('input_area').value = "";
            document.getElementById('input_duracion').value = "";
            document.getElementById('input_costo').value = "";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editCurso(datos) {
            document.getElementById('modalTitle').innerText = "Editar Curso";
            document.getElementById('curso_id_edit').value = datos.Curso_ID;
            document.getElementById('input_diplomado').value = datos.Diplomado;
            document.getElementById('input_area').value = datos.Area;
            document.getElementById('input_duracion').value = datos.Duracion;
            document.getElementById('input_costo').value = datos.Costo;
            document.getElementById('input_sede').value = datos.Sede_ID;
            
            document.getElementById('submitBtn').value = "Actualizar Curso";
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


