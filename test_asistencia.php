<?php
require 'conexion.php';
$res = mysqli_query($conn, "DESCRIBE asistenciaestudiante");
if($res) {
    while($r = mysqli_fetch_assoc($res)) {
        echo $r['Field'] . ' (' . $r['Type'] . ")<br>\n";
    }
}
?>
