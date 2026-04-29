<?php
session_start();

$conn = new mysqli("localhost", "root", "", "prueba_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

/* VALIDAR SESIÓN */
if (!isset($_SESSION['id_usuario'])) {
    die("No autorizado");
}

$usuario_id = intval($_SESSION['id_usuario']);

/* RECIBIR JSON */
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    die("Datos inválidos");
}

/* LIMPIAR DATOS */
$nombre = $conn->real_escape_string($data['nombre']);
$datos = $conn->real_escape_string(json_encode($data['objetos']));
$tipo = $conn->real_escape_string($data['tipo']); // 2D o 3D

/* INSERTAR */
$sql = "INSERT INTO proyectos (nombre, datos, usuario_id, tipo, fecha) 
        VALUES ('$nombre', '$datos', '$usuario_id', '$tipo', NOW())";

if ($conn->query($sql)) {
    echo "Proyecto guardado correctamente";
} else {
    echo "Error: " . $conn->error;
}
?>