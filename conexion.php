<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDatos = "prueba_db";

// Crear conexión
$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDatos);

// Verificar conexión
if (!$enlace) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
