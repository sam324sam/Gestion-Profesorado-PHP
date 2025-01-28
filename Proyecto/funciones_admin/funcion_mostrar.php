<?php
function mostrarActiDesac()
{
    include 'db.php';
    $query = "SELECT * FROM cursos";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    generarCabecera();
    echo "<div class='contenedor_curso'>";
    if ($stmt->rowCount() > 0) {
        echo "<form action='admin.php' method='POST'>";
        echo "<h2>Activar/Desactivar Cursos</h2>";
        echo "<h4>Por favor, recuerde que solo puede cerrar un curso si la fecha de inscripción ya ha finalizado.</h4>";
        echo "<input type='hidden' name='accion' value='actualizarEstado'>";
        while ($curso = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='curso'>";
            echo "<h3>" . htmlspecialchars($curso['nombre']) . "</h3>";
            echo "<label>Codigo: " . htmlspecialchars($curso['codigo']) . "</label>";
            echo "<label>Fecha de plazo de inscripción: " . htmlspecialchars($curso['plazoinscripcion']) . "</label>";
            echo "<label for='abierto" . $curso['codigo'] . "'>Estado:</label>";
            echo "<span>" . ($curso['abierto'] == 1 ? "Abierto" : "Cerrado") . "</span>";
            echo "<input type='checkbox' id='abierto" . $curso['codigo'] . "' name='abierto[" . $curso['codigo'] . "]' value='1' " . ($curso['abierto'] == 1 ? "checked" : "") . ">";
            echo "<input type='hidden' name='cursos[" . $curso['codigo'] . "][nombre]' value='" . htmlspecialchars($curso['nombre']) . "'>";
            echo "</div>";
        }
        echo "<input type='submit' value='Actualizar datos'>";
        echo "</form>";
    } else {
        echo "No hay cursos en la base de datos.";
    }
    echo "</div>";
}

function mostrarAdmitidos()
{
    include 'db.php';
    generarCabecera();
    echo "<center><h2>Listado de Admitidos por Curso</h2></center>";
    echo "<div class='contenedor_admitidos'>";
    

    $query = "
        SELECT c.nombre AS curso_nombre, so.nombre AS solicitante_nombre, so.apellidos AS solicitante_apellidos
        FROM solicitudes s
        INNER JOIN solicitantes so ON s.dni = so.dni
        INNER JOIN cursos c ON s.codigocurso = c.codigo
        WHERE s.admitido = 1
        GROUP BY c.nombre, so.dni
        ORDER BY c.nombre, so.apellidos
    ";

    $stmt = $conexion->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $curso_actual = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($curso_actual != $row['curso_nombre']) {
                if ($curso_actual != '') {
                    echo "</ul></div>";
                }
                $curso_actual = $row['curso_nombre'];
                echo "<div class='celda_admitidos'>";
                echo "<h3>Curso: " . htmlspecialchars($curso_actual) . "</h3>";
                echo "<ul class='lista_admitidos'>";
            }

            echo "<li class='admitido'>" . htmlspecialchars($row['solicitante_nombre']) . " " . htmlspecialchars($row['solicitante_apellidos']) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<p>No hay admitidos en la base de datos.</p>";
    }
    echo "</div>";
    generarMenu();
}

function mostrarEliminar()
{
    include 'db.php';
    $query = "SELECT * FROM cursos";
    $stmt = $conexion->prepare($query);
    $stmt->execute();

    generarCabecera();
    echo "<div class='contenedor_curso'>";
    echo "<form action='admin.php' method='POST'>";
    echo "<input type='hidden' name='accion' value='eliminarCurso'>";

    if ($stmt->rowCount() > 0) {
        while ($curso = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='curso'>";
            echo "<h3>" . htmlspecialchars($curso['nombre']) . "</h3>";
            echo "<label>Codigo: " . htmlspecialchars($curso['codigo']) . "</label>";
            echo "<label>Fecha de plazo de inscripción: " . htmlspecialchars($curso['plazoinscripcion']) . "</label>";
            echo "<label>Número de plazas: " . htmlspecialchars($curso['numeroplazas']) . "</label>";
            echo "<center><label for='eliminar" . $curso['codigo'] . "'>Eliminar</label></center>";
            echo "<center><input type='checkbox' id='eliminar" . $curso['codigo'] . "' name='cursos[" . $curso['codigo'] . "]' value='1'></center>";
            echo "</div>";
        }
        echo "<br><input type='submit' value='Eliminar cursos seleccionados'>";
    } else {
        echo "No hay cursos en la base de datos.";
    }

    echo "</form>";
    echo "</div>";
}

