<?php
session_start();
include("../conexion.php");

header('Content-Type: application/json');

// 1. Verificar login
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["status" => "no_login"]);
    exit;
}

$id = $_SESSION['id_usuario'];

// 2. Consultar si ya usó la prueba
$sql = "SELECT prueba_usada FROM usuarios WHERE id = '$id'";
$resultado = mysqli_query($conn, $sql);
$usuario = mysqli_fetch_assoc($resultado);

if ($usuario['prueba_usada'] == 1) {
    echo json_encode(["status" => "usado"]);
    exit;
}

// 3. Marcar prueba como usada
$update = "UPDATE usuarios SET prueba_usada = 1 WHERE id = '$id'";
mysqli_query($conexion, $update);

// 4. Permitir acceso
echo json_encode(["status" => "ok"]);