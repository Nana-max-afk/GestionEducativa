<?php
require 'conexion.php';
$tables = ['ESTUDIANTES', 'inscripciones'];
foreach($tables as $t) {
    echo "--- $t ---\n";
    $res = mysqli_query($conn, "DESCRIBE $t");
    if($res) {
        while($r = mysqli_fetch_assoc($res)) {
            echo $r['Field'] . ' (' . $r['Type'] . ") - Null: " . $r['Null'] . "\n";
        }
    } else {
        echo "Table not found\n";
    }
}
?>
