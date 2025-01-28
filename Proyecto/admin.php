<?php
session_start();
include 'cabecera.php';
include 'funciones_admin/funcion_menu.php';
include 'funciones_admin/funcion_mostrar.php';
include 'funciones_admin/funcion_db.php';

if (!empty($_SESSION)) {
    // Si el usuario es esta registrado en solicitantes
    if ($_SESSION['admin'] == 1) {
        $accionGET = $_GET['accion'] ?? '';
        $accionPOST = $_POST['accion'] ?? '';
        if ($accionGET == '' && $accionPOST == '') {
            generarCabecera();
            generarMenu();
        }
    } else {
        header("Location: formulario_solicitante.php");
    }
} else {
    header("Location: inicio.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $accion = $_GET['accion'] ?? '';

    switch ($accion) {
        case 'mostrarActiDesac':
            mostrarActiDesac();
            break;

        case 'mostrarAdmitidos':
            mostrarAdmitidos();
            break;

        case 'mostrarEliminar':
            mostrarEliminar();
            break;

        case 'mostrarIncorporar':
            mostrarIncorporar();
            break;

        case 'mostrarBaremacion':
            mostrarBaremacion();
            break;
        
        case 'mostrarNumeroPlazas':
            mostrarNumeroPlazas("");
            break;

        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    switch ($accion) {
        case 'actualizarEstado':
            actualizarEstado();
            break;

        case 'eliminarCurso':
            eliminarCurso();
            break;

        case 'validarIncorporar':
            validarCurso();
            break;

        case 'realizarBaremacion':
            realizarBaremacion();
            break;

        case 'validarNumeroPlazas':
            validarNumeroPlazas();
            break;

        default:
            break;
    }
}
