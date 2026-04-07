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
      <a href="phpPaginas/servicios.php" class="btn-menu">Servicios</a>
      <a href="phpPaginas/planes.php" class="btn-menu">Planes</a>
      <a href="phpPaginas/contacto.php" class="btn-menu">Contacto</a>
    </nav>

    <div class="cuenta">
      <?php if(!isset($_SESSION['id_usuario'])): ?>
        <!-- Usuario NO ha iniciado sesión -->
        <nav class="cuentas">
          <a href="phpPaginas/Registrarse.php" class="btn-menu">¡Crear una cuenta!</a>
        </nav>
        <nav>
          <a href="phpPaginas/inSesion.php" class="btn-menu">¿Ya tienes cuenta? Inicia sesión</a>
        </nav>
      <?php else: ?>
        <!-- Usuario SÍ ha iniciado sesión -->
        <img src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] !== '' 
              ? 'uploads/' . $_SESSION['foto'] 
              : 'assets/default.png'; ?>" 
             alt="Perfil" class="avatar" onclick="toggleMenu()">


        <!-- Ventana emergente -->
        <nav id="menuPopup" class="menu-popup">
          
        <!-- Botón para volver a la última página -->
        <?php if(isset($_SESSION['rol'])): ?>
  <?php if($_SESSION['rol'] === 'admin'): ?>
    <button onclick="location.href='phpPaginas/bibliotecaAdmin.php'">Volver a la Biblioteca de Administrador</button>
  <?php else: ?>
    <button onclick="location.href='phpPaginas/biblioteca.php'">Volver a la Biblioteca</button>
  <?php endif; ?>
<?php endif; ?>

          <button onclick="location.href='phpPaginas/editar_perfil.php'">Editar Perfil</button>
          <button onclick="location.href='phpFunciones/logout.php'">Cerrar Sesión</button>
  </nav>
      <?php endif; ?>
    </div>

    <script src="script.js"></script>
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
      <button onclick="location.href='phpPaginas/servicios.php'">VER SERVICIOS</button>
      <button onclick="location.href='phpPaginas/planes.php'">VER PLANES</button>
    </div>
  </nav>

  <!-- Redes sociales -->
  <footer>
    <nav class="redes">
      <a href="#" class="icon"><i class="fab fa-instagram"></i></a>
      <a href="#" class="icon"><i class="fab fa-facebook-f"></i></a>
    </nav>
    <nav class="links">
    </nav>
  </footer>
</body>
</html>