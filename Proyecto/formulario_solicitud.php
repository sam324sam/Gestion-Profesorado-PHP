<?php
session_start();
include 'cabecera.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

generarCabecera();

// Verifico si el usuario existe en la base de solicitantes y si no lo reenvio al formulario de solicitantes
if (!empty($_SESSION)) {
    // Si el usuario es esta registrado en solicitantes
    if (empty($_SESSION['dni'])) {
        header("Location: formulario_solicitante.php");
    } else {
        preguntarSolicitud();
    }
} else {
    header("Location: formulario_login.php");
}
function preguntarSolicitud()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dni = $_SESSION['dni'];
        $fechaSolicitud = date('Y-m-d');
        $codigoCurso = trim(htmlspecialchars($_POST["codigocurso"] ?? ""));
        echo "<div class='resultado'> <h2>Resultado</h2>";
        if (solicitudRepetida($codigoCurso, $dni)) {
            echo "Formulario enviado correctamente con los tus datos";
            echo insertSolicitud($fechaSolicitud, $dni, $codigoCurso);
            exit;
        }
        echo "</div>";
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $codigoCurso = trim(htmlspecialchars($_GET["codigocurso"] ?? ""));
        $nombreCurso = trim(htmlspecialchars($_GET["nombrecurso"] ?? ""));
        echo "
        <form action=\"formulario_solicitud.php\" method=\"POST\">
            <h3>¿Seguro que quieres mandar una solicitud para este el curso " . $nombreCurso . " ?</h3>
            <input type=\"hidden\" id=\"curso\" name=\"codigocurso\" value=\"" . $codigoCurso . "\">
            <br><br>

            <button type=\"submit\">Enviar solicitud</button>
        </form>
    ";
    }
}

function solicitudRepetida($codigoCurso, $dni)
{
    include 'db.php';
    try {
        $sql = "SELECT * FROM solicitudes WHERE codigocurso = :codigocurso AND dni = :dni";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':codigocurso', $codigoCurso);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();
        if ($stmt->rowCount() <= 0) {
            return true;
        } else {
            echo "Solo se permite una solicitud por dni";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error al validar el solicitud repetida: " . $e->getMessage();
        return false;
    }
}


function insertSolicitud($fechaSolicitud, $dni, $codigoCurso)
{
    include 'db.php';
    try {
        // Preparar la consulta SQL para insertar los datos
        $sql = "INSERT INTO solicitudes (dni, codigocurso, fechasolicitud)
                VALUES (:dni, :codigocurso, :fecha)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':codigocurso', $codigoCurso);
        $stmt->bindParam(':fecha', $fechaSolicitud);
        $stmt->execute();

        return ". Solicitud registrada exitosamente.";
    } catch (PDOException $e) {
        return ". Error al registrar la solicitud: " . $e->getMessage();
    }
}
?>