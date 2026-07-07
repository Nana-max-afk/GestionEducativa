<?php
$files = glob("*.php");
$exclude = ['conexion.php', 'header.php', 'footer.php', 'index.php'];

foreach ($files as $file) {
    if (in_array($file, $exclude)) continue;
    $content = file_get_contents($file);
    
    // Remove tags
    $content = preg_replace('/<\/body>\s*/i', '', $content);

    // Write back
    file_put_contents($file, $content);
    echo "Fixed: $file\n";
}
echo "Done.";
?>
