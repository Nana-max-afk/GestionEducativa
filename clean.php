<?php
$files = glob("*.php");
foreach($files as $f) {
    if (in_array($f, ["header.php", "footer.php", "conexion.php"])) continue;
    $c = file_get_contents($f);
    if (strpos($c, '') !== false) {
        $c = str_replace('', '', $c);
        file_put_contents($f, $c);
        echo "Cleaned $f<br>";
    }
}
echo "Clean Done";
?>
