<?php
session_start();
include("../conexion.php"); // asegúrate que $conn esté definido

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
        $mensaje = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo no es válido.";
    } else {
        // Verificar si el correo ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "Este correo ya está registrado. Intenta con otro.";
        } else {
            // Guardar usuario nuevo
            $claveHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, clave) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $apellido, $email, $claveHash);

            if ($stmt->execute()) {
                $mensaje = "✅ Registro exitoso. Ya puedes iniciar sesión.";
            } else {
                $mensaje = "❌ Error en el registro: " . $stmt->error;
            }
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrarse - Nook Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/Registrarse.css">
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
    <nav>
        <i class="fa-brands fa-instagram"></i>
        <i class="fa-brands fa-youtube"></i>
        <i class="fa-brands fa-facebook"></i>
    </nav>
</header>

<div class="cont">
    <h2>Registrarse</h2>
    <form action="Registrarse.php" method="POST">
        <input type="text" id="nombre" name="nombre" placeholder="Nombre" required><br>
        <input type="text" id="apellido" name="apellido" placeholder="Apellido" required><br>
        <input type="email" id="email" name="email" placeholder="Correo" required><br>
        <input type="password" id="password" name="password" placeholder="Contraseña" required><br>
        <button type="submit">Registrarse</button>
    </form>

    <?php if (!empty($mensaje)) echo "<p>$mensaje</p>"; ?>

    <nav class="cuentas">
        <a href="inSesion.php" class="btn-menu">¿Ya tienes cuenta? Inicia sesión</a>
    </nav>
</div>

</body>
</html>
