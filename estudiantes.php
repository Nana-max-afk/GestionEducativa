<?php 
require_once("conexion.php");

// Cargar sedes para el select
$res_sedes = mysqli_query($conn, "SELECT Sede_ID, Sede FROM SEDES");

// Cargar países para el select
$res_paises = mysqli_query($conn, "SELECT Pais_ID, Pais FROM paises ORDER BY Pais ASC");

// --- 1. LÓGICA PARA ELIMINAR ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    
    // Validar si el alumno ya está inscrito en algún lado
    $sql_check = "SELECT COUNT(*) as total FROM inscripciones WHERE Estudiante_ID = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id_eliminar);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($res_check);
    mysqli_stmt_close($stmt_check);

    if ($row_check['total'] > 0) {
        echo "<script>alert('No se puede eliminar el estudiante: tiene inscripciones activas.'); window.location='estudiantes.php';</script>";
        exit();
    }

    $sql_del = "DELETE FROM ESTUDIANTES WHERE Estudiantes_ID = ?";
    $stmt_del = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt_del, "i", $id_eliminar);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
    header("Location: estudiantes.php");
    exit();
}

// --- 2. LÓGICA PARA INSERTAR O EDITAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $ci = $_POST['ci'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $sede_id = (int)$_POST['sede_id'];
    
    $pais_id = $_POST['pais_id'] == "" ? null : (int)$_POST['pais_id'];
    $estado_id = $_POST['estado_id'] == "" ? null : (int)$_POST['estado_id'];
    $ciudad_id = $_POST['ciudad_id'] == "" ? null : (int)$_POST['ciudad_id'];
    
    $id_edit = $_POST['estudiante_id_edit'];

    if (!empty($id_edit)) {
        $id_edit = (int)$id_edit;
        $sql = "UPDATE ESTUDIANTES SET Nombre=?, Apellido=?, CI=?, Telefono=?, Correo=?, Sede_ID=?, Pais_ID=?, Estado_ID=?, Ciudad_ID=? WHERE Estudiantes_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssiiiii", $nombre, $apellido, $ci, $telefono, $correo, $sede_id, $pais_id, $estado_id, $ciudad_id, $id_edit);
    } else {
        $sql = "INSERT INTO ESTUDIANTES (Nombre, Apellido, CI, Telefono, Correo, Sede_ID, Pais_ID, Estado_ID, Ciudad_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssiiii", $nombre, $apellido, $ci, $telefono, $correo, $sede_id, $pais_id, $estado_id, $ciudad_id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: estudiantes.php");
        exit();
    } else {
        echo "Error al guardar el estudiante: " . mysqli_error($conn);
    }
}

include("header.php");
// Query relacional completo con sedes y geografía
$res_estudiantes_lista = mysqli_query($conn, "SELECT e.*, s.Sede, p.Pais, est.Estado, c.Ciudad 
    FROM ESTUDIANTES e 
    JOIN SEDES s ON e.Sede_ID = s.Sede_ID
    LEFT JOIN paises p ON e.Pais_ID = p.Pais_ID
    LEFT JOIN estados est ON e.Estado_ID = est.Estado_ID
    LEFT JOIN ciudades c ON e.Ciudad_ID = c.Ciudad_ID");
?>

    <div class="header-actions">
        <button class="add-button" onclick="openModal()">Nuevo Estudiante</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Cédula</th>
                <th>Sede</th>
                <th>Ubicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($res_estudiantes_lista)) { ?>
            <tr>
                <td><?php echo $row['Estudiantes_ID']; ?></td>
                <td><?php echo htmlspecialchars($row['Nombre'] . " " . $row['Apellido'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['CI'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['Sede'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?php 
                    $location_parts = array_filter([$row['Pais'] ?? null, $row['Estado'] ?? null, $row['Ciudad'] ?? null]);
                    echo htmlspecialchars(implode(" - ", $location_parts), ENT_QUOTES, 'UTF-8') ?: '<span style="color:#aaa;">No registrada</span>'; 
                    ?>
                </td>
                <td>
                    <a href="javascript:void(0);" onclick='editEstudiante(<?php echo json_encode($row); ?>)' style="margin-right: 10px; color: #2196F3;"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:void(0);" onclick="if(confirm('¿Deseas eliminar este estudiante?')) window.location.href='estudiantes.php?eliminar=<?php echo $row['Estudiantes_ID']; ?>';" style="color: #e74c3c;"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="estudianteModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nuevo Estudiante</h2>
            
            <form action="estudiantes.php" method="POST">
                <input type="hidden" id="estudiante_id_edit" name="estudiante_id_edit">
                
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

                <label>Sede:</label>
                <select id="input_sede_id" name="sede_id" required>
                    <?php mysqli_data_seek($res_sedes, 0); while($s = mysqli_fetch_assoc($res_sedes)) { ?>
                        <option value="<?php echo $s['Sede_ID']; ?>"><?php echo htmlspecialchars($s['Sede'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                </select>

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
        const modal = document.getElementById('estudianteModal');

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
            document.getElementById('modalTitle').innerText = "Nuevo Estudiante";
            document.getElementById('estudiante_id_edit').value = "";
            document.getElementById('input_nombre').value = "";
            document.getElementById('input_apellido').value = "";
            document.getElementById('input_ci').value = "";
            document.getElementById('input_telefono').value = "";
            document.getElementById('input_correo').value = "";
            document.getElementById('input_pais_id').value = "";
            document.getElementById('input_estado_id').innerHTML = '<option value="">Seleccione un estado...</option>';
            document.getElementById('input_ciudad_id').innerHTML = '<option value="">Seleccione una ciudad...</option>';
            document.getElementById('submitBtn').value = "Registrar Estudiante";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editEstudiante(datos) {
            document.getElementById('modalTitle').innerText = "Editar Estudiante";
            document.getElementById('estudiante_id_edit').value = datos.Estudiantes_ID;
            document.getElementById('input_nombre').value = datos.Nombre;
            document.getElementById('input_apellido').value = datos.Apellido;
            document.getElementById('input_ci').value = datos.CI;
            document.getElementById('input_telefono').value = datos.Telefono;
            document.getElementById('input_correo').value = datos.Correo;
            document.getElementById('input_sede_id').value = datos.Sede_ID;
            
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
            
            document.getElementById('submitBtn').value = "Actualizar Estudiante";
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