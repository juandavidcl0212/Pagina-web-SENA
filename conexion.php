<?php
$servidor="localhost";
$usuario="root";
$clave="";
$baseDatos="prueba_db";
$enlace=mysqli_connect(hostname: $servidor,username: $usuario,password: $clave, database: $baseDatos);
if (!$enlace) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>