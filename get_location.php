<?php
require_once("conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['pais_id'])) {
    $pais_id = (int)$_GET['pais_id'];
    $sql = "SELECT Estado_ID, Estado FROM estados WHERE Pais_ID = ? ORDER BY Estado ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $pais_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_all($res, MYSQLI_ASSOC);
    echo json_encode($data);
    exit;
}

if (isset($_GET['estado_id'])) {
    $estado_id = (int)$_GET['estado_id'];
    $sql = "SELECT Ciudad_ID, Ciudad FROM ciudades WHERE Estado_ID = ? ORDER BY Ciudad ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $estado_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_all($res, MYSQLI_ASSOC);
    echo json_encode($data);
    exit;
}

echo json_encode([]);
?>
