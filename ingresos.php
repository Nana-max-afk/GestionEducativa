<?php 
require_once("conexion.php");


// --- 1. BUSCAR PAGOS DISPONIBLES ---
$res_pagos_disponibles = mysqli_query($conn, "SELECT Pago_ID, Referencia FROM PagosEstudiantes");

// --- 2. LÓGICA PARA ELIMINAR ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    mysqli_query($conn, "DELETE FROM Ingresos WHERE Ingresos_ID = $id_eliminar");
    header("Location: ingresos.php");
    exit();
}

// --- 3. LÓGICA PARA INSERTAR O EDITAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pago_id'])) {
    $pago_id = $_POST['pago_id'] == "" ? "NULL" : $_POST['pago_id'];
    $monto = $_POST['monto'];
    $id_edit = $_POST['ingreso_id_edit'];

    if (!empty($id_edit)) {
        // ACTUALIZAR
        $sql = "UPDATE Ingresos SET Pago_ID=$pago_id, Monto='$monto' WHERE Ingresos_ID=$id_edit";
    } else {
        // INSERTAR
        $sql = "INSERT INTO Ingresos (Pago_ID, Monto) VALUES ($pago_id, '$monto')";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ingresos.php");
        exit();
    } else {
        die("Error de base de datos: " . mysqli_error($conn));
    }
}
?>

<?php include("header.php"); ?>
<div class="header-actions">
    <button class="add-button" onclick="openModal()">
        <i class="fa-solid fa-plus"></i> Registrar Ingreso
    </button>
</div>

<h1>Lista de Ingresos Registrados</h1>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID Ingreso</th>
                <th>Monto ($)</th>
                <th>Pago Asociado (Referencia)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sql_ingresos = "SELECT I.*, P.Referencia 
                             FROM Ingresos I 
                             LEFT JOIN PagosEstudiantes P ON I.Pago_ID = P.Pago_ID";
            $result = mysqli_query($conn, $sql_ingresos);

            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row["Ingresos_ID"]; ?></td>
                    <td style="font-weight: bold; color: var(--success);">$<?php echo number_format($row["Monto"], 2); ?></td>
                    <td><?php echo $row["Referencia"] ? $row["Referencia"] : '<span style="color:#aaa;">No vinculado</span>'; ?></td>
                    <td>
                        <i class="fa-solid fa-pen-to-square" onclick='editIngreso(<?php echo json_encode($row); ?>)'></i>
                        &nbsp;&nbsp;
                        <a href="ingresos.php?eliminar=<?php echo $row['Ingresos_ID']; ?>" onclick="return confirm('¿Seguro que querés borrar este registro?')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='4'>No hay ingresos registrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div id="ingresoModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Nuevo Ingreso</h2>
        <hr>
        
        <form method="post" action="">
            <input type="hidden" id="ingreso_id_edit" name="ingreso_id_edit">
            
            <label>Monto ($)</label>
            <input type="number" step="0.01" id="input_monto" name="monto" placeholder="Ej. 150.00" required>

            <label>Vincular con Pago de Estudiante (Opcional)</label>
            <select id="input_pago_id" name="pago_id">
                <option value="">-- Sin vincular --</option>
                <?php 
                if ($res_pagos_disponibles) {
                    mysqli_data_seek($res_pagos_disponibles, 0); 
                    while($p = mysqli_fetch_assoc($res_pagos_disponibles)) { ?>
                        <option value="<?php echo $p['Pago_ID']; ?>">
                            ID: <?php echo $p['Pago_ID']; ?> - Ref: <?php echo $p['Referencia']; ?>
                        </option>
                    <?php } 
                } ?>
            </select>
            
            <input type="submit" id="submitBtn" value="Guardar">
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('ingresoModal');

    function openModal() {
        document.getElementById('modalTitle').innerText = "Registrar Ingreso";
        document.getElementById('ingreso_id_edit').value = "";
        document.getElementById('input_monto').value = "";
        document.getElementById('input_pago_id').value = "";
        document.getElementById('submitBtn').value = "Guardar Ingreso";
        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function editIngreso(datos) {
        document.getElementById('modalTitle').innerText = "Editar Ingreso";
        document.getElementById('ingreso_id_edit').value = datos.Ingresos_ID;
        document.getElementById('input_monto').value = datos.Monto;
        document.getElementById('input_pago_id').value = datos.Pago_ID ? datos.Pago_ID : "";
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

