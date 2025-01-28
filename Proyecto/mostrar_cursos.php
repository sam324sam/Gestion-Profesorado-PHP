<?php
include('db.php');
include('cabecera.php');

$query = "SELECT * FROM cursos WHERE abierto = 1";
$stmt = $conexion->prepare($query);
$stmt->execute();
generarCabecera();
echo "<div class='contenedor_curso'>";
if ($stmt->rowCount() > 0) {
    while ($curso = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<form action='formulario_solicitud.php' method='GET'>";
        echo "<h3>" . $curso['nombre'] . "</h3>";
        echo "<label>Fecha de plazo de inscripcion: ". $curso['plazoinscripcion']."</label>";
        echo "<input type='hidden' name='codigocurso' value='".$curso['codigo']."'>";
        echo "<input type='hidden' name='nombrecurso' value='".$curso['nombre']."'>";
        echo "<input type='submit' value='Inscribirse'>";
        echo "</form>";
    }
} else {
    echo "No hay cursos activos en este momento.";
}
echo "</div>'";
?>