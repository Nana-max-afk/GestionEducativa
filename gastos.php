<?php 
require_once("conexion.php");


// --- 1. TRAER INGRESOS Y PAGOS DOCENTES DISPONIBLES ---
$res_ingresos = mysqli_query($conn, "SELECT Ingresos_ID FROM Ingresos");
$res_pagos_doc = mysqli_query($conn, "SELECT PagosDocente_ID FROM pagosdocente");

// --- 2. LÓGICA PARA ELIMINAR ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    mysqli_query($conn, "DELETE FROM Gastos WHERE Gastos_ID = $id_eliminar");
    header("Location: gastos.php");
    exit();
}

// --- 3. LÓGICA PARA INSERTAR O EDITAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['descripcion_gastos'])) {
    $desc = $_POST['descripcion_gastos'];
    $monto = $_POST['monto'];
    $ingreso_id = $_POST['ingresos_id'] == "" ? "NULL" : $_POST['ingresos_id'];
    $pago_doc_id = $_POST['pagodocente_id'] == "" ? "NULL" : $_POST['pagodocente_id'];
    $id_edit = $_POST['gasto_id_edit'];

    if (!empty($id_edit)) {
        // ACTUALIZAR
        $sql = "UPDATE Gastos SET 
                Descripcion_Gastos='$desc', 
                Monto='$monto',
                Ingresos_ID=$ingreso_id, 
                PagoDocente_ID=$pago_doc_id 
                WHERE Gastos_ID=$id_edit";
    } else {
        // INSERTAR
        $sql = "INSERT INTO Gastos (Descripcion_Gastos, Monto, Ingresos_ID, PagoDocente_ID) 
                VALUES ('$desc', '$monto', $ingreso_id, $pago_doc_id)";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: gastos.php");
        exit();
    } else {
        die("Error en la DB: " . mysqli_error($conn));
    }
}
?>

<?php include("header.php"); ?>
<div class="header-actions">
    <button class="add-button" onclick="openModal()">
        <i class="fa-solid fa-file-invoice-dollar"></i> Nuevo Gasto
    </button>
</div>

<h1>Lista de Gastos</h1>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Monto ($)</th>
                <th>Descripción</th>
                <th>Ingreso Relacionado</th>
                <th>Pago Docente Relacionado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $result = mysqli_query($conn, "SELECT * FROM Gastos");
            if($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row["Gastos_ID"]; ?></td>
                    <td style="font-weight: bold; color: var(--danger);">$<?php echo number_format($row["Monto"], 2); ?></td>
                    <td><?php echo $row["Descripcion_Gastos"]; ?></td>
                    <td><?php echo $row["Ingresos_ID"] ? $row["Ingresos_ID"] : '<span style="color:#aaa;">Ninguno</span>'; ?></td>
                    <td><?php echo $row["PagoDocente_ID"] ? $row["PagoDocente_ID"] : '<span style="color:#aaa;">Ninguno</span>'; ?></td>
                    <td>
                        <i class="fa-solid fa-pen-to-square" onclick='editGasto(<?php echo json_encode($row); ?>)'></i>
                        &nbsp;&nbsp;
                        <a href="gastos.php?eliminar=<?php echo $row['Gastos_ID']; ?>" onclick="return confirm('¿Eliminar gasto?')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } 
            } else { echo "<tr><td colspan='6'>No hay gastos registrados.</td></tr>"; } ?>
        </tbody>
    </table>
</div>

<div id="gastoModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Registrar Gasto</h2>
        <hr>
        <form method="post">
            <input type="hidden" id="gasto_id_edit" name="gasto_id_edit">
            
            <label>Monto ($)</label>
            <input type="number" step="0.01" id="input_monto" name="monto" placeholder="Ej. 50.00" required>

            <label>Descripción del Gasto</label>
            <input type="text" id="input_desc" name="descripcion_gastos" required placeholder="Ej: Pago de Luz">
            
            <label>Vincular con Ingreso (Opcional)</label>
            <select id="input_ingreso" name="ingresos_id">
                <option value="">-- Sin Vincular --</option>
                <?php 
                if ($res_ingresos) {
                    mysqli_data_seek($res_ingresos, 0);
                    while($i = mysqli_fetch_assoc($res_ingresos)) { ?>
                        <option value="<?php echo $i['Ingresos_ID']; ?>">Ingreso #<?php echo $i['Ingresos_ID']; ?></option>
                    <?php } 
                } ?>
            </select>

            <label>Vincular con Pago de Docente (Opcional)</label>
            <select id="input_pago_doc" name="pagodocente_id">
                <option value="">-- Sin Vincular --</option>
                <?php 
                if ($res_pagos_doc) {
                    mysqli_data_seek($res_pagos_doc, 0);
                    while($pd = mysqli_fetch_assoc($res_pagos_doc)) { ?>
                        <option value="<?php echo $pd['PagosDocente_ID']; ?>">Pago Docente #<?php echo $pd['PagosDocente_ID']; ?></option>
                    <?php } 
                } ?>
            </select>

            <input type="submit" id="submitBtn" value="Guardar Gasto">
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('gastoModal');

    function openModal() {
        document.getElementById('modalTitle').innerText = "Nuevo Gasto";
        document.getElementById('gasto_id_edit').value = "";
        document.getElementById('input_monto').value = "";
        document.getElementById('input_desc').value = "";
        document.getElementById('input_ingreso').value = "";
        document.getElementById('input_pago_doc').value = "";
        document.getElementById('submitBtn').value = "Guardar Gasto";
        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function editGasto(datos) {
        document.getElementById('modalTitle').innerText = "Editar Gasto";
        document.getElementById('gasto_id_edit').value = datos.Gastos_ID;
        document.getElementById('input_monto').value = datos.Monto;
        document.getElementById('input_desc').value = datos.Descripcion_Gastos;
        document.getElementById('input_ingreso').value = datos.Ingresos_ID ? datos.Ingresos_ID : "";
        document.getElementById('input_pago_doc').value = datos.PagoDocente_ID ? datos.PagoDocente_ID : "";
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

