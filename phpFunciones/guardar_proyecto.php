<?php
session_start();
$conn = new mysqli("localhost", "root", "", "prueba_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recibir datos JSON
$data = json_decode(file_get_contents("php://input"), true);
$nombre = $data['nombre'];
$imagen = $data['imagen']; // base64

$usuario_id = $_SESSION['usuario']; // id del usuario logueado

$sql = "INSERT INTO proyectos (usuario_id, nombre, imagen) VALUES ('$usuario_id', '$nombre', '$imagen')";
if ($conn->query($sql) === TRUE) {
    echo "Proyecto guardado en la base de datos.";
} else {
    echo "Error: " . $conn->error;
}
?>