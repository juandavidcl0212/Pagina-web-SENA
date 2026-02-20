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
exit();
?>

