<?php 
require_once("conexion.php");

// Cargar países para el select
$res_paises = mysqli_query($conn, "SELECT Pais_ID, Pais FROM paises ORDER BY Pais ASC");

// --- 1. LÓGICA PARA ELIMINAR (CON PREPARED STATEMENTS) ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    
    // Verificamos si tiene cursos o pagos asociados para evitar un crash de integridad
    $sql_check = "SELECT COUNT(*) as total FROM pagosdocente WHERE Docente_ID = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id_eliminar);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($res_check);
    mysqli_stmt_close($stmt_check);

    if ($row_check['total'] > 0) {
        echo "<script>alert('No se puede eliminar el docente: tiene pagos registrados.'); window.location='docentes.php';</script>";
        exit();
    }

    $sql_del = "DELETE FROM DOCENTES WHERE Docente_ID = ?";
    $stmt_del = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt_del, "i", $id_eliminar);
    
    if (mysqli_stmt_execute($stmt_del)) {
        mysqli_stmt_close($stmt_del);
        header("Location: docentes.php");
        exit();
    } else {
        echo "<script>alert('Error al intentar eliminar el docente.'); window.location='docentes.php';</script>";
    }
}

// --- 2. LÓGICA PARA INSERTAR O EDITAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $ci = $_POST['ci'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    
    $pais_id = $_POST['pais_id'] == "" ? null : (int)$_POST['pais_id'];
    $estado_id = $_POST['estado_id'] == "" ? null : (int)$_POST['estado_id'];
    $ciudad_id = $_POST['ciudad_id'] == "" ? null : (int)$_POST['ciudad_id'];
    
    $id_edit = $_POST['docente_id_edit'];

    if (!empty($id_edit)) {
        // ACTUALIZAR DOCENTE
        $id_edit = (int)$id_edit;
        $sql = "UPDATE DOCENTES SET Nombre=?, Apellido=?, CI=?, Telefono=?, Correo=?, Pais_ID=?, Estado_ID=?, Ciudad_ID=? WHERE Docente_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssiiii", $nombre, $apellido, $ci, $telefono, $correo, $pais_id, $estado_id, $ciudad_id, $id_edit);
    } else {
        // INSERTAR NUEVO DOCENTE
        $sql = "INSERT INTO DOCENTES (Nombre, Apellido, CI, Telefono, Correo, Pais_ID, Estado_ID, Ciudad_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssiii", $nombre, $apellido, $ci, $telefono, $correo, $pais_id, $estado_id, $ciudad_id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: docentes.php");
        exit();
    } else {
        echo "Error al guardar los datos del docente: " . mysqli_error($conn);
    }
}

