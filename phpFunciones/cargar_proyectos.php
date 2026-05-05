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
        SELECT id, nombre, data FROM proyectos 
        WHERE id = $id AND usuario_id = $usuario_id
        LIMIT 1
    ");

    $row = $result->fetch_assoc();
    if ($row && isset($row['data'])) {
        $parsed = json_decode($row['data'], true);
        if (is_array($parsed) && isset($parsed['tipo'])) {
            $row['tipo'] = $parsed['tipo'];
        }
    }
    echo json_encode($row);
    exit();
}

/* 🔥 SI NO, DEVUELVE TODOS */
$result = $conn->query("
    SELECT id, nombre, data FROM proyectos 
    WHERE usuario_id = $usuario_id
    ORDER BY fecha DESC
");

$proyectos = [];

while ($row = $result->fetch_assoc()) {
    if (isset($row['data'])) {
        $parsed = json_decode($row['data'], true);
        if (is_array($parsed) && isset($parsed['tipo'])) {
            $row['tipo'] = $parsed['tipo'];
        }
    }
    $proyectos[] = $row;
}

echo json_encode($proyectos);
?>