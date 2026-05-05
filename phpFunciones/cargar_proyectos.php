<?php
session_start();

$conn = new mysqli("localhost", "root", "", "prueba_db");

if (!isset($_SESSION['id_usuario'])) {
    die("No autorizado");
}

$usuario_id = intval($_SESSION['id_usuario']);

/* 🔥 SI PIDEN UN PROYECTO POR ID */
if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $result = $conn->query("
        SELECT * FROM proyectos 
        WHERE id = $id AND usuario_id = $usuario_id
        LIMIT 1
    ");

    echo json_encode($result->fetch_assoc());
    exit();
}

/* 🔥 SI NO, DEVUELVE TODOS */
$result = $conn->query("
    SELECT id, nombre, datos, tipo 
    FROM proyectos 
    WHERE usuario_id = $usuario_id
    ORDER BY fecha DESC
");

$proyectos = [];

while ($row = $result->fetch_assoc()) {
    $proyectos[] = $row;
}

echo json_encode($proyectos);
?>