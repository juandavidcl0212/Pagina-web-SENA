<?php
session_start();
$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (!isset($_SESSION['id_usuario'])) {
    die("Debes iniciar sesión.");
}

$id = $_SESSION['id_usuario'];

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    // Generar nombre único para la imagen
    $nombreArchivo = "perfil_" . $id . "_" . time() . ".png";
    $rutaDestino = "../uploads/" . $nombreArchivo;

    // Crear carpeta si no existe
    if (!is_dir("../uploads")) {
        mkdir("../uploads", 0777, true);
    }

    // Mover archivo al destino
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        // Actualizar en la base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET foto=? WHERE id=?");
        $stmt->bind_param("si", $nombreArchivo, $id);
        $stmt->execute();
        $stmt->close();

        // Actualizar en la sesión
        $_SESSION['foto'] = $nombreArchivo;

        // Redirigir al perfil para ver el cambio
        header("Location: ../phpPaginas/editar_perfil.php");
        exit();
    } else {
        echo "Error al mover el archivo al destino.";
    }
} else {
    echo "No se subió ninguna foto o hubo un error.";
}
?>

