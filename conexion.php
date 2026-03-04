<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDatos = "prueba_db";

// Crear conexión
$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDatos);

// Alias para compatibilidad
$conn = $enlace;

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
