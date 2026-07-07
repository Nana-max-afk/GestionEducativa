<?php 
require_once("conexion.php");


if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    mysqli_query($conn, "DELETE FROM empresa WHERE Empresa_ID = $id_eliminar");
    header("Location: empresa.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rif'])) {
    $rif = $_POST['rif'];
    $logo_empresa = $_POST['logo_empresa'];
    $direccion = $_POST['direccion'];
    $correo = $_POST['correo'];
    $usuario_id = $_POST['usuario_id'];
    $id_edit = $_POST['empresa_id_edit'];

    if (!empty($id_edit)) {
        $sql = "UPDATE empresa SET RIF='$rif', Logo_Empresa='$logo_empresa', Direccion='$direccion', Correo='$correo', Usuario_ID='$usuario_id' WHERE Empresa_ID=$id_edit";
    } else {
        $sql = "INSERT INTO empresa (RIF, Logo_Empresa, Direccion, Correo, Usuario_ID) VALUES ('$rif', '$logo_empresa', '$direccion', '$correo', '$usuario_id')";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: empresa.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

    <?php include("header.php"); ?>
<div class="header-actions">
        <button class="add-button" onclick="openModal()">
            <i class="fa-solid fa-city"></i> Nueva Empresa
        </button>
    </div>

    <h1>Lista de Empresas</h1>

    <div class="table-container">        <table>
        <thead>
            <tr>
                <th>Empresa_ID</th>
                <th>RIF</th>
                <th>Logo_Empresa</th>
                <th>Direccion</th>
                <th>Correo</th>
                <th>Usuario_ID</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $result = mysqli_query($conn, "SELECT * FROM empresa");
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row["Empresa_ID"]; ?></td>
                    <td><?php echo $row["RIF"]; ?></td>
                    <td><?php echo $row["Logo_Empresa"]; ?></td>
                    <td><?php echo $row["Direccion"]; ?></td>
                    <td><?php echo $row["Correo"]; ?></td>
                    <td><?php echo $row["Usuario_ID"]; ?></td>
                    <td>
                        <i class="fa-solid fa-pen-to-square" style="color: #2196F3; cursor:pointer; margin-right: 15px;" 
                           onclick='editEmpresa(<?php echo json_encode($row); ?>)'></i>
                        
                        <a href="empresa.php?eliminar=<?php echo $row['Empresa_ID']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta empresa?')">
                            <i class="fa-solid fa-trash" style="color: #f44336;"></i>
                        </a>
                    </td>
                </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='7'>No hay empresas registradas.</td></tr>";
            }
            ?>
        </tbody>
    </table>    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Nueva Empresa</h2>
            <hr>
            <form method="post" action="">
                <input type="hidden" id="empresa_id_edit" name="empresa_id_edit">
                
                <label for="rif">RIF:</label>
                <input type="text" id="input_rif" name="rif" required>
                
                <label for="logo_empresa">Logo Empresa:</label>
                <input type="text" id="input_logo_empresa" name="logo_empresa" required>
                
                <label for="direccion">Dirección:</label>
                <input type="text" id="input_direccion" name="direccion" required>
                
                <label for="correo">Correo:</label>
                <input type="email" id="input_correo" name="correo" required>
                
                <label for="usuario_id">Usuario ID:</label>
                <input type="number" id="input_usuario_id" name="usuario_id" required>
                
                <input type="submit" id="submitBtn" value="Guardar Empresa">
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('addModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Registrar Empresa";
            document.getElementById('empresa_id_edit').value = "";
            document.getElementById('input_rif').value = "";
            document.getElementById('input_logo_empresa').value = "";
            document.getElementById('input_direccion').value = "";
            document.getElementById('input_correo').value = "";
            document.getElementById('input_usuario_id').value = "";
            document.getElementById('submitBtn').value = "Guardar Empresa";
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function editEmpresa(datos) {
            document.getElementById('modalTitle').innerText = "Editar Empresa";
            document.getElementById('empresa_id_edit').value = datos.Empresa_ID;
            document.getElementById('input_rif').value = datos.RIF;
            document.getElementById('input_logo_empresa').value = datos.Logo_Empresa;
            document.getElementById('input_direccion').value = datos.Direccion;
            document.getElementById('input_correo').value = datos.Correo;
            document.getElementById('input_usuario_id').value = datos.Usuario_ID;
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


