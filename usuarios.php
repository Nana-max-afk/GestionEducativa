<?php
require_once("functions.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$flash = get_flash();

if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    $stmt = db_prepare('DELETE FROM usuarios WHERE Usuario_ID = ?');
    if ($stmt) {
        $stmt->bind_param('i', $id_eliminar);
        $stmt->execute();
        $stmt->close();
    }
    redirect('usuarios.php');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['rol'])) {
    if (!validate_csrf($_POST['_csrf'] ?? '')) {
        flash('Token CSRF inválido.', 'danger');
        redirect('usuarios.php');
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = trim($_POST['rol'] ?? '');
    $status = in_array($_POST['status'] ?? 'Activo', ['Activo', 'Inactivo'], true) ? $_POST['status'] : 'Activo';
    $fecha = date('Y-m-d');
    $id_edit = !empty($_POST['usuario_id_edit']) ? (int)$_POST['usuario_id_edit'] : null;

    if ($username === '' || $rol === '') {
        flash('Complete el nombre de usuario y el rol.', 'danger');
        redirect('usuarios.php');
    }

    if ($id_edit) {
        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = db_prepare('UPDATE usuarios SET Username = ?, PasswordHash = ?, Rol = ?, Status = ?, Fecha = ? WHERE Usuario_ID = ?');
            if ($stmt) {
                $stmt->bind_param('sssssi', $username, $passwordHash, $rol, $status, $fecha, $id_edit);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $stmt = db_prepare('UPDATE usuarios SET Username = ?, Rol = ?, Status = ?, Fecha = ? WHERE Usuario_ID = ?');
            if ($stmt) {
                $stmt->bind_param('ssssi', $username, $rol, $status, $fecha, $id_edit);
                $stmt->execute();
                $stmt->close();
            }
        }
        flash('Usuario actualizado correctamente.', 'success');
        redirect('usuarios.php');
    }

    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        flash('La contraseña debe tener al menos ' . PASSWORD_MIN_LENGTH . ' caracteres.', 'danger');
        redirect('usuarios.php');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db_prepare('INSERT INTO usuarios (Username, PasswordHash, Rol, Status, Fecha) VALUES (?, ?, ?, ?, ?)');
    if ($stmt) {
        $stmt->bind_param('sssss', $username, $passwordHash, $rol, $status, $fecha);
        if ($stmt->execute()) {
            flash('Usuario creado correctamente.', 'success');
        } else {
            flash('Error al crear usuario: ' . db_connect()->error, 'danger');
        }
        $stmt->close();
    }
    redirect('usuarios.php');
}

$search = trim($_GET['search'] ?? '');
$users = [];
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=usuarios.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Usuario_ID', 'Username', 'Rol', 'Status', 'Fecha']);

    if ($search !== '') {
        $like = '%' . $search . '%';
        $stmt = db_prepare('SELECT Usuario_ID, Username, Rol, Status, Fecha FROM usuarios WHERE Username LIKE ? OR Rol LIKE ? OR Status LIKE ? ORDER BY Usuario_ID DESC');
        $stmt->bind_param('sss', $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = db_query('SELECT Usuario_ID, Username, Rol, Status, Fecha FROM usuarios ORDER BY Usuario_ID DESC');
    }

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [$row['Usuario_ID'], $row['Username'], $row['Rol'], $row['Status'], $row['Fecha']]);
    }
    fclose($output);
    exit();
}

if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = db_prepare('SELECT Usuario_ID, Username, Rol, Status, Fecha FROM usuarios WHERE Username LIKE ? OR Rol LIKE ? OR Status LIKE ? ORDER BY Usuario_ID DESC');
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = db_query('SELECT Usuario_ID, Username, Rol, Status, Fecha FROM usuarios ORDER BY Usuario_ID DESC');
}

while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>

<?php include("header.php"); ?>

<div class="table-actions">
    <button class="add-button" onclick="openModal()">
        <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
    </button>
    <form class="search-form" method="get" action="usuarios.php">
        <input type="search" name="search" placeholder="Buscar usuarios, roles o estado" value="<?php echo escape($search); ?>">
        <button type="submit" class="export-button">Buscar</button>
        <button type="submit" name="export" value="csv" class="export-button">Exportar CSV</button>
    </form>
</div>

<?php if ($flash): ?>
    <div class="message <?php echo escape($flash['type']); ?>"><?php echo escape($flash['message']); ?></div>
<?php endif; ?>

<h1>Lista de Usuarios</h1>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $row): ?>
                    <tr>
                        <td><?php echo escape($row['Usuario_ID']); ?></td>
                        <td><?php echo escape($row['Username']); ?></td>
                        <td><?php echo escape($row['Rol']); ?></td>
                        <td><span class="badge <?php echo $row['Status'] === 'Activo' ? 'success' : 'danger'; ?>"><?php echo escape($row['Status']); ?></span></td>
                        <td><?php echo escape($row['Fecha']); ?></td>
                        <td>
                            <i class="fa-solid fa-pen-to-square" onclick='editUsuario(<?php echo json_encode($row, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'></i>
                            <a href="usuarios.php?eliminar=<?php echo (int)$row['Usuario_ID']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Nuevo Usuario</h2>
        <hr>
        <form method="post" action="usuarios.php">
            <input type="hidden" id="usuario_id_edit" name="usuario_id_edit">
            <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">

            <label for="input_username">Usuario</label>
            <input type="text" id="input_username" name="username" required>

            <label for="input_password">Contraseña</label>
            <input type="password" id="input_password" name="password" placeholder="Dejar vacío al editar para conservar">
            <small style="display:block; margin-top:6px; color:#6b7280;">La contraseña es obligatoria para nuevos usuarios.</small>

            <label for="input_rol">Rol</label>
            <input type="text" id="input_rol" name="rol" required>

            <label for="input_status">Estado</label>
            <select id="input_status" name="status">
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
            </select>

            <input type="submit" id="submitBtn" class="btn-submit" value="Guardar Usuario">
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('addModal');

    function openModal() {
        document.getElementById('modalTitle').innerText = 'Registrar Usuario';
        document.getElementById('usuario_id_edit').value = '';
        document.getElementById('input_username').value = '';
        document.getElementById('input_password').value = '';
        document.getElementById('input_rol').value = '';
        document.getElementById('input_status').value = 'Activo';
        document.getElementById('submitBtn').value = 'Guardar Usuario';
        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function editUsuario(datos) {
        document.getElementById('modalTitle').innerText = 'Editar Usuario';
        document.getElementById('usuario_id_edit').value = datos.Usuario_ID;
        document.getElementById('input_username').value = datos.Username;
        document.getElementById('input_password').value = '';
        document.getElementById('input_rol').value = datos.Rol;
        document.getElementById('input_status').value = datos.Status;
        document.getElementById('submitBtn').value = 'Actualizar Usuario';
        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    window.onclick = function(event) {
        if (event.target === modal) closeModal();
    };
</script>

<?php include("footer.php"); ?>


