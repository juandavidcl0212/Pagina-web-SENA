<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// 🔹 CONEXIÓN (⚠️ CAMBIA EL NOMBRE SI NO ES prueba_db)
$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 🔹 DATOS
$proyecto_id = $_POST['project_id'] ?? null;
$mensaje = trim($_POST['comentario'] ?? '');
$usuario_id = $_SESSION['id_usuario'] ?? null;

// 🔹 VALIDACIÓN
if (!$proyecto_id || !$mensaje || !$usuario_id) {
    die("Faltan datos obligatorios");
}

// 🔹 ARCHIVO
$nombreArchivo = null;

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {

    $nombreOriginal = basename($_FILES['archivo']['name']);
    $nombreArchivo = time() . "_" . $nombreOriginal;

    $rutaDestino = "../uploads/" . $nombreArchivo;

    // Crear carpeta si no existe
    if (!is_dir("../uploads")) {
        mkdir("../uploads", 0777, true);
    }

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {
        die("Error al subir el archivo");
    }
}

// 🔹 AUTOR
$autor = "usuario";

// 🔥 VERIFICAR SI EXISTE LA COLUMNA 'visto'
$check = $conn->query("SHOW COLUMNS FROM project_messages LIKE 'visto'");
$tieneVisto = ($check && $check->num_rows > 0);

// 🔹 SQL DINÁMICO (NO FALLA NUNCA)
if ($tieneVisto) {

    $sql = "INSERT INTO project_messages 
    (proyecto_id, autor, archivo, mensaje, usuario_id, eliminado, visto) 
    VALUES (?, ?, ?, ?, ?, 0, 0)";

} else {

    $sql = "INSERT INTO project_messages 
    (proyecto_id, autor, archivo, mensaje, usuario_id, eliminado) 
    VALUES (?, ?, ?, ?, ?, 0)";
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta SQL: " . $conn->error);
}

$stmt->bind_param("isssi", $proyecto_id, $autor, $nombreArchivo, $mensaje, $usuario_id);

// 🔹 EJECUTAR
if ($stmt->execute()) {
    header("Location: ../phpPaginas/mis_proyectos.php?ok=1");
    exit;
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
