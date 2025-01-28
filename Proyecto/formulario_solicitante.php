<?php
include('cabecera.php');
session_start();

$errores = [];
$datos = [];

if (empty($_SESSION)) {
    header("Location: formulario_login.php");

}else{
    if ($_SESSION['admin'] == 1 || !empty($_SESSION["dni"])) {
        header("Location: inicio.php");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = crearDatos($_POST); // crear datos
    $errores = validarFormulario($datos); // Validar los datos

    // Si no hay errores, mostrar un mensaje de exito
    if (empty($errores)) {
        generarCabecera();
        echo "<div class='resultado'>";
        echo insertarDatos($datos);
        echo "</div>";
        exit;
    } else {
        generarFormulario($datos, $errores);
    }
} else {
    $errores = [];
    $datos = [];
    generarFormulario($datos, $errores);
}

// Funcion para crear los datos de entrada
function crearDatos($entrada)
{
    $datos = [];
    foreach ($entrada as $clave => $valor) {
        $datos[$clave] = htmlspecialchars(trim($valor));
    }
    return $datos;
}

// Funcion para validar el formulario
function validarFormulario($datos)
{
    $errores = [];

    // Validar campos obligatorios dni
    if (empty($datos['dni']) || !preg_match('/^\d{8}[A-Za-z]$/', $datos['dni'])) {
        $errores['dni'] = "El DNI es obligatorio y debe tener 8 números y una letra.";
    } elseif (dniNoRep($datos['dni'])) {
        $errores['dni'] = "El DNI ya está registrado en la base de datos.";
    }

    // datos de nombre etc
    if (empty($datos['apellidos'])) {
        $errores['apellidos'] = "Los apellidos son obligatorios.";
    }
    if (empty($datos['nombre'])) {
        $errores['nombre'] = "El nombre es obligatorio.";
    }

    // Validar teléfono
    if (empty($datos['telefono']) && !preg_match('/^[0-9]{9,15}$/', $datos['telefono'])) {
        $errores['telefono'] = "El teléfono debe tener entre 9 y 15 números.";
    }

    // Validar correo
    if (empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = "El correo electrónico no es válido.";
    }

    // Validar fecha de nacimiento
    if (empty($datos['fechanac'])) {
        $errores['fechanac'] = "La fecha de nacimiento es obligatoria.";
    } elseif (strtotime($datos['fechanac']) > strtotime(date('Y-m-d'))) {
        $errores['fechanac'] = "La fecha de nacimiento no puede ser una fecha futura.";
    }

    return $errores;
}

function dniNoRep($dni)
{
    include('db.php');
    $query = $conexion->prepare("SELECT COUNT(*) FROM solicitantes WHERE dni = :dni");
    $query->execute(['dni' => $dni]);
    return $query->fetchColumn() > 0; // Retorna true si el DNI ya existe
}

function generarFormulario($datos, $errores)
{
    generarCabecera();
    echo "
    <form action=\"\" method=\"POST\">
    <h1>Para realizar cualquier inscripcion se debe obtener los siguientes datos</h1>
    <h2>Inscripción de Solicitantes</h2>
    <h3>Los campos marcados con * son obligatorios</h3>
        <label for=\"dni\">DNI *</label>
        <input type=\"text\" id=\"dni\" name=\"dni\" maxlength=\"9\" value=\"" . ($datos['dni'] ?? '') . "\">
        <span class=\"error\">" . ($errores['dni'] ?? '') . "</span>
        <br><br>

        <label for=\"apellidos\">Apellidos *</label>
        <input type=\"text\" id=\"apellidos\" name=\"apellidos\" maxlength=\"100\" value=\"" . ($datos['apellidos'] ?? '') . "\">
        <span class=\"error\">" . ($errores['apellidos'] ?? '') . "</span>
        <br><br>

        <label for=\"nombre\">Nombre *</label>
        <input type=\"text\" id=\"nombre\" name=\"nombre\" maxlength=\"100\" value=\"" . ($datos['nombre'] ?? '') . "\">
        <span class=\"error\">" . ($errores['nombre'] ?? '') . "</span>
        <br><br>

        <label for=\"telefono\">Teléfono *</label>
        <input type=\"text\" id=\"telefono\" name=\"telefono\" maxlength=\"15\" value=\"" . ($datos['telefono'] ?? '') . "\">
        <span class=\"error\">" . ($errores['telefono'] ?? '') . "</span>
        <br><br>

        <label for=\"correo\">Correo *</label>
        <input type=\"email\" id=\"correo\" name=\"correo\" maxlength=\"50\" value=\"" . ($datos['correo'] ?? '') . "\">
        <span class=\"error\">" . ($errores['correo'] ?? '') . "</span>
        <br><br>

        <label for=\"situacion\">Situación *</label>
        <select id=\"situacion\" name=\"situacion\">
            <option value=\"activo\"" . (isset($datos['situacion']) && $datos['situacion'] === 'activo' ? ' selected' : '') . ">Activo</option>
            <option value=\"inactivo\"" . (isset($datos['situacion']) && $datos['situacion'] === 'inactivo' ? ' selected' : '') . ">Inactivo</option>
        </select>
        <br><br>

        <label for=\"fechanac\">Fecha de Nacimiento *</label>
        <input type=\"date\" id=\"fechanac\" name=\"fechanac\" value=\"" . ($datos['fechanac'] ?? '') . "\">
        <span class=\"error\">" . ($errores['fechanac'] ?? '') . "</span>
        <br><br>

        <label for=\"codigocentro\">Código del Centro</label>
        <input type=\"text\" id=\"codigocentro\" name=\"codigocentro\" maxlength=\"8\" value=\"" . ($datos['codigocentro'] ?? '') . "\">
        <br><br>

        <label for=\"coordinadortc\">Coordinador TIC</label>
        <input type=\"checkbox\" id=\"coordinadortc\" name=\"coordinadortc\" value=\"1\"" . (isset($datos['coordinadortc']) ? ' checked' : '') . ">
        <br><br>

        <label for=\"grupotc\">Grupo TIC</label>
        <input type=\"checkbox\" id=\"grupotc\" name=\"grupotc\" value=\"1\"" . (isset($datos['grupotc']) ? ' checked' : '') . ">
        <br><br>

        <label for=\"nombregrupo\">Nombre del Grupo</label>
        <input type=\"text\" id=\"nombregrupo\" name=\"nombregrupo\" maxlength=\"50\" value=\"" . ($datos['nombregrupo'] ?? '') . "\">
        <br><br>

        <label for=\"pbilin\">Programa Bilingüe</label>
        <input type=\"checkbox\" id=\"pbilin\" name=\"pbilin\" value=\"1\"" . (isset($datos['pbilin']) ? ' checked' : '') . ">
        <br><br>

        <label for=\"cargo\">Cargo</label>
        <input type=\"checkbox\" id=\"cargo\" name=\"cargo\" value=\"1\"" . (isset($datos['cargo']) ? ' checked' : '') . ">
        <br><br>

        <label for=\"nombrecargo\">Nombre del Cargo</label>
        <input type=\"text\" id=\"nombrecargo\" name=\"nombrecargo\" maxlength=\"15\" value=\"" . ($datos['nombrecargo'] ?? '') . "\">
        <br><br>

        <label for=\"especialidad\">Especialidad</label>
        <input type=\"text\" id=\"especialidad\" name=\"especialidad\" maxlength=\"50\" value=\"" . ($datos['especialidad'] ?? '') . "\">
        <br><br>

        <button type=\"submit\">Inscribirse</button>
    </form>
    ";
}

function insertarDatos($datos)
{
    include('db.php');

    try {
        $sql = "INSERT INTO solicitantes (
                    dni, apellidos, nombre, telefono, correo, codigocentro,
                    coordinadortc, grupotc, nombregrupo, pbilin, cargo, nombrecargo,
                    situacion, fechanac, especialidad, puntos
                ) VALUES (
                    :dni, :apellidos, :nombre, :telefono, :correo, :codigocentro,
                    :coordinadortc, :grupotc, :nombregrupo, :pbilin, :cargo, :nombrecargo,
                    :situacion, :fechanac, :especialidad, :puntos
                )";
        $stmt = $conexion->prepare($sql);
        // Asignar valores a las variables antes de pasarlas a bindParam o si no me da error al momento de leer directo del array
        $dni = $datos['dni'];
        $apellidos = $datos['apellidos'];
        $nombre = $datos['nombre'];
        $telefono = $datos['telefono'];
        $correo = $datos['correo'];
        $codigocentro = $datos['codigocentro'];
        $coordinadortc = $datos['coordinadortc'] ?? 0;
        $grupotc = $datos['grupotc'] ?? 0;
        $nombregrupo = $datos['nombregrupo'] ?? '';
        $pbilin = $datos['pbilin'] ?? 0;
        $cargo = $datos['cargo'] ?? 0;
        $nombrecargo = $datos['nombrecargo'] ?? '';
        $situacion = $datos['situacion'] ?? '';
        $fechanac = $datos['fechanac'] ?? '';
        $especialidad = $datos['especialidad'] ?? '';
        $puntos = calcularPuntos($datos);

        // Asignar valores
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':codigocentro', $codigocentro);
        $stmt->bindParam(':coordinadortc', $coordinadortc);
        $stmt->bindParam(':grupotc', $grupotc);
        $stmt->bindParam(':nombregrupo', $nombregrupo);
        $stmt->bindParam(':pbilin', $pbilin);
        $stmt->bindParam(':cargo', $cargo);
        $stmt->bindParam(':nombrecargo', $nombrecargo);
        $stmt->bindParam(':situacion', $situacion);
        $stmt->bindParam(':fechanac', $fechanac);
        $stmt->bindParam(':especialidad', $especialidad);
        $stmt->bindParam(':puntos', $puntos);

        $stmt->execute();
        // actualizo la sesion y la tabla usuario
        echo updateDni($dni, $_SESSION['id']);
        $_SESSION['dni'] = $dni;
        return "Datos insertados correctamente.";
    } catch (PDOException $e) {
        return "Error al insertar los datos: " . $e->getMessage();
    }
}

