*actualiza el precio*

<?php

$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if(!isset($_POST['id']) || !isset($_POST['precio'])){
    die("Acceso no permitido");
}

$id = $_POST['id'];
$precio = $_POST['precio'];

if (!is_numeric($precio)) {
    die("El precio debe ser un número válido.");
}

$sql = "UPDATE membresias SET precio='$precio' WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    header("Location: bibliotecaAdmin.php");
    exit();
} else {
    echo "Error al actualizar: " . $conn->error;
}

$conn->close();

?>