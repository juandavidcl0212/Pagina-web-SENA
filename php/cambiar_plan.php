<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../servicios.php");
    exit();
}

$id = $_POST['id'];
$plan = $_POST['plan'];

$sql = "UPDATE usuarios SET plan='$plan' WHERE id='$id'";
$conn->query($sql);

header("Location: usuarios_admin.php");
exit();
?>
