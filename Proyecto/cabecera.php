<?php
session_start();

// Función para generar la cabecera
function generarCabecera()
{
    echo '
        <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Aplicación de Inscripción</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
        <h1>Aplicación de Inscripción</h1>
        <nav>
            <ul>';
            echo '<li><a href="inicio.php">Inicio</a></li>';
            echo '<li><a href="mostrar_cursos.php">Mostrar cursos</a></li>';
    if (isset($_SESSION['usuario'])) {
        echo '<li>Usuario: ' . htmlspecialchars($_SESSION['usuario']) . '</li>
              <li><a class="cerrar" href="cerrar_secion.php">Cerrar sesión</a></li>';
              if ($_SESSION['admin'] == 1) {
                echo '<li><a href="admin.php">Admin</a></li>';
              }
    } else {
        echo '<li><a href="formulario_login.php">Iniciar sesión</a></li>';
    }
    
    echo '    </ul>
        </nav>
    </header>';
}

function cerrarHtml(){
    echo '</body>
    </html>';
}
?>
