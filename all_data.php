<?php require_once("conexion.php");
?>

<?php include("header.php"); ?>
<h1>Todos los Datos de la Base de Datos</h1>
    <?php
    // Get all tables
    $tables_sql = "SHOW TABLES";
    $tables_result = mysqli_query($conn, $tables_sql);

    if (mysqli_num_rows($tables_result) > 0) {
        while ($table_row = mysqli_fetch_row($tables_result)) {
            $table_name = $table_row[0];
            echo "<h2>Tabla: $table_name</h2>";

            $data_sql = "SELECT * FROM $table_name";
            $data_result = mysqli_query($conn, $data_sql);

            if (mysqli_num_rows($data_result) > 0) {
                // Get column names
                $columns = mysqli_fetch_fields($data_result);
                echo "<div class=\"table-container\">\n        <table>";
                echo "<tr>";
                foreach ($columns as $column) {
                    echo "<th>" . $column->name . "</th>";
                }
                echo "</tr>";

                // Reset pointer
                mysqli_data_seek($data_result, 0);

                while ($row = mysqli_fetch_assoc($data_result)) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . $value . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>\n    </div>";
            } else {
                echo "<p>No hay datos en esta tabla.</p>";
            }
        }
    } else {
        echo "<p>No hay tablas en la base de datos.</p>";
    }

    mysqli_close($conn);
    ?>
<?php include("footer.php"); ?>

