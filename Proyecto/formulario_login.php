
<?php

session_start();

if (!empty($_SESSION)){
    header("Location: inicio.php");
}

$errores = array("usuario" => "", "clave" => "", "login" => "");
$datos = array("usuario" => "", "clave" => "");
// Procesamos el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = creaDatos();
    $errores = validar($datos);
    
    if (empty($errores)) {
        header("Location: inicio.php");
    }else{
        generarFormulario($errores, $datos);
    }

} else {
    generarFormulario($errores, $datos);
}

function creaDatos()
{
    $datos["usuario"] = trim(htmlspecialchars($_POST["usuario"] ?? ""));
    $datos["clave"] = trim(htmlspecialchars($_POST["clave"] ?? ""));
    return $datos;
}

function generarFormulario($errores, $datos)
{
    include('cabecera.php');
    generarCabecera();
    echo '
        <form action="" method="POST">
        <h2>Login</h2>
            <span class="error">' . $errores['login'] . '</span>
            <br><br>
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" maxlength="50" required value="' . $datos['usuario'] . '">
            <span class="error">' . $errores['usuario'] . '</span>
            <br><br>
            
            <label for="clave">Contraseña</label>
            <input type="password" id="clave" name="clave" maxlength="50" required>
            <span class="error">' . $errores['clave'] . '</span>
            <br><br>
            
            <button type="submit">Iniciar Sesión</button>
            <br><br>
            <a href="formulario_registro.php">Registrarse si no tienes cuenta</a>
        </form>'
;
    cerrarHtml();
}

function validar($datos)
{
    $errores = array();
    if ($datos["usuario"] === "") {
        $errores["usuario"] = "El campo de usuario no puede estar vacío.";
    }
    if ($datos["clave"] === "") {
        $errores["clave"] = "El campo de contraseña no puede estar vacío.";
    }
    $logeado = validado($datos);
    if ($logeado != "") {
        $errores["login"] = $logeado;
    }
    return $errores;
}

function validado($datos)
{
    include('db.php');
    $usuario = $datos["usuario"] ?? "";
    $clave = $datos["clave"] ?? "";

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($user) && $clave == $user['clave']) { // recordar cambiar esto por encriptacion
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['admin'] = $user['admin'];
        $_SESSION['id'] = $user['id'];
        $_SESSION['dni'] = $user['dni'] ?? "";
        return "";
    } else {
        return "Usuario o contraseña incorrectos.";
    }
}