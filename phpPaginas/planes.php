<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta de planes
$result = $conn->query("SELECT * FROM membresias ORDER BY id ASC");

$planes = [];
while($row = $result->fetch_assoc()){
    $planes[$row['id']] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planes</title>
    <link rel="stylesheet" href="../CSS/style planes.css">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>

<!-- ENCABEZADO -->
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

    <nav class="cuenta">
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
    <button onclick="location.href='../phpPaginas/bibliotecaAdmin.php'">Volver a la Biblioteca de Administrador</button>
  <?php else: ?>
    <button onclick="location.href='../phpPaginas/biblioteca.php'">Volver a la Biblioteca</button>
  <?php endif; ?>
<?php endif; ?>

          <button onclick="location.href='../phpPaginas/editar_perfil.php'">Editar Perfil</button>
          <button onclick="location.href='../phpFunciones/logout.php'">Cerrar Sesión</button>
        </div>
      <?php endif; ?>
    </nav>

    <script src="../script.js"></script>
  </header>

<h1 style="text-align:center;">PLANES</h1>

<div class="planes">

    <!-- PLAN PERSONAL -->
    <div class="plan1">
        <h2>Personal</h2>
        <div class="precio1">
            <h3>$<?php echo isset($planes[1]) ? $planes[1]['precio'] : '0'; ?></h3>
            <p>/mes</p>
        </div>
        <ul>
            <li>Consulta de diseño de interiores.</li>
            <li>1 habitación/sala decorada</li>
            <li>Soporte por e-mail</li>
        </ul>

        <form method="POST" action="../phpFunciones/comprar_plan.php">
            <input type="hidden" name="plan" value="Personal">
            <button type="submit" class="plan1B">COMPRAR</button>
        </form>
    </div>

    <!-- PLAN FAMILIAR -->
    <div class="plan2">
        <h2>Familiar</h2>
        <div class="precio2">
            <h3>$<?php echo isset($planes[2]) ? $planes[2]['precio'] : '0'; ?></h3>
            <p>/mes</p>
        </div>
        <ul>
            <li>Consulta de diseño de interiores.</li>
            <li>3 habitaciones/salas decoradas</li>
            <li>Soporte diario</li>
        </ul>

        <form method="POST" action="../phpFunciones/comprar_plan.php">
            <input type="hidden" name="plan" value="Familiar">
            <button type="submit" class="plan2B">COMPRAR</button>
        </form>
    </div>

    <!-- PLAN INSTITUCIONAL -->
    <div class="plan3">
        <h2>Institucional</h2>
        <div class="precio3">
            <h3>$<?php echo isset($planes[3]) ? $planes[3]['precio'] : '0'; ?></h3>
            <p>/mes</p>
        </div>
        <ul>
            <li>Consulta de diseño de interiores.</li>
            <li>12 habitaciones/salas decoradas</li>
            <li>Soporte dedicado</li>
        </ul>

        <form method="POST" action="../phpFunciones/comprar_plan.php">
            <input type="hidden" name="plan" value="Institucional">
            <button type="submit" class="plan3B">COMPRAR</button>
        </form>
    </div>

</div>

</body>
</html>