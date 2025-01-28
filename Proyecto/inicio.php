<?php
session_start();
include 'cabecera.php';

// Crear la cabecera
generarCabecera();

echo '<main class="seccion_principal">';

// Verificar si hay sesion para moidificar el menu
echo '<section class="menu">';
if (isset($_SESSION['usuario'])) {
    
    if ($_SESSION['admin'] == 1) {
        echo '
            <h2>Panel de Administración</h2>
            <ul>
                <li><a href="mostrar_cursos.php">Ver Cursos</a></li>
                <li><a href="admin.php">Administrar Panel</a></li>
            </ul>';
    } else {
        echo '
            <h2>Menú de Usuario</h2>
            <ul>
                <li><a href="perfil.php">Mi Perfil</a></li>
                <li><a href="mostrar_cursos.php">Ver Cursos</a></li>
            </ul>';
    }
} else {
    echo '
        <h2>Explorar</h2>
        <ul>
            <li><a href="mostrar_cursos.php">Ver Cursos</a></li>
        </ul>';
}
echo '</section>';
echo '</main>';