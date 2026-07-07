<?php
require_once 'conexion.php';

// Set headers for plain text output if run via browser
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

$sql_dump = "-- SQL Dump
-- Database: $dbname
-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";

$sql_dump .= "CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
$sql_dump .= "USE `$dbname`;\n\n";

// Disable foreign keys during import
$sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n";
$sql_dump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$sql_dump .= "SET time_zone = \"+00:00\";\n\n";

// Get all tables
$tables_res = mysqli_query($conn, "SHOW TABLES");
if (!$tables_res) {
    die("Error retrieving tables: " . mysqli_error($conn) . "\n");
}

$tables = [];
while ($row = mysqli_fetch_row($tables_res)) {
    $tables[] = $row[0];
}

echo "Found " . count($tables) . " tables.\n";

foreach ($tables as $table) {
    echo "Dumping structure and data for table: $table...\n";
    $sql_dump .= "-- --------------------------------------------------------\n";
    $sql_dump .= "-- Table structure for table `$table`\n";
    $sql_dump .= "-- --------------------------------------------------------\n\n";
    
    // Drop table if exists
    $sql_dump .= "DROP TABLE IF EXISTS `$table`;\n";
    
    // Get CREATE TABLE
    $create_res = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
    if ($create_res) {
        $create_row = mysqli_fetch_row($create_res);
        $sql_dump .= $create_row[1] . ";\n\n";
    } else {
        echo "Error getting structure for $table: " . mysqli_error($conn) . "\n";
        continue;
    }
    
    // Get DATA
    $data_res = mysqli_query($conn, "SELECT * FROM `$table`");
    if ($data_res && mysqli_num_rows($data_res) > 0) {
        $sql_dump .= "-- Dumping data for table `$table`\n\n";
        
        while ($row = mysqli_fetch_assoc($data_res)) {
            $fields = array_keys($row);
            $escaped_values = [];
            foreach ($row as $val) {
                if ($val === null) {
                    $escaped_values[] = "NULL";
                } else {
                    $escaped_values[] = "'" . mysqli_real_escape_string($conn, $val) . "'";
                }
            }
            
            $sql_dump .= "INSERT INTO `$table` (`" . implode("`, `", $fields) . "`) VALUES (" . implode(", ", $escaped_values) . ");\n";
        }
        $sql_dump .= "\n";
    } else {
        $sql_dump .= "-- No data found in `$table`\n\n";
    }
}

// Enable foreign keys
$sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";

// Save to file
$output_file = 'gestion_educativa.sql';
if (file_put_contents($output_file, $sql_dump)) {
    echo "\nDatabase successfully dumped to: $output_file (" . number_format(strlen($sql_dump)) . " bytes)\n";
} else {
    echo "\nError saving dump to $output_file\n";
}
?>
