<?php
require 'conexion.php';
$sql = "ALTER TABLE asistenciaestudiante MODIFY Asistencia VARCHAR(50)";
if(mysqli_query($conn, $sql)) {
    echo "Successfully altered Asistencia to VARCHAR(50).";
} else {
    echo "Error altering table: " . mysqli_error($conn);
}

// Add an ID to the table just in case they need to edit a specific row easily
$sql2 = "ALTER TABLE asistenciaestudiante ADD Asistencia_ID INT AUTO_INCREMENT PRIMARY KEY FIRST";
mysqli_query($conn, $sql2); // ignore if fails (might already have primary key or duplicate)

echo "<br>Done.";
?>
