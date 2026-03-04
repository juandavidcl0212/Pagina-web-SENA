<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "prueba_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta de planes
$result = $conn->query("SELECT * FROM membresias");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes - Nook Studio</title>
    <link rel="stylesheet" href="../CSS/style_planes.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<header class="encabezado">
    <!-- Logo -->
    <img src="../assets/fb p.png" alt="Logo fantasy box" class="fantasy-box">

    <!-- Menú -->
    <nav class="menu">
      <a href="../index.php" class="btn-menu">Inicio</a>
      <a href="../html/servicios.html" class="btn-menu">Servicios</a>
      <a href="../php/planes.php" class="btn-menu">Planes</a>
      <a href="../html/contacto.html" class="btn-menu">Contacto</a>
    </nav>

    <!-- Cuentas -->
    <nav class="cuentas">
      <a href="../php/Registrarse.php" class="btn-menu">¡Crear una cuenta!</a>
      <a href="../php/inSesion.php" class="btn-menu">¿Ya tienes cuenta? Inicia sesión</a>
    </nav>

    <!-- Redes -->
    <div class="redes">
      <i class="fa-brands fa-instagram"></i>
      <i class="fa-brands fa-youtube"></i>
      <i class="fa-brands fa-facebook"></i>
    </div>
</header>

<main class="content">
    <h1>PLANES</h1>
    <div class="planes">
        <?php while($row = $result->fetch_assoc()) { ?>
            <div class="plan-card">
                <h2><?php echo $row['nombre']; ?></h2>
                <div class="precio">
                    <h3>$<?php echo $row['precio']; ?> USD</h3>
                    <p>/mes</p>
                </div>
                <p><?php echo $row['descripcion']; ?></p>
                <button type="submit" class="btn-comprar">COMPRAR</button>
                <!-- Menú o enlaces al centro -->
    <nav class="menu">
      <a href="../index.php" class="btn-menu">Inicio</a>
      <a href="../html/servicios.html" class="btn-menu">Servicios</a>
      <a href="../html/planes.html" class="btn-menu">Planes</a>
      <a href="../html/contacto.html" class="btn-menu">Contacto</a>
    </nav>
    <nav>
      <nav class="cuentas">
        <a href="../php/Registrarse.php" class="btn-menu">¡Crear una cuenta!</a>
    </nav>
    <nav>
      <a href="../php/inSesion.php" class="btn-menu">¿Ya tienes cuenta? Inicia sesión</a>
    </nav>
  </nav>

<i class="fa-brands fa-instagram"></i>
<i class="fa-brands fa-youtube"></i>
<i class="fa-brands fa-facebook"></i>
  </header>
</head>
<body>
    <nav class="content">
        <h1>PLANES</h1>
    <div class="planes">
        <div class="plan1">
            <h2>Personal</h2>
            <div class="precio1">
                <h3>$30 USD</h3>
                <p>/mes</p>
            </div>
            <ul>
                <li>Consulta de diseño de interiores.</li>
                <li>1 habitacion/sala decorada</li>
                <li>Soporte por e-mail</li>
            </ul>
            <button type="submit" class="plan1B">COMPRAR</button>
        </div>
        <div class="plan2">
            <h2>Familiar</h2>
            <div class="precio2">
                <h3>$90 USD</h3>
                <p>/mes</p>
            </div>
            <ul>
                <li>Consulta de diseño de interiores.</li>
                <li>3 habitaciones/salas decoradas</li>
                <li>Soporte por diario</li>
            </ul>
            <button type="submit" class="plan2B">COMPRAR</button>
        </div>
        <div class="plan3">
            <h2>Institucional</h2>
            <div class="precio3">
                <h3>$250 USD</h3>
                <p>/mes</p>
            </div>
            <ul>
                <li>Consulta de diseño de interiores.</li>
                <li>12 habitaciones/salas decoradas</li>
                <li>Soporte dedicado</li>
            </ul>
            <button type="submit" class="plan3B">COMPRAR</button>
        </div>
    </div>
    </div>
    </nav>
            </div>
        <?php } ?>
    </div>
</main>

<script src="../js/script.js"></script>
</body>
</html>

<?php

$conn = new mysqli("localhost", "root", "", "prueba_db");

$result = $conn->query("SELECT * FROM membresias");

while($row = $result->fetch_assoc()) {

echo "<h2>".$row['nombre']."</h2>";

echo "<p>Precio: $".$row['precio']."</p>";

}

?>