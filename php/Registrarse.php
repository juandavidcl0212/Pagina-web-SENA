<?php
include("../conexion.php"); // Ajusta la ruta según tu estructura
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el correo ya existe
    $checkQuery = "SELECT * FROM usuarios WHERE email='$email'";
    $checkResult = mysqli_query($enlace, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Ya existe un usuario con ese correo
        $mensaje = "Este correo ya está registrado. Intenta con otro.";
    } else {
        // Insertar nuevo usuario
        $query = "INSERT INTO usuarios (nombre, apellido, email, clave) 
                  VALUES ('$nombre', '$apellido', '$email', '$password')";
        $resultado = mysqli_query($enlace, $query);

        if ($resultado) {
            $mensaje = "Registro exitoso";
        } else {
            $mensaje = "Error en el registro: " . mysqli_error($enlace);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="../CSS/Registrarse.css">
<link rel="stylesheet" href="../styles.css">

  <header class="encabezado">
    <!-- Logo a la izquierda -->
    <img src="../assets/fb p.png" alt="Logo fantasy box" class="fantasy box" >
  
    <!-- Menú o enlaces al centro -->
    <nav class="menu">
      <a href="../index.php" class="btn-menu">Inicio</a>
      <a href="../html/servicios.html" class="btn-menu">Servicios</a>
      <a href="../html/planes.html" class="btn-menu">Planes</a>
      <a href="../html/contacto.html" class="btn-menu">Contacto</a>
    </nav>
    <nav>

<i class="fa-brands fa-instagram"></i>
<i class="fa-brands fa-youtube"></i>
<i class="fa-brands fa-facebook"></i>
  </header>

<body>
    <div class="cont">
        <h2>Registrarse</h2>
       <form action="Registrarse.php" method="POST">
            <input type="nombre" id="nombre" name="nombre" placeholder="Nombre" required><br>
            <input type="apellido" id="apellido" name="apellido" placeholder="Apellido" required><br>
            <input type="email" id="email" name="email" placeholder="Correo" required><br>
            <input type="password" id="password" name="password" placeholder="Contraseña" required><br>
            <button type="submit">Registrarse</button>
  </form>
  <?php if (!empty($mensaje)) { echo "<p>$mensaje</p>"; } ?>

  <nav class="cuentas">
      <a href="inSesion.php" class="btn-menu">¿Ya tienes cuenta? Inicia sesión</a>
    </nav>

</div>
  </nav>
</body>
</html>