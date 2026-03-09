<?php
$conn = new mysqli("localhost", "root", "", "prueba_db");
$json = file_get_contents("php://input");
$data = json_decode($json, true);

$nombre = $data['nombre'];
$contenido = json_encode($data['data']);

$sql = "INSERT INTO proyectos (usuario_id, nombre, data) VALUES (1, '$nombre', '$contenido')";
if ($conn->query($sql)) {
    echo "Proyecto guardado en la base de datos.";
} else {
    echo "Error: " . $conn->error;
}
?>