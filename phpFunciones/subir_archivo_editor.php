<?php
session_start();

$conn = new mysqli("localhost", "root", "", "prueba_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión con la base de datos']);
    exit;
}

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se seleccionó archivo o hubo un error en la subida']);
    exit;
}

$archivo = $_FILES['archivo'];
$permitidos = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf'
];

if (!in_array($archivo['type'], $permitidos)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
    exit;
}

$nombreArchivo = basename($archivo['name']);
$rutaCarpeta = dirname(__DIR__) . '/uploads/editor_files';
if (!is_dir($rutaCarpeta)) {
    mkdir($rutaCarpeta, 0777, true);
}

$nombreUnico = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $nombreArchivo);
$rutaDestino = $rutaCarpeta . '/' . $nombreUnico;

if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al mover el archivo subido']);
    exit;
}

$rutaRelativa = 'uploads/editor_files/' . $nombreUnico;

echo json_encode(['success' => true, 'url' => $rutaRelativa]);
?>