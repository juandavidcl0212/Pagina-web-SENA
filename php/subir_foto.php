<?php

session_start();
$conn = new mysqli("localhost", "root", "", "prueba_db");

$id = $_SESSION['id_usuario'];

if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $nombreArchivo = "perfil_" . $id . "_" . time() . ".png";
    move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/" . $nombreArchivo);

    $conn->query("UPDATE usuarios SET foto='$nombreArchivo' WHERE id=$id");
    $_SESSION['foto'] = $nombreArchivo;

    header("Location: ../html/panel.php"); // o donde esté tu panel
    exit();
} else {
    echo "Error al subir la imagen.";
}
?>
