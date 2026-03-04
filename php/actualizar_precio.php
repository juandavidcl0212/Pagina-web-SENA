<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "tu_base");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recibir datos del formulario
$id = $_POST['id'];
$precio = $_POST['precio'];

// Validar que el precio sea numérico
if (!is_numeric($precio)) {
    die("El precio debe ser un número válido.");
}

// Actualizar en la base de datos
$sql = "UPDATE membresias SET precio='$precio' WHERE id='$id'";
if ($conn->query($sql) === TRUE) {
    // Redirigir de nuevo al dashboard
    header("Location: dashboard.php");
    exit();
} else {
    echo "Error al actualizar: " . $conn->error;
}

$conn->close();
?>
