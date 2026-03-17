<?php
session_start();
$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscar usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, nombre, password, rol, foto FROM usuarios WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        // Verificar contraseña
        if (password_verify($password, $usuario['password'])) {
            // Guardar datos en la sesión
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['foto'] = $usuario['foto']; // Aquí se carga automáticamente la foto

            // Redirigir según rol
            if ($usuario['rol'] === 'admin') {
                header("Location: bibliotecaAdmin.php");
            } else {
                header("Location: biblioteca.php");
            }
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
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