function mostrarIncorporar($datos = [], $errores = [])
{
    generarCabecera();
    echo "<div class='contenedor_curso'>";
    echo "<form action='admin.php' method='POST'>";
    echo "<h2>Incorporar Curso</h2>";
    echo "<input type='hidden' name='accion' value='validarIncorporar'>";

    //Nombre
    echo "<label for='nombre'>Nombre:</label>";
    $nombre = isset($datos['nombre']) ? htmlspecialchars($datos['nombre']) : '';
    echo "<input type='text' id='nombre' name='nombre' value='$nombre'>";
    if (isset($errores['nombre'])) {
        echo "<p class='error'>" . htmlspecialchars($errores['nombre']) . "</p>";
    }

    //Plazo de Inscripción
    echo "<label for='plazoinscripcion'>Plazo de inscripción:</label>";
    $plazoinscripcion = isset($datos['plazoinscripcion']) ? htmlspecialchars($datos['plazoinscripcion']) : '';
    echo "<input type='date' id='plazoinscripcion' name='plazoinscripcion' value='$plazoinscripcion'>";
    if (isset($errores['plazoinscripcion'])) {
        echo "<p class='error'>" . htmlspecialchars($errores['plazoinscripcion']) . "</p>";
    }

    echo "<br>";
    echo "<input type='submit' value='Incorporar curso'>";
    echo "</form>";
    echo "</div>";
}

function mostrarBaremacion()
{
    include 'db.php';
    $query = "SELECT * FROM cursos WHERE abierto = 0 AND numeroplazas != 0";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    generarCabecera();
    echo "<div class='contenedor_curso'>";
    if ($stmt->rowCount() > 0) {
        echo "<form action='admin.php' method='POST'>";
        echo "<input type='hidden' name='accion' value='realizarBaremacion'>";
        while ($curso = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='curso'>";
            echo "<h3>" . htmlspecialchars($curso['nombre']) . "</h3>";
            echo "<label>Codigo: " . htmlspecialchars($curso['codigo']) . "</label>";
            echo "<label>Fecha de plazo de inscripción: " . htmlspecialchars($curso['plazoinscripcion']) . "</label>";
            echo "<label>Número de plazas: " . htmlspecialchars($curso['numeroplazas']) . "</label>";
            echo "<center><label for='abierto" . $curso['codigo'] . "'>Selecionar</label></center>";
            echo "<center><input type='checkbox' id='selccionado' name='selccionado[" . $curso['codigo'] . "]' value='" . $curso['codigo'] . "'></center>";
            echo "</div>";
        }
        echo "<input type='submit' value='Realizar baremacion de los cursos seleccionados'>";
        echo "</form>";
    } else {
        echo "No hay cursos en la base de datos.";
    }
    echo "</div>";
}

function mostrarNumeroPlazas($error)
{
    include 'db.php';
    $query = "SELECT * FROM cursos WHERE abierto = 0 AND numeroplazas = 0";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    generarCabecera();
    if ($error != "") {
        echo "<div class='resultado'>";
        echo "<p>Resultado: " . $error . "</p>";
        echo "</div>";
    }
    echo "<center><h2>Cambiar número de plazas</h2></center>";
    echo "<div class='contenedor_curso'>";
    if ($stmt->rowCount() > 0) {
        while ($curso = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<form action='admin.php' method='POST'>";
            echo "<h2>Curso: " . $curso['nombre'] . "</h2>";
            echo "<input type='hidden' name='accion' value='validarNumeroPlazas'>";
            echo "<input type='hidden' name='codigo' value='" . $curso['codigo'] . "'>";
            echo "<input type='number' name='numeroplazas' value='" . $curso['numeroplazas'] . "'><br>";
            echo "<br><input type='submit' value='Cambiar número de plazas'>";
            echo "</form>";
        }
    } else {
        echo "No hay cursos cerrados en la base de datos. O sin asignar número de plazas.";
    }
    echo "</div>";
}