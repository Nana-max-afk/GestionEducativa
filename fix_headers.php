<?php
$files = glob("*.php");
foreach($files as $f) {
    if ($f == "header.php" || $f == "footer.php" || $f == "conexion.php" || $f == "logout.php" || $f == "check_tables.php") continue;
    
    $content = file_get_contents($f);
    
    // Check if it has include("header.php") at the top before POST logic
    if (strpos($content, '') !== false) {
        // Remove it from the top
        $content = str_replace('', '', $content);
        
        // Find where the logic ends. Usually right before the first HTML tag or after the first large <?php block
        // Actually, we can just find the first ?> and put it right before it, but some files have multiple <?php tags.
        // It's safer to find the first <?php include("header.php"); ?>
<div class="header-actions"> or <div or <style> or <h1> and insert <?php  ?> right before it.
        
        $patterns = ['<style>', '<div class="header-actions">', '<h1>', '<div'];
        $inserted = false;
        foreach($patterns as $p) {
            $pos = strpos($content, $p);
            if ($pos !== false) {
                $content = substr_replace($content, "<?php include(\"header.php\"); ?>\n    ", $pos, 0);
                $inserted = true;
                break;
            }
        }
        
        if ($inserted) {
            file_put_contents($f, $content);
            echo "Fixed $f\n";
        } else {
            echo "Could not fix $f automatically\n";
        }
    }
}
?>

