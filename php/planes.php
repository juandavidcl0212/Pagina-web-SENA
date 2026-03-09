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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/style planes.css">
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
    <script src="js\script.js"></script>
    <form method="POST" action="actualizar_precio.php">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <input type="text" name="precio" value="<?php echo $row['precio']; ?>">
    <button type="submit">Guardar</button>
</body>
</html>
       
    </div>
</main>

<script src="../js/script.js"></script>
</body>
</html>

<?php

$conn = new mysqli("localhost", "root", "", "membresias");

$result = $conn->query("SELECT * FROM membresias");

while($row = $result->fetch_assoc()) 

echo "<h2>".$row['nombre']."</h2>";

echo "<p>Precio: $".$row['precio']."</p>";

if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin')  ?>
    <form method="POST" action="actualizar_precio.php" style="display:inline;">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <input type="text" name="precio" value="<?php echo $row['precio']; ?>">
        <button type="submit" title="Editar precio">
            <i class="fas fa-edit"></i> Guardar
        </button>
    </form>



?>