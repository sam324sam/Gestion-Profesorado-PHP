<?php

session_start();
include "cabecera.php";

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (empty($_SESSION)) {
        generarFormulario("");
    } else {
        header("Location: inicio.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "db.php";
    
    $usuario = $_POST["usuario"] ?? "";
    $contrasena = $_POST["clave"] ?? "";
    
    if ($usuario == "" || $contrasena == "") {
        $error = "Por favor, llene todos los campos.";
        generarFormulario($error);
    } else {
        // Verificar si el usuario ya existe
        $query = "SELECT * FROM usuarios WHERE usuario = :usuario";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "El usuario ya existe. Por favor, elija otro nombre de usuario.";
            generarFormulario($error);
        } else {
            // Insertar nuevo usuario
            $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, clave) VALUES (?, ?)");
            if ($stmt->execute([$usuario, $contrasena])) {
                generarCabecera();
                echo "<div class='resultado'>";
                echo "<h2>Registro de Usuario</h2>";
                echo "Registro exitoso. <a href='formulario_login.php'>Iniciar sesión</a>";
                echo "</div>";
            } else {
                echo "Error: " . $stmt->errorInfo()[2];
            }
        }
    }
}

function generarFormulario($error)
{
    generarCabecera();
    echo "<form method='post' action='formulario_registro.php'>";
    echo "<h2>Registro de Usuario</h2>";
    echo "<span class='error'>$error</span><br><br>";
    echo "Usuario: <input type='text' name='usuario'><br><br>";
    echo "Contraseña: <input type='password' name='clave'><br><br>";
    echo "<input type='submit' value='Registrar'>";
    echo "</form>";
}