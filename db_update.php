<?php
require 'conexion.php';

// 1. ADD COLUMNS TO DOCENTES
$sql_docentes = "ALTER TABLE DOCENTES ADD COLUMN Curso_ID INT DEFAULT NULL, ADD COLUMN Especialidad VARCHAR(100) DEFAULT NULL";
if(mysqli_query($conn, $sql_docentes)){ echo "Added columns to DOCENTES\n"; } else { echo "Error or already exists: " . mysqli_error($conn) . "\n"; }

// 2. CREATE HORARIOS TABLE
$sql_horarios = "CREATE TABLE IF NOT EXISTS HORARIOS (
    Horario_ID INT AUTO_INCREMENT PRIMARY KEY,
    Curso_ID INT NOT NULL,
    Dia_Semana VARCHAR(50) NOT NULL,
    Hora_Inicio TIME NOT NULL,
    Hora_Fin TIME NOT NULL
)";
if(mysqli_query($conn, $sql_horarios)){ echo "Created HORARIOS table\n"; } else { echo "Error: " . mysqli_error($conn) . "\n"; }

// 3. ADD MONTO TO FINANCIAL TABLES
$financial_tables = ['PagosEstudiantes', 'pagosdocente', 'Ingresos', 'Gastos'];
foreach($financial_tables as $t) {
    $sql_monto = "ALTER TABLE $t ADD COLUMN Monto DECIMAL(10,2) DEFAULT 0.00";
    if(mysqli_query($conn, $sql_monto)){ echo "Added Monto to $t\n"; } else { echo "Error or already exists for $t: " . mysqli_error($conn) . "\n"; }
}
?>
