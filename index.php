<?php
include("conexion.php");
session_start();

// Si el usuario ya inició sesión y tiene guardada la última página
$volver = null;
if (isset($_SESSION['id_usuario']) && isset($_SESSION['ultima_pagina'])) {
    $volver = $_SESSION['ultima_pagina'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nooke - Diseña tu espacio</title>
  <link rel="stylesheet" href="CSS/index.css">
  <link rel="stylesheet" href="styles.css">
  <script src="script.js"></script>
</head>
<body>
  <header class="encabezado">
    <!-- Logo a la izquierda -->
    <img src="assets/fb p.png" alt="Logo fantasy box" class="fantasy box">

    <!-- Menú o enlaces al centro -->
    <nav class="menu">
      <a href="index.php" class="btn-menu">Inicio</a>
      <a href="html/servicios.html" class="btn-menu">Servicios</a>
      <a href="php/planes.php" class="btn-menu">Planes</a>
      <a href="html/contacto.html" class="btn-menu">Contacto</a>
    </nav>

    <div class="cuenta">
      <?php if(!isset($_SESSION['id_usuario'])): ?>
        <!-- Usuario NO ha iniciado sesión -->
        <nav class="cuentas">
          <a href="php/Registrarse.php" class="btn-menu">¡Crear una cuenta!</a>
        </nav>
        <nav>
          <a href="php/inSesion.php" class="btn-menu">¿Ya tienes cuenta? Inicia sesión</a>
        </nav>
      <?php else: ?>
        <!-- Usuario SÍ ha iniciado sesión -->
        <img src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] !== '' 
              ? 'uploads/' . $_SESSION['foto'] 
              : 'assets/default.png'; ?>" 
             alt="Perfil" class="avatar" onclick="toggleMenu()">


        <!-- Ventana emergente -->
        <div id="menuPopup" class="menu-popup">
          
        <!-- Botón para volver a la última página -->
        <?php if(isset($_SESSION['rol'])): ?>
  <?php if($_SESSION['rol'] === 'admin'): ?>
    <button onclick="location.href='php/bibliotecaAdmin.php'">Volver al Inicio de Administrador</button>
  <?php else: ?>
    <button onclick="location.href='php/biblioteca.php'">Volver al Inicio</button>
  <?php endif; ?>
<?php endif; ?>

          <button onclick="location.href='php/editar_perfil.php'">Editar Perfil</button>
          <button onclick="location.href='index.php'">Cerrar Sesión</button>
        </div>
      <?php endif; ?>
    </div>
  </header>

  <img src="assets/nooke g.png" class="nooke-logo">

  <!-- Contenido principal -->
  <nav class="content">
    <div class="info">
      <h2>¿DE QUÉ TRATA?</h2>
      <p>
        El motivo principal del proyecto Nook brinda a los usuarios una interfaz de diseñador 
        que permite organizar preparativos de una habitación, sala, etc. para cualquier tipo de evento, 
        o incluso si solo se trata de mejorar la estética del lugar.
      </p>
    </div>

    <div class="botones">
      <h1>¡DISEÑA TU ESPACIO COMO UN PROFESIONAL!</h1>
      <button onclick="location.href='html/servicios.html'">VER SERVICIOS</button>
      <button onclick="location.href='php/planes.php'">VER PLANES</button>
    </div>
  </nav>

  <!-- Redes sociales -->
  <footer>
    <nav class="redes">
      <a href="#" class="icon"><i class="fab fa-instagram"></i></a>
      <a href="#" class="icon"><i class="fab fa-facebook-f"></i></a>
    </nav>
    <nav class="links">
      <script src="script.js"></script>
    </nav>
  </footer>
</body>
</html>