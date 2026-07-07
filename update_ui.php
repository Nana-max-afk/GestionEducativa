<?php
$files = [
    'cursos.php', 'docentes.php', 'empresa.php', 'estudiantes.php', 
    'gastos.php', 'ingresos.php', 'sedes.php', 'usuarios.php', 'all_data.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Wrap <table> with <?php include("header.php"); ?>
<div class="table-container"> if not already wrapped
        if (strpos($content, '<div class="table-container">') === false && strpos($content, '<table') !== false) {
            $content = preg_replace('/(<table>|<table [^>]*>)/i', "<div class=\"table-container\">\n        $1", $content);
            $content = str_replace('</table>', "</table>\n    </div>", $content);
        }
        
        // Update Modal Open
        if (strpos($content, "setTimeout(() => modal.classList.add('show'), 10);") === false) {
            $content = str_replace("modal.style.display = 'block';", "modal.style.display = 'block';\n            setTimeout(() => modal.classList.add('show'), 10);", $content);
        }
        
        // Update Modal Close
        if (strpos($content, "modal.classList.remove('show');") === false) {
            $content = str_replace("function closeModal() { modal.style.display = 'none'; }", "function closeModal() { \n            modal.classList.remove('show');\n            setTimeout(() => modal.style.display = 'none', 300);\n        }", $content);
        }
        
        file_put_contents($file, $content);
        echo "Updated: $file\n";
    }
}
echo "Done.\n";
?>
