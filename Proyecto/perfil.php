<?php
include 'cabecera.php';
session_start();

generarCabecera();
if (!empty($_SESSION)){
    $dni =$_SESSION['dni'];
    mostrarDatos($dni);
}else{
    header("Location: inicio.php");
}

function mostrarDatos($dni){
    include 'db.php';
    $solicitanteDatos = obtenerDatosSolicitante($conexion, $dni);
    if ($solicitanteDatos) {
        mostrarDatosSolicitante($solicitanteDatos);
        $solicitudesDatos = obtenerDatosSolicitudes($conexion, $dni);
        if ($solicitudesDatos) {
            mostrarDatosSolicitudes($solicitudesDatos);
        } else {
            echo "No hay datos de incripcion del usuario.";
        }
    } else {
        echo '<main class="seccion_principal">';
        echo "No hay datos personales del usuario.<br>";
        echo "<a href='formulario_solicitante.php'>Realizar registro de datos</a>";
        echo '</main>';
    }
}

function obtenerDatosSolicitante($conexion, $dni) {
    $query = "SELECT * FROM solicitantes WHERE dni = :dni";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mostrarDatosSolicitante($datosSolicitante) {
    echo "<h2><center>Datos</center></h2>";
    foreach ($datosSolicitante as $datos) {
        echo "<div class='contenedor_perfil'>";
        echo "<h3>" . $datos['nombre'] . "</h3>";
        echo "<p><strong>DNI:</strong> " . $datos['dni'] . "</p>";
        echo "<p><strong>Apellidos:</strong> " . $datos['apellidos'] . "</p>";
        echo "<p><strong>Telefono:</strong> " . $datos['telefono'] . "</p>";
        echo "<p><strong>Correo:</strong> " . $datos['correo'] . "</p>";
        echo "<p><strong>Código de centro:</strong> " . $datos['codigocentro'] . "</p>";
        echo "<p><strong>Coordinador TC:</strong> " . $datos['coordinadortc'] . "</p>";
        echo "<p><strong>Grupo TC:</strong> " . $datos['grupotc'] . "</p>";
        echo "<p><strong>Nombre de grupo:</strong> " . $datos['nombregrupo'] . "</p>";
        echo "<p><strong>PBilin:</strong> " . $datos['pbilin'] . "</p>";
        echo "<p><strong>Cargo:</strong> " . $datos['cargo'] . "</p>";
        echo "<p><strong>Nombre de cargo:</strong> " . $datos['nombrecargo'] . "</p>";
        echo "<p><strong>Situación:</strong> " . $datos['situacion'] . "</p>";
        echo "<p><strong>Fecha de nacimiento:</strong> " . $datos['fechanac'] . "</p>";
        echo "<p><strong>Especialidad:</strong> " . $datos['especialidad'] . "</p>";
        echo "<p><strong>Puntos:</strong> " . $datos['puntos'] . "</p>";
        echo "</div>";
    }
}

function obtenerDatosSolicitudes($conexion, $dni) {
    $query = "SELECT * FROM solicitudes s INNER JOIN cursos c ON s.codigocurso = c.codigo WHERE s.dni = :dni";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mostrarDatosSolicitudes($datosSolicitudes) {
    echo "<h2><center>Solicitudes</center></h2>";
    foreach ($datosSolicitudes as $datos) {
        echo "<div class='contenedor_perfil'>";
        echo "<h3>Nombre del curso: " . $datos['nombre'] . "</h3>";
        echo "<p><strong>Plazo:</strong> " . ($datos['abierto'] == 1 ? "abierto" : "cerrado") . "</p>";
        echo "<p><strong>Fecha:</strong> " . $datos['fechasolicitud'] . "</p>";
        echo "<p><strong>Numero de plazas:</strong> " . $datos['numeroplazas'] . "</p>";
        echo "<p><strong>Estado:</strong> " . ($datos['admitido'] == 1 ? "admitido" : ($datos['abierto'] == 1 ? "en resolucion" : "no admitido")) . "</p>";
        echo "</div>";
    }
}

?>