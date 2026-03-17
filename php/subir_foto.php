<?php
session_start();
$conn = new mysqli("localhost", "root", "", "prueba_db");

$id = $_SESSION['id_usuario'];

if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $nombreArchivo = "perfil_" . $id . "_" . time() . ".png";
    move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/" . $nombreArchivo);

    // Actualizar en la base de datos
    $conn->query("UPDATE usuarios SET foto='$nombreArchivo' WHERE id=$id");

    // Actualizar en la sesión automáticamente
    $_SESSION['foto'] = $nombreArchivo;

    // Redirigir según rol
    if($_SESSION['rol'] === 'admin') {
        header("Location: bibliotecaAdmin.php");
    } else {
        header("Location: biblioteca.php");
    }
    exit();
} else {
    echo "Error al subir la imagen.";
}
?>


