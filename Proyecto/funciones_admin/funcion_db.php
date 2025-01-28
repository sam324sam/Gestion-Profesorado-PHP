<?php
function actualizarEstado()
{
    include 'db.php';
    generarCabecera();
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['abierto'])) {
        $estados = $_POST['abierto']; // Cursos marcados como abiertos
        echo "<div class='resultado'>";
        echo "<h2>Resultado</h2>";
        try {
            // Desactivar todos los cursos primero
            $queryDesactivar = "UPDATE cursos SET abierto = 0 WHERE plazoinscripcion < CURDATE()";
            $stmtDesactivar = $conexion->prepare($queryDesactivar);
            $stmtDesactivar->execute();

            // Activar solo los cursos seleccionados
            $queryActualizar = "UPDATE cursos
                    SET abierto = 1
                    WHERE codigo = :codigo";
            $stmtActualizar = $conexion->prepare($queryActualizar);

            foreach ($estados as $codigo => $valor) {
                $stmtActualizar->bindParam(':codigo', $codigo);
                $stmtActualizar->execute();
            }

            echo "Estado actualizado correctamente. Cursos modificados";
        } catch (PDOException $e) {
            echo "Error al actualizar los estados: " . $e->getMessage();
        }
    } else {
        echo "No se recibieron cambios en el estado de los cursos";
    }
    echo "</div>";
    generarMenu();
}


function eliminarCurso()
{
    include 'db.php';
    generarCabecera();
    $cursos = array_keys($_POST['cursos']);
    echo "<div class='resultado'>";
    echo "<h2>Resultado</h2>";

    try {
        $queryEliminar = "DELETE FROM cursos WHERE codigo = :codigo";
        $stmtEliminar = $conexion->prepare($queryEliminar);

        foreach ($cursos as $codigo) {
            $stmtEliminar->bindParam(':codigo', $codigo);
            $stmtEliminar->execute();
            if ($stmtEliminar->rowCount() > 0) {
                echo "<p>Curso con código <strong>" . htmlspecialchars($codigo) . "</strong> eliminado correctamente.</p>";
            } else {
                echo "<p>No se pudo eliminar el curso con código <strong>" . htmlspecialchars($codigo) . "</strong>.</p>";
            }
        }
    } catch (PDOException $e) {
        echo "Error al eliminar el curso: " . $e->getMessage();
    }

    echo "</div>";
    generarMenu();
}

function validarCurso()
{
    // Inicializar variables
    $datos = $_POST;
    $errores = [];

    // Validación del campo Nombre
    if (empty($datos['nombre'])) {
        $errores['nombre'] = "El nombre es obligatorio.";
    }

    // Validación del campo Plazo de inscripción
    if (empty($datos['plazoinscripcion'])) {
        $errores['plazoinscripcion'] = "El plazo de inscripción es obligatorio.";
    } elseif (strtotime($datos['plazoinscripcion']) < strtotime(date('Y-m-d'))) {
        $errores['plazoinscripcion'] = "El plazo de inscripción no puede ser una fecha pasada.";
    }

    // Si hay errores, repintar el formulario
    if (!empty($errores)) {
        mostrarIncorporar($datos, $errores);
    } else {
        // Llamar a la función de inserción
        insertarCurso($datos);
    }
}


