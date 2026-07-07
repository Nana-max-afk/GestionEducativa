<?php
$files = glob("*.php");
foreach($files as $f) {
    if (in_array($f, ["header.php", "footer.php", "conexion.php", "logout.php", "check_tables.php", "fix.php"])) continue;
    
    $content = file_get_contents($f);
    
    // Remove all existing includes of header.php
    $content = preg_replace('/<\?php\s*include\("header\.php"\);\s*\?>\s*/', '', $content);
    $content = preg_replace('/include\("header\.php"\);/', '', $content); // Just in case there are raw ones
    
    // Find the first HTML element where we want to inject it.
    // The main content usually starts with <div class="header-actions">, <h1>, <style>, or <div class="dashboard-container">
    $patterns = [
        '<div class="header-actions">', 
        '<style>', 
        '<h1>', 
        '<div class="dashboard-container">',
        '<div class="table-container">'
    ];
    
    $inserted = false;
    $min_pos = strlen($content);
    $best_pattern = "";
    
    foreach($patterns as $p) {
        $pos = strpos($content, $p);
        if ($pos !== false && $pos < $min_pos) {
            $min_pos = $pos;
            $best_pattern = $p;
        }
    }
    
    if ($best_pattern !== "") {
        $content = substr_replace($content, "<?php include(\"header.php\"); ?>\n", $min_pos, 0);
        file_put_contents($f, $content);
        echo "Fixed $f<br>\n";
    } else {
        echo "Could not find insertion point for $f<br>\n";
    }
}
echo "Done";
?>
