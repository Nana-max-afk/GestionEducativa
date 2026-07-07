<?php 
require_once("conexion.php");

// --- CARGAR DATOS PARA LOS SELECTS ---
$res_cursos = mysqli_query($conn, "SELECT Curso_ID, Diplomado FROM CURSOS");
$res_estudiantes = mysqli_query($conn, "SELECT Estudiantes_ID, Nombre, Apellido, CI FROM ESTUDIANTES");
$res_cohortes = mysqli_query($conn, "SELECT Cohorte_ID, Fecha_de_inicio FROM cohortes");
$res_pagos = mysqli_query($conn, "SELECT Pago_ID, Referencia FROM pagosestudiantes");
$res_sedes = mysqli_query($conn, "SELECT Sede_ID, Sede FROM SEDES");
$res_paises = mysqli_query($conn, "SELECT Pais_ID, Pais FROM paises ORDER BY Pais ASC");

// --- 1. LÓGICA PARA ELIMINAR (CON PREPARED STATEMENTS) ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    $sql_del = "DELETE FROM inscripciones WHERE Inscripcion_ID = ?";
    $stmt = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt, "i", $id_eliminar);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: inscripciones.php");
    exit();
}

// --- 2. LÓGICA PARA INSERTAR O EDITAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['curso_id'])) {
    $curso_id = (int)$_POST['curso_id'];
    $cohorte_id = (int)$_POST['cohorte_id'];
    $status_pago = $_POST['status_pago'];
    $nota_minima = (float)$_POST['nota_minima'];
    
    // Tratamiento seguro de claves foráneas nulas
    $pago_id = $_POST['pago_id'] == '0' ? null : (int)$_POST['pago_id'];
    $id_edit = $_POST['inscripcion_id_edit'];
    $estudiante_id = null;

    if (empty($id_edit)) {
        // MODO CREACIÓN: Evaluamos si insertamos un alumno nuevo o vinculamos existente
        if ($_POST['tipo_estudiante'] == 'nuevo') {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $ci = $_POST['ci'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];
            $sede_id = (int)$_POST['sede_id'];
            
            $pais_id = $_POST['pais_id'] == "" ? null : (int)$_POST['pais_id'];
            $estado_id = $_POST['estado_id'] == "" ? null : (int)$_POST['estado_id'];
            $ciudad_id = $_POST['ciudad_id'] == "" ? null : (int)$_POST['ciudad_id'];

            $sql_est = "INSERT INTO ESTUDIANTES (Nombre, Apellido, CI, Telefono, Correo, Sede_ID, Pais_ID, Estado_ID, Ciudad_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_est = mysqli_prepare($conn, $sql_est);
            mysqli_stmt_bind_param($stmt_est, "sssssiiii", $nombre, $apellido, $ci, $telefono, $correo, $sede_id, $pais_id, $estado_id, $ciudad_id);
            mysqli_stmt_execute($stmt_est);
            $estudiante_id = mysqli_insert_id($conn); // Captura el ID creado
            mysqli_stmt_close($stmt_est);
        } else {
            $estudiante_id = (int)$_POST['estudiante_id'];
        }

        // GUARDAR INSCRIPCIÓN NUEVA
        $sql_insc = "INSERT INTO inscripciones (Curso_ID, Estudiante_ID, Cohorte_ID, Status_Pago, Nota_minima, Pago_ID) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insc = mysqli_prepare($conn, $sql_insc);
        mysqli_stmt_bind_param($stmt_insc, "iiisdi", $curso_id, $estudiante_id, $cohorte_id, $status_pago, $nota_minima, $pago_id);
        mysqli_stmt_execute($stmt_insc);
        mysqli_stmt_close($stmt_insc);
    } else {
        // MODO EDICIÓN
        $id_edit = (int)$id_edit;
        $estudiante_id = (int)$_POST['estudiante_id'];

        $sql_up = "UPDATE inscripciones SET Curso_ID=?, Estudiante_ID=?, Cohorte_ID=?, Status_Pago=?, Nota_minima=?, Pago_ID=? WHERE Inscripcion_ID=?";
        $stmt_up = mysqli_prepare($conn, $sql_up);
        mysqli_stmt_bind_param($stmt_up, "iiisdii", $curso_id, $estudiante_id, $cohorte_id, $status_pago, $nota_minima, $pago_id, $id_edit);
        mysqli_stmt_execute($stmt_up);
        mysqli_stmt_close($stmt_up);
    }

    header("Location: inscripciones.php");
    exit();
}