function insertarCurso($datos)
{
    include 'db.php';
    $query = "INSERT INTO cursos (nombre, codigo, plazoinscripcion) VALUES (:nombre, :codigo, :plazoinscripcion)";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':nombre', $datos['nombre']);
    $stmt->bindParam(':codigo', $datos['codigo']);
    $stmt->bindParam(':plazoinscripcion', $datos['plazoinscripcion']);
    generarCabecera();
    echo "<div class='resultado'>";
    echo "<h2>Resultado</h2>";

    try {
        if ($stmt->execute()) {
            echo "<p>Curso incorporado con éxito.</p>";
        } else {
            echo "<p>Error al incorporar el curso.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    generarMenu();
}

function validarNumeroPlazas()
{
    $numero_plazas = $_POST['numeroplazas'];
    $codigo_curso = $_POST['codigo'];
    $error = "";
    if ($numero_plazas <= 0) {
        $error = "El número de plazas debe ser mayor que 0.";
        mostrarNumeroPlazas($error);
    } else {
        cambiarNumeroPlazas($numero_plazas, $codigo_curso);
    }
}

function cambiarNumeroPlazas($numero_plazas, $codigo_curso)
{
    include "db.php";
    $query = "UPDATE cursos SET numeroplazas = :numeroplazas WHERE codigo = :codigo AND numeroplazas = 0";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':numeroplazas', $numero_plazas);
    $stmt->bindParam(':codigo', $codigo_curso);
    $stmt->execute();

    generarCabecera();
    echo "<div class='resultado'>";
    echo "<h2>Resultado</h2>";
    if ($stmt->rowCount() > 0) {
        echo "<p>Número de plazas actualizado correctamente para el curso con código <strong>" . htmlspecialchars($codigo_curso) . "</strong>.</p>";
    } else {
        echo "<p>No se pudo actualizar el número de plazas para el curso con código <strong>" . htmlspecialchars($codigo_curso) . "</strong>.</p>";
    }
    echo "</div>";
    generarMenu();

}

function realizarBaremacion()
{
    $codigos_cursos = $_POST['selccionado'];
    foreach ($codigos_cursos as $codigo) {
        realizarCalculoBarem($codigo);
    }
}

function realizarCalculoBarem($codigo_curso)
{
    include 'db.php';
    $query = "SELECT solicitantes.dni, solicitantes.nombre, solicitantes.apellidos, solicitantes.puntos, solicitudes.codigocurso
    FROM solicitantes
    INNER JOIN solicitudes ON solicitantes.dni = solicitudes.dni
    WHERE solicitudes.codigocurso = :codigocurso
    ORDER BY solicitantes.puntos DESC
    LIMIT :numeroPlazas";

    $numeroPlazas = verNumeroPlazas($codigo_curso);
    
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':codigocurso', $codigo_curso);
    $stmt->bindParam(':numeroPlazas', $numeroPlazas, PDO::PARAM_INT);
    $stmt->execute();
    
    generarCabecera();
    echo "<div class='resultado'>";
    echo "<h2>Resultado</h2>";
    echo "<h3>Admitidos para el curso con código <strong>" . htmlspecialchars($codigo_curso) . "</strong>:</h3>";
    if ($stmt->rowCount() > 0) {
        while ($solicitante = $stmt->fetch(PDO::FETCH_ASSOC)) {
            actualizarAdmitidos($codigo_curso, $solicitante['dni'], $solicitante['nombre'], $solicitante['apellidos']);
        }
    } else {
        echo "<p>No hay solicitantes para el curso con código <strong>" . htmlspecialchars($codigo_curso) . "</strong>.</p>";
    }
    echo "</div>";
    generarMenu();
}


function verNumeroPlazas($codigo_curso){
    include 'db.php';
    $query = "SELECT numeroplazas FROM cursos WHERE codigo = :codigo_curso";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':codigo_curso', $codigo_curso);
    $stmt->execute();
    $numero_plazas = $stmt->fetch(PDO::FETCH_ASSOC);
    return $numero_plazas['numeroplazas']; // Asegúrate de devolver el valor escalar
}


function actualizarAdmitidos($codigo_curso, $dni, $nombre, $apellidos){
    include 'db.php';
    $query = "UPDATE solicitudes SET admitido = 1 WHERE codigocurso = :codigo_curso AND dni = :dni";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':codigo_curso', $codigo_curso);
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "<p>Solicitante con nombre y apellidos <strong>" . htmlspecialchars($nombre) ." ".$apellidos. "</strong> admitido correctamente para el curso con código <strong>" . htmlspecialchars($codigo_curso) . "</strong>.</p>";
    }else{
        echo "<p>No se pudo admitir al solicitante con nombre y apellidos <strong>" . htmlspecialchars($nombre) ." ".$apellidos. "</strong> para el curso con código <strong>" . htmlspecialchars($codigo_curso) . "</strong>.</p>";
        echo "<p class='error'>Por favor, revisa que el solicitante no haya sido admitido previamente.</p>";
    }
}