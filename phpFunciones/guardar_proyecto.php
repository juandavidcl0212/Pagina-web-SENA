<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error de conexion: " . $conn->connect_error]);
    exit;
}

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "message" => "No autorizado"]);
    exit;
}

$usuario_id = intval($_SESSION['id_usuario']);
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['nombre'])) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "Datos invalidos"]);
    exit;
}

$nombre = trim($data['nombre']);
$project_id = isset($data['id']) && $data['id'] !== '' ? intval($data['id']) : 0;

if (isset($data['data']) && is_array($data['data'])) {
    $payload = $data['data'];
} elseif (isset($data['objetos'])) {
    $payload = [
        "tipo" => $data['tipo'] ?? "2D",
        "objetos" => $data['objetos']
    ];
} else {
    $payload = [
        "tipo" => $data['tipo'] ?? "2D",
        "objetos" => []
    ];
}

if (!isset($payload['tipo'])) {
    $payload['tipo'] = $data['tipo'] ?? "2D";
}

$contenido = json_encode($payload, JSON_UNESCAPED_UNICODE);

if ($project_id > 0) {
    $stmt = $conn->prepare("UPDATE proyectos SET nombre = ?, data = ?, fecha = NOW() WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ssii", $nombre, $contenido, $project_id, $usuario_id);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo json_encode(["ok" => true, "id" => $project_id, "message" => "Proyecto actualizado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["ok" => false, "message" => "No se pudo actualizar el proyecto"]);
    }

    $stmt->close();
    exit;
}

$stmt = $conn->prepare("INSERT INTO proyectos (nombre, data, usuario_id, fecha) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("ssi", $nombre, $contenido, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "id" => $stmt->insert_id, "message" => "Proyecto guardado correctamente"]);
} else {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
