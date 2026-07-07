<?php
require_once("conexion.php");
include("header.php");

$sql = "SHOW TABLES";
$result = mysqli_query($conn, $sql);
?>

<div style="width: 85%; margin: 20px auto;">
    <h1>Estructura de la Base de Datos</h1>
    <p>Listado completo de tablas y columnas del sistema.</p>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre de la Tabla</th>
            <th>Columnas (Estructura)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_row($result)) {
                $table = $row[0];
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($table, ENT_QUOTES, 'UTF-8') . "</strong></td>";
                
                // Buscar las columnas de esta tabla
                $col_sql = "DESCRIBE $table";
                $col_result = mysqli_query($conn, $col_sql);
                
                $columnas = [];
                while ($col_row = mysqli_fetch_assoc($col_result)) {
                    // Guardamos el nombre del campo y su tipo de dato (ej: int, varchar)
                    $columnas[] = "<span style='background:#eef2f5; padding:3px 6px; border-radius:4px; margin:2px; display:inline-block; font-size:12px;'>" . $col_row['Field'] . " <em style='color:#888;'>(" . $col_row['Type'] . ")</em></span>";
                }
                
                echo "<td>" . implode(" ", $columnas) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No se encontraron tablas en la base de datos.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php 
mysqli_close($conn);
include("footer.php"); 
?>