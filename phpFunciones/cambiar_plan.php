<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$id   = $_POST['id'];
$plan = $_POST['plan'];

$stmt = mysqli_prepare($enlace, "UPDATE usuarios SET plan = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $plan, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: ../bibliotecaAdmin.php");
/* 📥 Validar datos */
$id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$plan = $_POST['plan'] ?? '';

$planesPermitidos = ['Basico', 'Premium', 'Pro'];

if ($id <= 0 || !in_array($plan, $planesPermitidos)) {
    die("Datos inválidos");
}

/* 🔄 Actualizar plan */
$stmt = mysqli_prepare($enlace, "UPDATE usuarios SET plan = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $plan, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

/* 🔁 Redirigir correctamente */
header("Location: admin.php?seccion=usuarios");
exit();

?>



