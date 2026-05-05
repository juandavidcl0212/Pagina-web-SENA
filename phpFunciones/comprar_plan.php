<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../phpPaginas/inSesion.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$planId = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : null;
$banco = $_POST['banco'] ?? '';

$bancos_validos = ['Bancolombia', 'Davivienda', 'BBVA', 'Banco de Bogotá'];

if ($planId === null || $planId <= 0) {
    die("Plan inválido");
}

if ($banco !== '' && !in_array($banco, $bancos_validos)) {
    die("Banco inválido");
}

$stmt = $conn->prepare("SELECT nombre, precio FROM membresias WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $planId);
$stmt->execute();
$result = $stmt->get_result();
$planInfo = $result->fetch_assoc();
$stmt->close();

if (!$planInfo) {
    die("Plan no encontrado");
}

$plan = $planInfo['nombre'];
$precio = $planInfo['precio'];

// Aquí puedes integrar el pago real con la pasarela que elijas.
// Por ahora se simula que el pago se completó correctamente.

$stmt = $conn->prepare("UPDATE usuarios SET plan = ? WHERE id = ?");
$stmt->bind_param('si', $plan, $id_usuario);
$stmt->execute();
$stmt->close();

$_SESSION['plan'] = $plan;
if ($banco !== '') {
    $_SESSION['banco_pago'] = $banco;
}

header("Location: ../diseño/pagina3d.php");
exit;
?>