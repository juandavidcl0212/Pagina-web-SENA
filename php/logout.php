<?php
session_start();

// Destruye todas las variables de sesión
$_SESSION = array();

// Destruye la sesión
session_destroy();

// Redirige al inicio de sesión o página principal
header("Location: ../index.php"); // Cambia la ruta según tu proyecto
exit();
?>
