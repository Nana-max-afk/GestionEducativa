<?php 
require_once("conexion.php");

// --- CARGAR DATOS PARA LOS SELECTS ---
$res_estudiantes = mysqli_query($conn, "SELECT Estudiantes_ID, Nombre, Apellido, CI FROM ESTUDIANTES");
$res_ingresos = mysqli_query($conn, "SELECT Ingresos_ID FROM Ingresos");

// --- 1. LÓGICA PARA ELIMINAR (CON PREPARED STATEMENTS) ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    $sql_del = "DELETE FROM pagosestudiantes WHERE Pago_ID = ?";
    $stmt = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt, "i", $id_eliminar);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: pagosestudiantes.php");
    exit();
}

// --- 2. LÓGICA PARA INSERTAR O EDITAR (CON PREPARED STATEMENTS) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estudiantes_id'])) {
    $estudiantes_id = (int)$_POST['estudiantes_id'];
    $status_pago = $_POST['status_pago'];
    $tipo_pago = $_POST['tipo_pago'];
    $banco_emisor = $_POST['banco_emisor'];
    $referencia = $_POST['referencia'];
    $descripcion_pago = $_POST['descripcion_pago'];
    $monto = (float)$_POST['monto'];
    $id_edit = $_POST['pago_id_edit'];

    // Manejo correcto de nulos para la clave foránea de Ingreso_ID
    $ingreso_id = $_POST['ingreso_id'] == '0' ? null : (int)$_POST['ingreso_id'];

    if (!empty($id_edit)) {
        // ACTUALIZAR PAGO EXISTENTE
        $id_edit = (int)$id_edit;
        $sql = "UPDATE pagosestudiantes SET Estudiantes_ID=?, Status_Pago=?, Tipo_Pago=?, Banco_Emisor=?, Referencia=?, Descripcion_Pago=?, Ingreso_ID=?, Monto=? WHERE Pago_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssssdi", $estudiantes_id, $status_pago, $tipo_pago, $banco_emisor, $referencia, $descripcion_pago, $ingreso_id, $monto, $id_edit);
    } else {
        // INSERTAR NUEVO PAGO
        $sql = "INSERT INTO pagosestudiantes (Estudiantes_ID, Status_Pago, Tipo_Pago, Banco_Emisor, Referencia, Descripcion_Pago, Ingreso_ID, Monto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssssd", $estudiantes_id, $status_pago, $tipo_pago, $banco_emisor, $referencia, $descripcion_pago, $ingreso_id, $monto);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: pagosestudiantes.php");
        exit();
    } else {
        echo "Error al procesar el pago financiero.";
    }
}

include("header.php");

// Cargar listado relacional
$res_pagos_lista = mysqli_query($conn, "SELECT p.*, e.Nombre, e.Apellido FROM pagosestudiantes p JOIN ESTUDIANTES e ON p.Estudiantes_ID = e.Estudiantes_ID");
?>

    <div class="header-actions">
        <button class="add-button" onclick="openModal()">Registrar Pago</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Referencia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($res_pagos_lista)) { ?>
            <tr>
                <td><?php echo $row['Pago_ID']; ?></td>
                <td><?php echo htmlspecialchars($row['Nombre'] . " " . $row['Apellido'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo $row['Monto']; ?></td>
                <td><?php echo $row['Status_Pago']; ?></td>
                <td><?php echo htmlspecialchars($row['Referencia'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href="javascript:void(0);" onclick='editPago(<?php echo json_encode($row); ?>)' style="margin-right: 10px; color: #2196F3;"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:void(0);" onclick="if(confirm('¿Deseas eliminar este registro de pago?')) window.location.href='pagosestudiantes.php?eliminar=<?php echo $row['Pago_ID']; ?>';" style="color: #e74c3c;"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="pagoModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Registrar Pago</h2>
            
            <form action="pagosestudiantes.php" method="POST">
                <input type="hidden" id="pago_id_edit" name="pago_id_edit">
                
                <label for="input_estudiantes_id">Estudiante:</label>
                <select id="input_estudiantes_id" name="estudiantes_id" required>
                    <?php mysqli_data_seek($res_estudiantes, 0); ?>
                    <?php while($est = mysqli_fetch_assoc($res_estudiantes)) { ?>
                        <option value="<?php echo $est['Estudiantes_ID']; ?>"><?php echo $est['Nombre']." ".$est['Apellido']." (".$est['CI'].")"; ?></option>
                    <?php } ?>
                </select>

                <label for="input_monto">Monto:</label>
                <input type="number" step="0.01" id="input_monto" name="monto" required>

                <label for="input_status_pago">Estado del Pago:</label>
                <select id="input_status_pago" name="status_pago">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Pagado">Pagado</option>
                    <option value="Abono">Abono</option>
                </select>

                <label for="input_tipo_pago">Tipo de Pago:</label>
                <input type="text" id="input_tipo_pago" name="tipo_pago" placeholder="Ej. Transferencia, Efectivo">

                <label for="input_banco_emisor">Banco Emisor:</label>
                <input type="text" id="input_banco_emisor" name="banco_emisor">

                <label for="input_referencia">Referencia:</label>
                <input type="text" id="input_referencia" name="referencia">

                <label for="input_descripcion_pago">Descripción:</label>
                <textarea id="input_descripcion_pago" name="descripcion_pago"></textarea>

                <label for="input_ingreso_id">Asociar a ID Ingreso (Opcional):</label>
                <select id="input_ingreso_id" name="ingreso_id">
                    <option value="0">Ninguno</option>
                    <?php mysqli_data_seek($res_ingresos, 0); ?>
                    <?php while($ing = mysqli_fetch_assoc($res_ingresos)) { ?>
                        <option value="<?php echo $ing['Ingresos_ID']; ?>">Ingreso #<?php echo $ing['Ingresos_ID']; ?></option>
                    <?php } ?>
                </select>

                <input type="submit" id="submitBtn" class="btn-submit" value="Guardar Pago">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('pagoModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Registrar Pago";
            document.getElementById('pago_id_edit').value = "";
            document.getElementById('input_monto').value = "";
            document.getElementById('input_status_pago').value = "Pendiente";
            document.getElementById('input_tipo_pago').value = "";
            document.getElementById('input_banco_emisor').value = "";
            document.getElementById('input_referencia').value = "";
            document.getElementById('input_descripcion_pago').value = "";
            document.getElementById('input_ingreso_id').value = "0";
            document.getElementById('submitBtn').value = "Guardar Pago";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editPago(datos) {
            document.getElementById('modalTitle').innerText = "Editar Pago";
            document.getElementById('pago_id_edit').value = datos.Pago_ID;
            document.getElementById('input_estudiantes_id').value = datos.Estudiantes_ID;
            document.getElementById('input_monto').value = datos.Monto;
            document.getElementById('input_status_pago').value = datos.Status_Pago;
            document.getElementById('input_tipo_pago').value = datos.Tipo_Pago;
            document.getElementById('input_banco_emisor').value = datos.Banco_Emisor;
            document.getElementById('input_referencia').value = datos.Referencia;
            document.getElementById('input_descripcion_pago').value = datos.Descripcion_Pago;
            document.getElementById('input_ingreso_id').value = datos.Ingreso_ID ? datos.Ingreso_ID : "0";
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