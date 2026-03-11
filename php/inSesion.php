<?php
session_start();
include("../conexion.php"); // Ajusta la ruta según tu estructura

if (isset($_POST['ingresar'])) {

  $email    = $_POST['loginEmail'];
  $password = password_hash($_POST['loginPassword'], PASSWORD_DEFAULT);

  $resultado= $conn->query("SELECT * FROM usuarios WHERE email ='$email'");

    if (mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Guardar en sesión
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['foto'] = $usuario['foto'] ?? "default.png";


        // Verificar rol
        if ($usuario['rol'] === 'admin') {
            header("Location: bibliotecaAdmin.php"); // Página para administradores
            exit;
            
        } else {
            header("Location: biblioteca.php"); // Página para usuarios normales
            exit;
        }
       
    } else {
        echo "<p>Correo o contraseña incorrectos</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../CSS/inSesionStyle.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <header class="encabezado">
    <img src="../assets/fb p.png" alt="Logo fantasy box" class="fantasy box">
    <nav class="menu">
      <a href="../index.php" class="btn-menu">Inicio</a>
      <a href="../html/servicios.html" class="btn-menu">Servicios</a>
      <a href="../php/planes.php" class="btn-menu">Planes</a>
      <a href="../html/contacto.html" class="btn-menu">Contacto</a>
    </nav>
    <i class="fa-brands fa-instagram"></i>
    <i class="fa-brands fa-youtube"></i>
    <i class="fa-brands fa-facebook"></i>
  </header>

  <div class="cont">
    <h2>Iniciar Sesión</h2>
    <form action="inSesion.php" method="POST">
      <input type="email" id="loginEmail" name="loginEmail" placeholder="Correo" required><br>
      <input type="password" id="loginPassword" name="loginPassword" placeholder="Contraseña" required><br>
      <button type="submit" name="ingresar">Ingresar</button>
    </form>

    <nav class="cuentas">
      <a href="Registrarse.php" class="btn-menu">¿No tienes cuenta? ¡Crear una!</a>
    </nav>
  </div>
</body>
</html>
