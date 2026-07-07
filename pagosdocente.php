<?php 
require_once("conexion.php");

// --- CARGAR DATOS PARA LOS SELECTS ---
$res_docentes = mysqli_query($conn, "SELECT Docente_ID, Nombre, Apellido, CI FROM DOCENTES");

// --- 1. LÓGICA PARA ELIMINAR (CON PREPARED STATEMENTS) ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    $sql_del = "DELETE FROM pagosdocente WHERE PagosDocente_ID = ?";
    $stmt = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt, "i", $id_eliminar);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: pagosdocente.php");
    exit();
}

// --- 2. LÓGICA PARA INSERTAR O EDITAR (CON PREPARED STATEMENTS) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['docente_id'])) {
    $docente_id = (int)$_POST['docente_id'];
    $tipo_pago = $_POST['tipo_pago'];
    $descripcion_pago = $_POST['descripcion_pago'];
    $fecha_pago = $_POST['fecha_pago'];
    $status_pago = $_POST['status_pago'];
    $monto = (float)$_POST['monto'];
    $id_edit = $_POST['pagosdocente_id_edit'];

    if (!empty($id_edit)) {
        // ACTUALIZAR PAGO EXISTENTE
        $id_edit = (int)$id_edit;
        $sql = "UPDATE pagosdocente SET Docente_ID=?, Tipo_Pago=?, Descripcion_Pago=?, Fecha_Pago=?, Status_Pago=?, Monto=? WHERE PagosDocente_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssdi", $docente_id, $tipo_pago, $descripcion_pago, $fecha_pago, $status_pago, $monto, $id_edit);
    } else {
        // INSERTAR NUEVO PAGO
        $sql = "INSERT INTO pagosdocente (Docente_ID, Tipo_Pago, Descripcion_Pago, Fecha_Pago, Status_Pago, Monto) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssd", $docente_id, $tipo_pago, $descripcion_pago, $fecha_pago, $status_pago, $monto);
    }

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: pagosdocente.php");
        exit();
    } else {
        echo "Error al guardar el pago del docente.";
    }
}

include("header.php");

// Carga relacional para la tabla
$res_pagos_doc_lista = mysqli_query($conn, "SELECT p.*, d.Nombre, d.Apellido FROM pagosdocente p JOIN DOCENTES d ON p.Docente_ID = d.Docente_ID");
?>

    <div class="header-actions">
        <button class="add-button" onclick="openModal()">Registrar Pago Docente</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Docente</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($res_pagos_doc_lista)) { ?>
            <tr>
                <td><?php echo $row['PagosDocente_ID']; ?></td>
                <td><?php echo htmlspecialchars($row['Nombre'] . " " . $row['Apellido'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo $row['Monto']; ?></td>
                <td><?php echo $row['Fecha_Pago']; ?></td>
                <td><?php echo $row['Status_Pago']; ?></td>
                <td>
                    <a href="javascript:void(0);" onclick='editPago(<?php echo json_encode($row); ?>)' style="margin-right: 10px; color: #2196F3;"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:void(0);" onclick="if(confirm('¿Seguro que deseas eliminar este registro de pago al docente?')) window.location.href='pagosdocente.php?eliminar=<?php echo $row['PagosDocente_ID']; ?>';" style="color: #e74c3c;"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="pagoDocenteModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Registrar Pago</h2>
            
            <form action="pagosdocente.php" method="POST">
                <input type="hidden" id="pagosdocente_id_edit" name="pagosdocente_id_edit">
                
                <label for="input_docente_id">Docente:</label>
                <select id="input_docente_id" name="docente_id" required>
                    <?php mysqli_data_seek($res_docentes, 0); ?>
                    <?php while($doc = mysqli_fetch_assoc($res_docentes)) { ?>
                        <option value="<?php echo $doc['Docente_ID']; ?>"><?php echo $doc['Nombre']." ".$doc['Apellido']." (".$doc['CI'].")"; ?></option>
                    <?php } ?>
                </select>

                <label for="input_monto">Monto:</label>
                <input type="number" step="0.01" id="input_monto" name="monto" required>

                <label for="input_tipo_pago">Tipo de Pago:</label>
                <input type="text" id="input_tipo_pago" name="tipo_pago" placeholder="Ej. Efectivo, Transferencia">

                <label for="input_fecha_pago">Fecha de Pago:</label>
                <input type="date" id="input_fecha_pago" name="fecha_pago" required>

                <label for="input_status_pago">Estado del Pago:</label>
                <select id="input_status_pago" name="status_pago">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Pagado">Pagado</option>
                </select>

                <label for="input_descripcion_pago">Descripción:</label>
                <textarea id="input_descripcion_pago" name="descripcion_pago"></textarea>

                <input type="submit" id="submitBtn" class="btn-submit" value="Guardar Pago">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('pagoDocenteModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Registrar Pago";
            document.getElementById('pagosdocente_id_edit').value = "";
            document.getElementById('input_monto').value = "";
            document.getElementById('input_tipo_pago').value = "";
            document.getElementById('input_fecha_pago').value = "";
            document.getElementById('input_status_pago').value = "Pendiente";
            document.getElementById('input_descripcion_pago').value = "";
            document.getElementById('submitBtn').value = "Guardar Pago";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editPago(datos) {
            document.getElementById('modalTitle').innerText = "Editar Pago";
            document.getElementById('pagosdocente_id_edit').value = datos.PagosDocente_ID;
            document.getElementById('input_docente_id').value = datos.Docente_ID;
            document.getElementById('input_monto').value = datos.Monto;
            document.getElementById('input_tipo_pago').value = datos.Tipo_Pago;
            document.getElementById('input_descripcion_pago').value = datos.Descripcion_Pago;
            document.getElementById('input_fecha_pago').value = datos.Fecha_Pago;
            document.getElementById('input_status_pago').value = datos.Status_Pago;
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
