<?php
session_start();
include("../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];

    $stmt = mysqli_prepare($enlace, "SELECT * FROM usuarios WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        if (password_verify($password, $usuario['clave'])) {

            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['nombre'] = $usuario['nombre'];

            if ($usuario['rol'] === 'admin') {
                header("Location: bibliotecaAdmin.php");
            } else {
                header("Location: biblioteca.php");
            }
            exit;

        } else {
            echo "<p>Contraseña incorrecta</p>";
        }

    } else {
        echo "<p>Correo no encontrado</p>";
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
        <a href="../phpPaginas/servicios.php" class="btn-menu">Servicios</a>
        <a href="../phpPaginas/planes.php" class="btn-menu">Planes</a>
        <a href="../phpPaginas/contacto.php" class="btn-menu">Contacto</a>
    </nav>
    <nav>
        <i class="fa-brands fa-instagram"></i>
        <i class="fa-brands fa-youtube"></i>
        <i class="fa-brands fa-facebook"></i>
    </nav>
</header>

  <div class="cont">
    <h2>Iniciar Sesión</h2>
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
