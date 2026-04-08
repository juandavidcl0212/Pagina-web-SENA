*actualiza el precio*

<?php
$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if(!isset($_POST['id']) || !isset($_POST['precio'])){
    die("Acceso no permitido");
}

$id = intval($_POST['id']);       // aseguramos que sea número entero
$precio = $_POST['precio'];

if (!is_numeric($precio)) {
    die("El precio debe ser un número válido.");
}

// Usar prepared statement
$stmt = $conn->prepare("UPDATE membresias SET precio=? WHERE id=?");
$stmt->bind_param("di", $precio, $id);

if ($stmt->execute()) {
    // Redirigir al listado de planes
    header("Location: ../phpPaginas/planes.php");
    exit();
} else {
    echo "Error al actualizar: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