function calcularPuntos($solicitante)
{
    $puntos = 0;
    $puntos += $solicitante['coordinadortc'] ? 4 : 0;
    $puntos += $solicitante['grupotc'] ? 3 : 0;
    $puntos += $solicitante['pbilin'] ? 3 : 0;

    $puntos += (strtolower(trim($solicitante['nombrecargo'])) == 'director') ? 2 : 0; // Director
    $puntos += (strtolower(trim($solicitante['nombrecargo'])) == 'jefe de estudios') ? 2 : 0; // Jefe de estudios
    $puntos += (strtolower(trim($solicitante['nombrecargo'])) == 'secretario') ? 2 : 0; // Secretario
    $puntos += (strtolower(trim($solicitante['nombrecargo'])) == 'jefe de departamento') ? 1 : 0; // Jefe de departamento

    // Calcular antiguedad
    $fechaActual = new DateTime();
    if (!empty($solicitante['fechanac'])) {
        $fechaIngreso = new DateTime($solicitante['fechanac']);
        $antiguedad = $fechaActual->diff($fechaIngreso)->y;
        $puntos += ($antiguedad >= 15) ? 1 : 0;
    }
    return $puntos;
}

function updateDni($dni, $id)
{
    include('db.php');

    try {
        $sql = "UPDATE usuarios SET dni = :dni WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $idUsuario = $id ?? null;
        if (!$idUsuario) {
            throw new Exception("ID de usuario no encontrado en la sesión.");
        }
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);

        $stmt->execute();

        return "DNI actualizado correctamente.";
    } catch (PDOException $e) {
        return "Error al actualizar el DNI: " . $e->getMessage();
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

?>