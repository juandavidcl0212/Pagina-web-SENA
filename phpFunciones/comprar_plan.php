<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../phpPaginas/inSesion.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$plan = $_POST['plan'] ?? '';

// Validar planes
$planes_validos = ['Personal', 'Familiar', 'Institucional'];

if (!in_array($plan, $planes_validos)) {
    die("Plan inválido");
}

// Guardar en BD
$sql = "UPDATE usuarios SET plan = '$plan' WHERE id = '$id_usuario'";
mysqli_query($conn, $sql);

// Guardar en sesión
$_SESSION['plan'] = $plan;

// Redirigir
header("Location: ../diseño/pagina3d.php");
exit;
?>