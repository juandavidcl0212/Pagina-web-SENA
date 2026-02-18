<?php
include("../conexion.php"); // Ajusta la ruta según tu estructura

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];

    $query = "SELECT * FROM usuarios WHERE email='$email' AND clave='$password'";
    $resultado = mysqli_query($enlace, $query);

    if (mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verificar rol
        if ($usuario['rol'] === 'admin') {
            header("Location: ../html/bibliotecaAdmin.html"); // Página para administradores
            exit;
        } else {
            header("Location: ../html/biblioteca.html"); // Página para usuarios normales
            exit;
        }
    } else {
        echo "<p>Correo o contraseña incorrectos</p>";
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
<link rel="stylesheet" href="../CSS/inSesionStyle.css">
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

<i class="fa-brands fa-instagram"></i>
<i class="fa-brands fa-youtube"></i>
<i class="fa-brands fa-facebook"></i>
  </header>
  
<body>
    <div class="cont">
        <h2>Iniciar Sesion</h2>
       <form action="inSesion.php" method="POST">
    <input type="email" id="loginEmail" name="loginEmail" placeholder="Correo" required><br>
    <input type="password" id="loginPassword" name="loginPassword" placeholder="Contraseña" required><br>
    <button type="submit">Ingresar</button>
  </form>

  <nav class="cuentas">
        <a href="Registrarse.php" class="btn-menu">¿No tienes cuenta? ¡Crear una!</a>
  </nav>
    </div>
</body>
</html>