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
    <img src="../assets/fb p.png" alt="Logo fantasy box" class="fantasy box">

    <!-- Menú o enlaces al centro -->
    <nav class="menu">
      <a href="../index.php" class="btn-menu">Inicio</a>
      <a href="../phpPaginas/servicios.php" class="btn-menu">Servicios</a>
      <a href="../phpPaginas/planes.php" class="btn-menu">Planes</a>
      <a href="../phpPaginas/contacto.php" class="btn-menu">Contacto</a>
    </nav>

    <div class="cuenta">
      <?php if(!isset($_SESSION['id_usuario'])): ?>
        <!-- Usuario NO ha iniciado sesión -->
        <nav class="cuentas">
          <a href="../phpPaginas/Registrarse.php" class="btn-menu">¡Crear una cuenta!</a>
        </nav>
        <nav>
          <a href="../phpPaginas/inSesion.php" class="btn-menu">¿Ya tienes cuenta? Inicia sesión</a>
        </nav>
      <?php else: ?>
        <!-- Usuario SÍ ha iniciado sesión -->
        <img src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] !== '' 
              ? '../uploads/' . $_SESSION['foto'] 
              : '../assets/default.png'; ?>" 
             alt="Perfil" class="avatar" onclick="toggleMenu()">


        <!-- Ventana emergente -->
        <div id="menuPopup" class="menu-popup">
          
        <!-- Botón para volver a la última página -->
        <?php if(isset($_SESSION['rol'])): ?>
  <?php if($_SESSION['rol'] === 'admin'): ?>
    <button onclick="location.href='phpPaginas/bibliotecaAdmin.php'">Volver a la Biblioteca de Administrador</button>
  <?php else: ?>
    <button onclick="location.href='phpPaginas/biblioteca.php'">Volver a la Biblioteca</button>
  <?php endif; ?>
<?php endif; ?>

          <button onclick="location.href='../phpPaginas/editar_perfil.php'">Editar Perfil</button>
          <button onclick="location.href='../phpFunciones/logout.php'">Cerrar Sesión</button>
        </div>
      <?php endif; ?>
    </div>

    <script src="../script.js"></script>
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
    <script src="../js/script.js"></script>
    <form method="POST" action="../phpFunciones/actualizar_precio.php">
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