<?php
require 'conexion.php';

echo "--- MIGRACIÓN: AGREGAR RELACIONES A USUARIOS PARA RBAC ---\n";

// 1. Agregar columnas a usuarios si no existen
$sql_check = "SHOW COLUMNS FROM usuarios LIKE 'Estudiante_ID'";
$res_check = mysqli_query($conn, $sql_check);
if (mysqli_num_rows($res_check) == 0) {
    $sql_alter = "ALTER TABLE usuarios 
        ADD COLUMN Estudiante_ID INT DEFAULT NULL AFTER Rol,
        ADD COLUMN Docente_ID INT DEFAULT NULL AFTER Estudiante_ID,
        ADD CONSTRAINT fk_usuarios_estudiantes FOREIGN KEY (Estudiante_ID) REFERENCES ESTUDIANTES (Estudiantes_ID) ON DELETE SET NULL ON UPDATE CASCADE,
        ADD CONSTRAINT fk_usuarios_docentes FOREIGN KEY (Docente_ID) REFERENCES DOCENTES (Docente_ID) ON DELETE SET NULL ON UPDATE CASCADE";
    if (mysqli_query($conn, $sql_alter)) {
        echo "Columnas Estudiante_ID y Docente_ID con llaves foráneas creadas exitosamente en la tabla usuarios.\n";
    } else {
        echo "Error al crear las columnas: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Las columnas de relación ya existen en la tabla usuarios.\n";
}

// 2. Modificar columna Rol si es necesario
// Actualmente es un varchar(100). Podemos dejarlo así pero nos aseguraremos de que contenga 'Administrador', 'Docente' o 'Estudiante'.

// 3. Insertar/Actualizar usuarios de prueba con contraseñas seguras
$usuarios_prueba = [
    [
        'username' => 'admin',
        'password' => 'admin123',
        'rol' => 'Administrador',
        'status' => 'Activo'
    ],
    [
        'username' => 'docente',
        'password' => 'docente123',
        'rol' => 'Docente',
        'status' => 'Activo'
    ],
    [
        'username' => 'estudiante',
        'password' => 'estudiante123',
        'rol' => 'Estudiante',
        'status' => 'Activo'
    ]
];

foreach ($usuarios_prueba as $u) {
    $username = $u['username'];
    // Buscar si ya existe
    $stmt_check = mysqli_prepare($conn, "SELECT Usuario_ID FROM usuarios WHERE Username = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    
    if (mysqli_stmt_num_rows($stmt_check) == 0) {
        mysqli_stmt_close($stmt_check);
        
        $hash = password_hash($u['password'], PASSWORD_DEFAULT);
        $rol = $u['rol'];
        $status = $u['status'];
        $fecha = date('Y-m-d');
        
        $stmt_ins = mysqli_prepare($conn, "INSERT INTO usuarios (Username, PasswordHash, Rol, Status, Fecha) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_ins, "sssss", $username, $hash, $rol, $status, $fecha);
        if (mysqli_stmt_execute($stmt_ins)) {
            echo "Usuario de prueba creado: '$username' con rol '$rol' (contraseña: '{$u['password']}')\n";
        } else {
            echo "Error al crear usuario de prueba '$username': " . mysqli_error($conn) . "\n";
        }
        mysqli_stmt_close($stmt_ins);
    } else {
        mysqli_stmt_close($stmt_check);
        echo "El usuario de prueba '$username' ya existe.\n";
    }
}

echo "Migración finalizada con éxito.\n";
?>
