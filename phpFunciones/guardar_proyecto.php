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

if (!$data || !isset($data['nombre'])) {
    die("Datos inválidos");
}

/* LIMPIAR DATOS */
$nombre = $conn->real_escape_string($data['nombre']);

$payload = null;
if (isset($data['data'])) {
    $payload = $data['data'];
} elseif (isset($data['objetos'])) {
    $payload = [
        'tipo' => isset($data['tipo']) ? $data['tipo'] : '2D',
        'objetos' => $data['objetos']
    ];
} else {
    $payload = [
        'tipo' => isset($data['tipo']) ? $data['tipo'] : '2D',
        'objetos' => []
    ];
}

$contenido = json_encode($payload);
$datos = $conn->real_escape_string($contenido);

/* INSERTAR */
$sql = "INSERT INTO proyectos (nombre, data, usuario_id, fecha) 
        VALUES ('$nombre', '$datos', '$usuario_id', NOW())";

if ($conn->query($sql)) {
    echo "Proyecto guardado correctamente";
} else {
    echo "Error: " . $conn->error;
}
?>