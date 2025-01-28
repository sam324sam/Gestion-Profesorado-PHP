<?php
function generarMenu() {
    echo "<h2 style='text-align: center; color: #4CAF50;'>Panel de Administración</h2><div class='contenedor_admin' style='padding: 20px; background-color: #f9f9f9; border-radius: 10px;'>";
    echo '
    <form action="admin.php" method="GET" style="margin-bottom: 20px;">
        <h3 style="color: #333;">Activar/Desactivar cursos</h3>
        <input type="hidden" name="accion" value="mostrarActiDesac">
        <button type="submit" >Procesar</button>
    </form>

    <form action="admin.php" method="GET" style="margin-bottom: 20px;">
        <h3 style="color: #333;">Plazas para cursos cerrados</h3>
        <input type="hidden" name="accion" value="mostrarNumeroPlazas">
        <button type="submit" >Gestion de plazas</button>
    </form>
    
    <form action="admin.php" method="GET" style="margin-bottom: 20px;">
        <h3 style="color: #333;">Baremación automática</h3>
        <input type="hidden" name="accion" value="mostrarBaremacion">
        <button type="submit" >Baremación</button>
    </form>
    
    <form action="admin.php" method="GET" style="margin-bottom: 20px;">
        <h3 style="color: #333;">Listado de Admitidos</h3>
        <input type="hidden" name="accion" value="mostrarAdmitidos">
        <button type="submit">Ver Admitidos</button>
    </form>
    
    <form action="admin.php" method="GET" style="margin-bottom: 20px;">
        <h3 style="color: #333;">Incorporar Curso</h3>
        <input type="hidden" name="accion" value="mostrarIncorporar">
        <button type="submit" >Añadir Curso</button>
    </form>
    
    <form action="admin.php" method="GET" style="margin-bottom: 20px;">
        <h3 style="color: #333;">Eliminar Curso</h3>
        <input type="hidden" name="accion" value="mostrarEliminar">
        <button type="submit" >Eliminar Curso</button>
    </form>
    </div>';
}
