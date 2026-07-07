<?php

$css_to_add = <<<CSS

/* --- ESTILOS ESTANDAR DE FORMULARIOS Y MODALES --- */
.header-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.add-button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

.add-button:hover { background-color: #45a049; }

/* MODAL Y SCROLL */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0; top: 0; width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.6);
    overflow-y: auto; /* Scroll para la pantalla completa */
}

.modal-content {
    background-color: #fff;
    margin: 30px auto;
    padding: 25px;
    border-radius: 12px;
    width: 90%;
    max-width: 450px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    position: relative;
    /* Scroll interno si el contenido supera el alto de la pantalla */
    max-height: 85vh; 
    overflow-y: auto;
}

.close { float: right; font-size: 28px; font-weight: bold; cursor: pointer; color: #777; }
.close:hover { color: #000; }

form label { display: block; margin-top: 10px; font-weight: bold; color: #333; }
form input, form select { 
    width: 100%; 
    padding: 10px; 
    margin-top: 5px; 
    border: 1px solid #ddd; 
    border-radius: 6px; 
    box-sizing: border-box; 
}

input[type="submit"] {
    background-color: #2196F3;
    color: white;
    border: none;
    margin-top: 20px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    padding: 12px;
    border-radius: 6px;
}

input[type="submit"]:hover { background-color: #1976D2; }

CSS;

$global_css_file = "estilos_globales.css";
$css_content = file_get_contents($global_css_file);

// Remove old floating add-button and modal definitions
$css_content = preg_replace('/\/\* --- Botón Flotante "Nuevo" --- \*\/(.*?)\/\* --- Diseño de la Tabla --- \*\//is', '/* --- Diseño de la Tabla --- */', $css_content);
$css_content = preg_replace('/\/\* --- El Modal \(Formulario\) --- \*\/(.*)/is', '', $css_content);

// append new modal styles
file_put_contents($global_css_file, $css_content . $css_to_add);
echo "estilos_globales.css actualizado.\n";

// Remove local <?php include("header.php"); ?>
<style> blocks from existing modal files
$files_to_clean = ['estudiantes.php', 'cursos.php', 'docentes.php', 'sedes.php', 'ingresos.php', 'gastos.php'];
foreach ($files_to_clean as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = preg_replace('/<style>.*?<\/style>\s*/is', '', $content);
        file_put_contents($file, $content);
        echo "Limpiado: $file\n";
    }
}
?>
