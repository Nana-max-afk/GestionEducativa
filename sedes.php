<?php 
require_once("conexion.php");

// --- 1. LÓGICA PARA ELIMINAR (CON PREPARED STATEMENTS) ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    
    // Primero verificamos si tiene estudiantes asociados para evitar el crash de MySQL
    $sql_check = "SELECT COUNT(*) as total FROM ESTUDIANTES WHERE Sede_ID = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id_eliminar);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($res_check);
    mysqli_stmt_close($stmt_check);

    if ($row_check['total'] > 0) {
        echo "<script>alert('No se puede eliminar la sede: tiene estudiantes asociados.'); window.location='sedes.php';</script>";
        exit();
    }

    $sql_del = "DELETE FROM SEDES WHERE Sede_ID = ?";
    $stmt_del = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt_del, "i", $id_eliminar);
    
    if (mysqli_stmt_execute($stmt_del)) {
        mysqli_stmt_close($stmt_del);
        header("Location: sedes.php");
        exit();
    } else {
        echo "<script>alert('No se puede eliminar la sede: tiene registros asociados.'); window.location='sedes.php';</script>";
    }
}

// --- 2. LÓGICA PARA INSERTAR O EDITAR (CON PREPARED STATEMENTS) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sede'])) {
    $nombre_sede = $_POST['sede'];
    $id_edit = $_POST['sede_id_edit'];

    if (!empty($id_edit)) {
        // ACTUALIZAR SEDE EXISTENTE
        $id_edit = (int)$id_edit;
        $sql = "UPDATE SEDES SET Sede=? WHERE Sede_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nombre_sede, $id_edit);
    } else {
        // INSERTAR NUEVA SEDE
        $sql = "INSERT INTO SEDES (Sede) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $nombre_sede);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: sedes.php");
        exit();
    } else {
        echo "Error al guardar los datos.";
    }
}

include("header.php");

// Listado de la tabla
$res_sedes_lista = mysqli_query($conn, "SELECT Sede_ID, Sede FROM SEDES");
?>

    <div class="header-actions">
        <button class="add-button" onclick="openModal()">Nueva Sede</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Sede</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($res_sedes_lista)) { ?>
            <tr>
                <td><?php echo $row['Sede_ID']; ?></td>
                <td><?php echo htmlspecialchars($row['Sede'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href="javascript:void(0);" onclick='editSede(<?php echo json_encode($row); ?>)' style="margin-right: 10px; color: #2196F3;"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:void(0);" onclick="if(confirm('¿Seguro que deseas eliminar esta sede?')) window.location.href='sedes.php?eliminar=<?php echo $row['Sede_ID']; ?>';" style="color: #e74c3c;"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="sedeModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nueva Sede</h2>
            
            <form action="sedes.php" method="POST">
                <input type="hidden" id="sede_id_edit" name="sede_id_edit">
                
                <label for="input_sede">Nombre de la Sede:</label>
                <input type="text" id="input_sede" name="sede" required placeholder="Ej. Sede Maracay">
                
                <input type="submit" id="submitBtn" class="btn-submit" value="Guardar Sede">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('sedeModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Nueva Sede";
            document.getElementById('sede_id_edit').value = "";
            document.getElementById('input_sede').value = "";
            document.getElementById('submitBtn').value = "Registrar Sede";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editSede(datos) {
            document.getElementById('modalTitle').innerText = "Editar Sede";
            document.getElementById('sede_id_edit').value = datos.Sede_ID;
            document.getElementById('input_sede').value = datos.Sede;
            document.getElementById('submitBtn').value = "Actualizar Sede";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeModal() { 
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }
        
        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }
    </script>

<?php include("footer.php"); ?>