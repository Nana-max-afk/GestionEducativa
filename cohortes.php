<?php 
require_once("conexion.php");

// --- 1. LÓGICA PARA ELIMINAR ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    
    $sql_check = "SELECT COUNT(*) as total FROM inscripciones WHERE Cohorte_ID = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id_eliminar);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($res_check);
    mysqli_stmt_close($stmt_check);

    if ($row_check['total'] > 0) {
        echo "<script>alert('No se puede borrar la cohorte: existen inscripciones asociadas a ella.'); window.location='cohortes.php';</script>";
        exit();
    }

    $sql_del = "DELETE FROM cohortes WHERE Cohorte_ID = ?";
    $stmt_del = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt_del, "i", $id_eliminar);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
    header("Location: cohortes.php");
    exit();
}

// --- 2. LÓGICA PARA INSERTAR O EDITAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fecha_inicio'])) {
    $fecha_inicio = $_POST['fecha_inicio'];
    $id_edit = $_POST['cohorte_id_edit'];

    if (!empty($id_edit)) {
        $id_edit = (int)$id_edit;
        $sql = "UPDATE cohortes SET Fecha_de_inicio=? WHERE Cohorte_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $fecha_inicio, $id_edit);
    } else {
        $sql = "INSERT INTO cohortes (Fecha_de_inicio) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $fecha_inicio);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: cohortes.php");
        exit();
    } else {
        echo "Error al procesar la cohorte.";
    }
}

include("header.php");
$res_cohortes_lista = mysqli_query($conn, "SELECT * FROM cohortes");
?>

    <div class="header-actions">
        <button class="add-button" onclick="openModal()">Nueva Cohorte</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha de Inicio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($res_cohortes_lista)) { ?>
            <tr>
                <td><?php echo $row['Cohorte_ID']; ?></td>
                <td><?php echo $row['Fecha_de_inicio']; ?></td>
                <td>
                    <a href="javascript:void(0);" onclick='editCohorte(<?php echo json_encode($row); ?>)' style="margin-right: 10px; color: #2196F3;"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:void(0);" onclick="if(confirm('¿Eliminar esta cohorte?')) window.location.href='cohortes.php?eliminar=<?php echo $row['Cohorte_ID']; ?>';" style="color: #e74c3c;"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="cohorteModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nueva Cohorte</h2>
            
            <form action="cohortes.php" method="POST">
                <input type="hidden" id="cohorte_id_edit" name="cohorte_id_edit">
                
                <label>Fecha de Inicio:</label>
                <input type="date" id="input_fecha" name="fecha_inicio" required>
                
                <input type="submit" id="submitBtn" class="btn-submit" value="Guardar">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('cohorteModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Nueva Cohorte";
            document.getElementById('cohorte_id_edit').value = "";
            document.getElementById('input_fecha').value = "";
            document.getElementById('submitBtn').value = "Registrar Cohorte";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editCohorte(datos) {
            document.getElementById('modalTitle').innerText = "Editar Cohorte";
            document.getElementById('cohorte_id_edit').value = datos.Cohorte_ID;
            document.getElementById('input_fecha').value = datos.Fecha_de_inicio;
            document.getElementById('submitBtn').value = "Actualizar Cohorte";
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