include("header.php");
// Query relacional completo con geografía
$res_docentes_lista = mysqli_query($conn, "SELECT d.*, p.Pais, est.Estado, c.Ciudad 
    FROM DOCENTES d 
    LEFT JOIN paises p ON d.Pais_ID = p.Pais_ID
    LEFT JOIN estados est ON d.Estado_ID = est.Estado_ID
    LEFT JOIN ciudades c ON d.Ciudad_ID = c.Ciudad_ID");
?>

    <div class="header-actions">
        <button class="add-button" onclick="openModal()">Nuevo Docente</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cédula</th>
                <th>Teléfono</th>
                <th>Ubicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($res_docentes_lista)) { ?>
            <tr>
                <td><?php echo $row['Docente_ID']; ?></td>
                <td><?php echo htmlspecialchars($row['Nombre'] . " " . $row['Apellido'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['CI'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['Telefono'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?php 
                    $location_parts = array_filter([$row['Pais'] ?? null, $row['Estado'] ?? null, $row['Ciudad'] ?? null]);
                    echo htmlspecialchars(implode(" - ", $location_parts), ENT_QUOTES, 'UTF-8') ?: '<span style="color:#aaa;">No registrada</span>'; 
                    ?>
                </td>
                <td>
                    <a href="javascript:void(0);" onclick='editDocente(<?php echo json_encode($row); ?>)' style="margin-right: 10px; color: #2196F3;"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:void(0);" onclick="if(confirm('¿Seguro que deseas eliminar este docente?')) window.location.href='docentes.php?eliminar=<?php echo $row['Docente_ID']; ?>';" style="color: #e74c3c;"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="docenteModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nuevo Docente</h2>
            
            <form action="docentes.php" method="POST">
                <input type="hidden" id="docente_id_edit" name="docente_id_edit">
                
                <label>Nombre:</label>
                <input type="text" id="input_nombre" name="nombre" required>
                
                <label>Apellido:</label>
                <input type="text" id="input_apellido" name="apellido" required>
                
                <label>Cédula:</label>
                <input type="text" id="input_ci" name="ci" required>
                
                <label>Teléfono:</label>
                <input type="text" id="input_telefono" name="telefono">
                
                <label>Correo:</label>
                <input type="email" id="input_correo" name="correo">

                <!-- Campos Geográficos -->
                <label>País:</label>
                <select id="input_pais_id" name="pais_id" required>
                    <option value="">Seleccione un país...</option>
                    <?php mysqli_data_seek($res_paises, 0); while($p = mysqli_fetch_assoc($res_paises)) { ?>
                        <option value="<?php echo $p['Pais_ID']; ?>"><?php echo htmlspecialchars($p['Pais'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                </select>

                <label>Estado:</label>
                <select id="input_estado_id" name="estado_id" required>
                    <option value="">Seleccione un estado...</option>
                </select>

                <label>Ciudad:</label>
                <select id="input_ciudad_id" name="ciudad_id" required>
                    <option value="">Seleccione una ciudad...</option>
                </select>
                
                <input type="submit" id="submitBtn" class="btn-submit" value="Guardar">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('docenteModal');

        // AJAX para combos dinámicos
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

        function openModal() {
            document.getElementById('modalTitle').innerText = "Nuevo Docente";
            document.getElementById('docente_id_edit').value = "";
            document.getElementById('input_nombre').value = "";
            document.getElementById('input_apellido').value = "";
            document.getElementById('input_ci').value = "";
            document.getElementById('input_telefono').value = "";
            document.getElementById('input_correo').value = "";
            document.getElementById('input_pais_id').value = "";
            document.getElementById('input_estado_id').innerHTML = '<option value="">Seleccione un estado...</option>';
            document.getElementById('input_ciudad_id').innerHTML = '<option value="">Seleccione una ciudad...</option>';
            document.getElementById('submitBtn').value = "Registrar Docente";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editDocente(datos) {
            document.getElementById('modalTitle').innerText = "Editar Docente";
            document.getElementById('docente_id_edit').value = datos.Docente_ID;
            document.getElementById('input_nombre').value = datos.Nombre;
            document.getElementById('input_apellido').value = datos.Apellido;
            document.getElementById('input_ci').value = datos.CI;
            document.getElementById('input_telefono').value = datos.Telefono;
            document.getElementById('input_correo').value = datos.Correo;
            
            // Ubicación geografía
            const paisId = datos.Pais_ID;
            const estadoId = datos.Estado_ID;
            const ciudadId = datos.Ciudad_ID;
            
            document.getElementById('input_pais_id').value = paisId || "";
            
            if (paisId) {
                fetch('get_location.php?pais_id=' + paisId)
                    .then(res => res.json())
                    .then(states => {
                        const estadoSelect = document.getElementById('input_estado_id');
                        estadoSelect.innerHTML = '<option value="">Seleccione un estado...</option>';
                        states.forEach(est => {
                            const selected = est.Estado_ID == estadoId ? 'selected' : '';
                            estadoSelect.innerHTML += `<option value="${est.Estado_ID}" ${selected}>${est.Estado}</option>`;
                        });
                        
                        if (estadoId) {
                            fetch('get_location.php?estado_id=' + estadoId)
                                .then(res => res.json())
                                .then(cities => {
                                    const ciudadSelect = document.getElementById('input_ciudad_id');
                                    ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad...</option>';
                                    cities.forEach(c => {
                                        const selected = c.Ciudad_ID == ciudadId ? 'selected' : '';
                                        ciudadSelect.innerHTML += `<option value="${c.Ciudad_ID}" ${selected}>${c.Ciudad}</option>`;
                                    });
                                });
                        } else {
                            document.getElementById('input_ciudad_id').innerHTML = '<option value="">Seleccione una ciudad...</option>';
                        }
                    });
            } else {
                document.getElementById('input_estado_id').innerHTML = '<option value="">Seleccione un estado...</option>';
                document.getElementById('input_ciudad_id').innerHTML = '<option value="">Seleccione una ciudad...</option>';
            }
            
            document.getElementById('submitBtn').value = "Actualizar Docente";
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