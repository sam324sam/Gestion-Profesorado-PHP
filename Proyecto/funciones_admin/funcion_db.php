<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    $query = "SELECT solicitantes.dni, solicitantes.nombre, solicitantes.apellidos, solicitantes.puntos, solicitudes.codigocurso, cursos.nombre AS nombre_curso, solicitantes.correo
    FROM solicitantes
    INNER JOIN solicitudes ON solicitantes.dni = solicitudes.dni
    INNER JOIN cursos ON solicitudes.codigocurso = cursos.codigo
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
            actualizarAdmitidos($codigo_curso, $solicitante['dni'], $solicitante['nombre'], $solicitante['apellidos'], $solicitante['nombre_curso'], $solicitante['correo']);
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


function actualizarAdmitidos($codigo_curso, $dni, $nombre, $apellidos, $nombre_curso, $email){
    include 'db.php';
    $query = "UPDATE solicitudes SET admitido = 1 WHERE codigocurso = :codigo_curso AND dni = :dni";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':codigo_curso', $codigo_curso);
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "<p>Solicitante con nombre y apellidos <strong>" . htmlspecialchars($nombre) ." ".$apellidos. "</strong> admitido correctamente para el curso con código <strong>" . htmlspecialchars($codigo_curso) . "</strong>.</p>";
        crearPDF($codigo_curso, $dni, $nombre, $apellidos, $nombre_curso, $email);
    }else{
        echo "<p>No se pudo admitir al solicitante con nombre y apellidos <strong>" . htmlspecialchars($nombre) ." ".$apellidos. "</strong> para el curso con código <strong>" . htmlspecialchars($codigo_curso) . "</strong>.</p>";
        echo "<p class='error'>Por favor, revisa que el solicitante no haya sido admitido previamente.</p>";
    }
}

function crearPDF($codigo_curso, $dni, $nombre, $apellidos, $nombre_curso, $email){
    // Incluir la librería FPDF
    require_once('librerias/fpdf186/fpdf.php');

    // Crear un objeto FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    //Agregar la imagen
    $pdf->Image('img/logoIES.png', 10, 10, 50); //Para los tamaños de las mismas

    // Establecer la fuente
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Certificado de Admision', 0, 1, 'C');

    // Información del admitido
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(10);
    $pdf->MultiCell(0, 10, "Estimado/a $nombre $apellidos,");
    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, "Nos complace informarle que ha sido admitido/a en el curso '$nombre_curso'.");
    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, "DNI: $dni");
    $pdf->MultiCell(0, 10, "Codigo del curso: $codigo_curso");

    // Mensaje de finalización
    $pdf->Ln(10);
    $pdf->MultiCell(0, 10, "Le deseamos mucho exito en su formación. Para mas informacion, contacte con la administración.");

    // Creo la fecha para incorporarla en el pdf y darle nombre unico al pdf
    $nuevaFecha = new DateTime(); 
    echo $nuevaFecha->format("Y-m-d H:i:s") . "<br>";
    $pdf->MultiCell(0, 10, "Fecha de admicion: " . $nuevaFecha->format("Y-m-d"));
    
    // Salvar o mostrar el PDF
    $ruta_PDF = 'PDF/admitido_' . $dni . $codigo_curso . $nuevaFecha->format("Y-m-d H:i:s") . '.pdf';
    $pdf->Output('F', $ruta_PDF);
    $asunto = 'Admision en el curso '.$nombre_curso;
    $mensaje = "Estimado/a $nombre $apellidos,\n\nNos complace informarle que ha sido admitido/a en el curso '$nombre_curso'.\n\nDNI: $dni\nCodigo del curso: $codigo_curso\n\nLe deseamos mucho exito en su formacion. Para mas informacion, contacte con la administracion.";
    enviarCorreo($email, $asunto, $mensaje, $ruta_PDF);
}

function enviarCorreo($email, $asunto, $mensaje, $ruta){
    spl_autoload_register(function ($clase) {
        $fullpath = "librerias/PHPMailer-master/src/" . $clase . ".php";
        if (file_exists($fullpath)) {
            require_once $fullpath;
        } else {
            echo "<p>La clase $fullpath no se encuentra</p>";
            exit();
        }
    
    });
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0; // Habilita la salida de depuración detallada
    $mail->isSMTP();                       // Establece el tipo de correo electrónico para usar SMTP
    $mail->Host     = 'localhost';         // Especifica los servidores SMTP principales y de respaldo
    $mail->SMTPAuth = false;
    $mail->Username = 'admin';
    $mail->Password = 'qm#.FMb*7uy';
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Habilita el cifrado TLS; `ssl` también aceptado
    $mail->Port = 587;
    try {
        // Configura y envía el mensaje
        $mail->setFrom('admin@cursos.com', 'admin');
        $mail->addAddress($email);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;

        // Adjuntar pdf si la ruta es valida
        if (!empty($ruta) && file_exists($ruta)) {
            $mail->addAttachment($ruta); // Adjunta la imagen
        } else {
            $mail->Body .= "\n\nNo se encontra el archivo adjunto en la ruta especificada.";
        }
        $mail->send();
    } catch (Exception $e) {
        echo 'El mensaje no pudo ser enviado.';
        echo 'Error de correo: ' . $mail->ErrorInfo;
    }
}
