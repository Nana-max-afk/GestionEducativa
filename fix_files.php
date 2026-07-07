<?php
$files = glob("*.php");
$exclude = ['conexion.php', 'header.php', 'footer.php', 'index.php'];

foreach ($files as $file) {
    if (in_array($file, $exclude)) continue;
    $content = file_get_contents($file);
    
    // Remove DOCTYPE, html tags, body tags
    $content = preg_replace('/\s*/i', '', $content);
    $content = preg_replace('/]*>\s*/i', '', $content);
    $content = preg_replace('/<\/html>\s*/i', '', $content);
    $content = preg_replace('/\s*/i', '', $content);
    
    // Remove meta, link, title tags inside head (or the whole head)
    // Be careful to keep <?php include("header.php"); ?>
<style> if they exist
    if (preg_match('/<head>(.*?)<\/head>/is', $content, $matches)) {
        $head_content = $matches[1];
        // extract styles
        preg_match_all('/<style>.*?<\/style>/is', $head_content, $style_matches);
        $styles = implode("\n", $style_matches[0]);
        
        $content = str_replace($matches[0], $styles, $content);
    }
    
    // Some files might have styles outside <head>, they will remain untouched.

    // Remove old footer inclusions and append it to the very end
    $content = preg_replace('/include\s*\(\s*[\'"]footer\.php[\'"]\s*\)\s*;/i', '', $content);
    
    // Make sure we don't have dangling ?><?php
    $content .= "\n<?php include(\"footer.php\"); ?>\n";

    // Write back
    file_put_contents($file, $content);
    echo "Fixed: $file\n";
}
echo "Done.";
?>

<?php include("footer.php"); ?>
