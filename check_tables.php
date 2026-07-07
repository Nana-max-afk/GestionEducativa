<?php
require 'conexion.php';
$tables = ['DOCENTES', 'CURSOS', 'Ingresos', 'Gastos', 'cohortes'];
foreach($tables as $t) {
    echo "--- $t ---\n";
    $res = mysqli_query($conn, "DESCRIBE $t");
    if($res) {
        while($r = mysqli_fetch_assoc($res)) {
            echo $r['Field'] . ' (' . $r['Type'] . ")\n";
        }
    } else {
        echo "Table not found\n";
    }
}
?>
