<?php
require 'conexion.php';

// Mimic POST request for new student
$_POST['curso_id'] = 1; // Assuming there is a course with ID 1
$_POST['cohorte_id'] = 1; // Assuming cohorte 1
$_POST['status_pago'] = 'Pendiente';
$_POST['nota_minima'] = '14';
$_POST['pago_id'] = 0;
$_POST['inscripcion_id_edit'] = '';
$_POST['estudiante_id'] = '';

$_POST['tipo_estudiante'] = 'nuevo';
$_POST['new_nombre'] = 'Test';
$_POST['new_apellido'] = 'User';
$_POST['new_ci'] = '123456';
$_POST['new_telefono'] = '123456';
$_POST['new_direccion'] = 'Test Dir';
$_POST['new_sede_id'] = 1; // Assuming Sede 1 exists

// Run logic
$curso_id = $_POST['curso_id'];
$cohorte_id = $_POST['cohorte_id'];
$status_pago = $_POST['status_pago'];
$nota_minima = $_POST['nota_minima'];
$pago_id = $_POST['pago_id'] == '0' ? 'NULL' : "'".$_POST['pago_id']."'";
$id_edit = $_POST['inscripcion_id_edit'];

$estudiante_id = $_POST['estudiante_id'];

if (isset($_POST['tipo_estudiante']) && $_POST['tipo_estudiante'] == 'nuevo' && empty($id_edit)) {
    $nombre = $_POST['new_nombre'];
    $apellido = $_POST['new_apellido'];
    $ci = $_POST['new_ci'];
    $telefono = $_POST['new_telefono'];
    $direccion = $_POST['new_direccion'];
    $sede_id = $_POST['new_sede_id'];
    
    // 1. Crear Usuario
    $fecha_actual = date('Y-m-d');
    mysqli_query($conn, "INSERT INTO Usuarios (Rol, Fecha) VALUES ('Estudiante', '$fecha_actual')");
    $nuevo_usuario_id = mysqli_insert_id($conn);
    
    // 2. Crear Estudiante
    $sql_est = "INSERT INTO ESTUDIANTES (Nombre, Apellido, CI, Telefono, Direccion, Sede_ID, Usuario_ID) 
                VALUES ('$nombre', '$apellido', '$ci', '$telefono', '$direccion', '$sede_id', '$nuevo_usuario_id')";
    if(mysqli_query($conn, $sql_est)) {
        $estudiante_id = mysqli_insert_id($conn);
        echo "Created student ID: $estudiante_id\n";
    } else {
        echo "Error al crear estudiante: " . mysqli_error($conn) . "\n";
    }
}

if (!empty($id_edit)) {
    $sql = "UPDATE inscripciones SET Curso_ID='$curso_id', Estudiante_ID='$estudiante_id', Cohorte_ID='$cohorte_id', Status_Pago='$status_pago', Nota_minima='$nota_minima', Pago_ID=$pago_id WHERE Inscripcion_ID=$id_edit";
} else {
    $sql = "INSERT INTO inscripciones (Curso_ID, Estudiante_ID, Cohorte_ID, Status_Pago, Nota_minima, Pago_ID) VALUES ('$curso_id', '$estudiante_id', '$cohorte_id', '$status_pago', '$nota_minima', $pago_id)";
}

if (mysqli_query($conn, $sql)) {
    echo "Inscription successful!\n";
} else {
    echo "Error on inscription: " . mysqli_error($conn) . "\n";
}
?>