include("header.php");

// Carga relacional completa para la grilla principal
$query_lista = "SELECT i.*, c.Diplomado, e.Nombre, e.Apellido, e.CI, co.Fecha_de_inicio, p.Referencia 
                FROM inscripciones i
                JOIN CURSOS c ON i.Curso_ID = c.Curso_ID
                JOIN ESTUDIANTES e ON i.Estudiante_ID = e.Estudiantes_ID
                JOIN cohortes co ON i.Cohorte_ID = co.Cohorte_ID
                LEFT JOIN pagosestudiantes p ON i.Pago_ID = p.Pago_ID";
$res_inscripciones_lista = mysqli_query($conn, $query_lista);
?>

    <div class="header-actions">
        <button class="add-button" onclick="openModal()">Nueva Inscripción</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Cédula</th>
                <th>Curso</th>
                <th>Estado Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($res_inscripciones_lista)) { ?>
            <tr>
                <td><?php echo $row['Inscripcion_ID']; ?></td>
                <td><?php echo htmlspecialchars($row['Nombre'] . " " . $row['Apellido'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['CI'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['Diplomado'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo $row['Status_Pago']; ?></td>
                <td>
                    <a href="javascript:void(0);" onclick='editInscripcion(<?php echo json_encode($row); ?>)' style="margin-right: 10px; color: #2196F3;"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:void(0);" onclick="if(confirm('¿Deseas revocar esta inscripción escolar?')) window.location.href='inscripciones.php?eliminar=<?php echo $row['Inscripcion_ID']; ?>';" style="color: #e74c3c;"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="inscripcionModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nueva Inscripción</h2>
            
            <form action="inscripciones.php" method="POST">
                <input type="hidden" id="inscripcion_id_edit" name="inscripcion_id_edit">

                <label for="input_curso_id">Curso / Diplomado:</label>
                <select id="input_curso_id" name="curso_id" required>
                    <?php mysqli_data_seek($res_cursos, 0); while($c = mysqli_fetch_assoc($res_cursos)) { ?>
                        <option value="<?php echo $c['Curso_ID']; ?>"><?php echo htmlspecialchars($c['Diplomado'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                </select>

                <label for="input_cohorte_id">Cohorte (Fecha Inicio):</label>
                <select id="input_cohorte_id" name="cohorte_id" required>
                    <?php mysqli_data_seek($res_cohortes, 0); while($co = mysqli_fetch_assoc($res_cohortes)) { ?>
                        <option value="<?php echo $co['Cohorte_ID']; ?>"><?php echo $co['Fecha_de_inicio']; ?></option>
                    <?php } ?>
                </select>

                <div id="toggle_estudiante_container" style="margin: 15px 0; padding: 10px; background: #eef2f5; border-radius: 6px;">
                    <label style="margin-right: 20px;">
                        <input type="radio" name="tipo_estudiante" value="existente" checked onclick="toggleEstudiante('existente')"> Estudiante Existente
                    </label>
                    <label>
                        <input type="radio" name="tipo_estudiante" value="nuevo" onclick="toggleEstudiante('nuevo')"> Registrar Alumno Nuevo
                    </label>
                </div>

                <div id="sec_existente">
                    <label for="input_estudiante_id">Seleccionar Estudiante:</label>
                    <select id="input_estudiante_id" name="estudiante_id">
                        <?php mysqli_data_seek($res_estudiantes, 0); while($e = mysqli_fetch_assoc($res_estudiantes)) { ?>
                            <option value="<?php echo $e['Estudiantes_ID']; ?>"><?php echo $e['Nombre']." ".$e['Apellido']." (".$e['CI'].")"; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div id="sec_nuevo" style="display:none; border-left: 4px solid #4CAF50; padding-left: 15px; margin-top: 10px;">
                    <h3>Datos Personales del Estudiante</h3>
                    <input type="text" name="nombre" placeholder="Nombres">
                    <input type="text" name="apellido" placeholder="Apellidos">
                    <input type="text" name="ci" placeholder="Cédula">
                    <input type="text" name="telefono" placeholder="Teléfono Móvil">
                    <input type="text" name="correo" placeholder="Email institucional">
                    
                    <label>Sede de Adscripción:</label>
                    <select name="sede_id">
                        <?php mysqli_data_seek($res_sedes, 0); while($s = mysqli_fetch_assoc($res_sedes)) { ?>
                            <option value="<?php echo $s['Sede_ID']; ?>"><?php echo htmlspecialchars($s['Sede'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php } ?>
                    </select>

                    <!-- Campos Geográficos para Alumno Nuevo -->
                    <h3 style="margin-top:15px;">Dirección / Ubicación</h3>
                    <label>País:</label>
                    <select id="input_pais_id" name="pais_id">
                        <option value="">Seleccione un país...</option>
                        <?php mysqli_data_seek($res_paises, 0); while($p = mysqli_fetch_assoc($res_paises)) { ?>
                            <option value="<?php echo $p['Pais_ID']; ?>"><?php echo htmlspecialchars($p['Pais'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php } ?>
                    </select>

                    <label>Estado:</label>
                    <select id="input_estado_id" name="estado_id">
                        <option value="">Seleccione un estado...</option>
                    </select>

                    <label>Ciudad:</label>
                    <select id="input_ciudad_id" name="ciudad_id">
                        <option value="">Seleccione una ciudad...</option>
                    </select>
                </div>

                <label for="input_status_pago">Estado Matriculación:</label>
                <select id="input_status_pago" name="status_pago">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Pagado">Pagado</option>
                    <option value="Abono">Abono</option>
                </select>

                <label for="input_nota_minima">Nota Exigida Aprobación:</label>
                <input type="number" step="0.01" id="input_nota_minima" name="nota_minima" value="10.00">

                <label for="input_pago_id">Vincular Transacción Financiera:</label>
                <select id="input_pago_id" name="pago_id">
                    <option value="0">Ninguno / Operación en Espera</option>
                    <?php mysqli_data_seek($res_pagos, 0); while($p = mysqli_fetch_assoc($res_pagos)) { ?>
                        <option value="<?php echo $p['Pago_ID']; ?>">Referencia: <?php echo htmlspecialchars($p['Referencia'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                </select>

                <input type="submit" id="submitBtn" class="btn-submit" value="Guardar Inscripción">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('inscripcionModal');

        // AJAX para combos dinámicos en Inscripciones (Alumno Nuevo)
        document.getElementById('input_pais_id').addEventListener('change', function() {
            const paisId = this.value;
            const estadoSelect = document.getElementById('input_estado_id');
            const ciudadSelect = document.getElementById('input_ciudad_id');
            
            estadoSelect.innerHTML = '<option value="">Cargando estados...</option>';
            ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad...</option>';
            
            if (!paisId) {
                estadoSelect.innerHTML = '<option value="">Seleccione un estado...</option>';
                return;
            }
            
            fetch('get_location.php?pais_id=' + paisId)
                .then(res => res.json())
                .then(data => {
                    estadoSelect.innerHTML = '<option value="">Seleccione un estado...</option>';
                    data.forEach(est => {
                        estadoSelect.innerHTML += `<option value="${est.Estado_ID}">${est.Estado}</option>`;
                    });
                })
                .catch(err => {
                    console.error("Error cargando estados:", err);
                    estadoSelect.innerHTML = '<option value="">Error al cargar estados</option>';
                });
        });

        document.getElementById('input_estado_id').addEventListener('change', function() {
            const estadoId = this.value;
            const ciudadSelect = document.getElementById('input_ciudad_id');
            
            ciudadSelect.innerHTML = '<option value="">Cargando ciudades...</option>';
            
            if (!estadoId) {
                ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad...</option>';
                return;
            }
            
            fetch('get_location.php?estado_id=' + estadoId)
                .then(res => res.json())
                .then(data => {
                    ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad...</option>';
                    data.forEach(c => {
                        ciudadSelect.innerHTML += `<option value="${c.Ciudad_ID}">${c.Ciudad}</option>`;
                    });
                })
                .catch(err => {
                    console.error("Error cargando ciudades:", err);
                    ciudadSelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
                });
        });

        function toggleEstudiante(tipo) {
            const secExistente = document.getElementById('sec_existente');
            const secNuevo = document.getElementById('sec_nuevo');
            const inputEstudianteId = document.getElementById('input_estudiante_id');
            const inputNombre = document.querySelector('input[name="nombre"]');
            const inputApellido = document.querySelector('input[name="apellido"]');
            const inputCi = document.querySelector('input[name="ci"]');
            
            const selectPais = document.getElementById('input_pais_id');
            const selectEstado = document.getElementById('input_estado_id');
            const selectCiudad = document.getElementById('input_ciudad_id');

            if(tipo === 'existente') {
                secExistente.style.display = 'block';
                secNuevo.style.display = 'none';
                
                inputEstudianteId.setAttribute('required', 'required');
                inputNombre.removeAttribute('required');
                inputApellido.removeAttribute('required');
                inputCi.removeAttribute('required');
                selectPais.removeAttribute('required');
                selectEstado.removeAttribute('required');
                selectCiudad.removeAttribute('required');
            } else {
                secExistente.style.display = 'none';
                secNuevo.style.display = 'block';
                
                inputEstudianteId.removeAttribute('required');
                inputNombre.setAttribute('required', 'required');
                inputApellido.setAttribute('required', 'required');
                inputCi.setAttribute('required', 'required');
                selectPais.setAttribute('required', 'required');
                selectEstado.setAttribute('required', 'required');
                selectCiudad.setAttribute('required', 'required');
            }
        }

        function openModal() {
            document.getElementById('modalTitle').innerText = "Nueva Inscripción";
            document.getElementById('inscripcion_id_edit').value = "";
            document.getElementById('submitBtn').value = "Guardar Inscripción";
            document.getElementById('toggle_estudiante_container').style.display = 'block';
            
            // Limpiar inputs del alumno nuevo
            document.querySelector('input[name="nombre"]').value = "";
            document.querySelector('input[name="apellido"]').value = "";
            document.querySelector('input[name="ci"]').value = "";
            document.querySelector('input[name="telefono"]').value = "";
            document.querySelector('input[name="correo"]').value = "";
            document.getElementById('input_pais_id').value = "";
            document.getElementById('input_estado_id').innerHTML = '<option value="">Seleccione un estado...</option>';
            document.getElementById('input_ciudad_id').innerHTML = '<option value="">Seleccione una ciudad...</option>';

            document.querySelector('input[name="tipo_estudiante"][value="existente"]').checked = true;
            toggleEstudiante('existente');
            
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editInscripcion(datos) {
            document.getElementById('modalTitle').innerText = "Editar Inscripción";
            document.getElementById('inscripcion_id_edit').value = datos.Inscripcion_ID;
            document.getElementById('input_curso_id').value = datos.Curso_ID;
            document.getElementById('input_estudiante_id').value = datos.Estudiante_ID;
            document.getElementById('input_cohorte_id').value = datos.Cohorte_ID;
            document.getElementById('input_status_pago').value = datos.Status_Pago;
            document.getElementById('input_nota_minima').value = datos.Nota_minima;
            document.getElementById('input_pago_id').value = datos.Pago_ID ? datos.Pago_ID : "0";
            document.getElementById('submitBtn').value = "Actualizar Cambios";
            
            document.getElementById('toggle_estudiante_container').style.display = 'none';
            document.querySelector('input[name="tipo_estudiante"][value="existente"]').checked = true;
            toggleEstudiante('existente');

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