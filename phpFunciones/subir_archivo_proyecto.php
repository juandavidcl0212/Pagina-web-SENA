<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'prueba_db');
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

if (!isset($_SESSION['id_usuario'])) {
    die('No autorizado');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método no permitido');
}

$usuario_id = intval($_SESSION['id_usuario']);
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$comentario = trim($_POST['comentario'] ?? '');

if ($project_id <= 0 || $comentario === '') {
    die('Proyecto o comentario inválido.');
}

$stmt = $conn->prepare('SELECT id FROM proyectos WHERE id = ? AND usuario_id = ? AND tipo = "2D" LIMIT 1');
$stmt->bind_param('ii', $project_id, $usuario_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    die('Proyecto no encontrado o no es tu proyecto 2D.');
}
$stmt->close();

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    die('No se seleccionó ningún archivo o hubo un error en la subida.');
}

$archivo = $_FILES['archivo'];
$nombreArchivo = basename($archivo['name']);
$rutaCarpeta = dirname(__DIR__) . '/uploads/project_files';
if (!is_dir($rutaCarpeta)) {
    mkdir($rutaCarpeta, 0777, true);
}

$nombreUnico = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $nombreArchivo);
$rutaDestino = $rutaCarpeta . '/' . $nombreUnico;

if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    die('Error al mover el archivo subido.');
}

$rutaRelativa = 'uploads/project_files/' . $nombreUnico;
$comentarioLimpio = $conn->real_escape_string($comentario);
$mensaje = $comentarioLimpio . "\nArchivo: " . $rutaRelativa;

$stmt = $conn->prepare('INSERT INTO project_messages (proyecto_id, usuario_id, autor, mensaje, creado) VALUES (?, ?, "cliente", ?, NOW())');
$stmt->bind_param('iis', $project_id, $usuario_id, $mensaje);
if (!$stmt->execute()) {
    die('Error al guardar el mensaje: ' . $stmt->error);
}
$stmt->close();

header('Location: ../phpPaginas/mis_proyectos.php?success=1');
exit;
