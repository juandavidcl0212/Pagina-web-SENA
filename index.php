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
    <nav>
      <a href="index.php">Inicio</a>
      <a href="servicios.php">Servicios</a>
      <a href="planes.php">Planes</a>
      <a href="contacto.php">Contacto</a>
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

        <!-- Botón para volver a la última página -->
        <?php if(isset($_SESSION['rol'])): ?>
  <?php if($_SESSION['rol'] === 'admin'): ?>
    <a href="php/bibliotecaAdmin.php" class="btn-menu">Volver a Administrador</a>
  <?php else: ?>
    <a href="php/biblioteca.php" class="btn-menu">Volver a Biblioteca</a>
  <?php endif; ?>
<?php endif; ?>


        <!-- Ventana emergente -->
        <div id="menuPopup" class="menu-popup">
          <form method="POST" action="php/subir_foto.php" enctype="multipart/form-data">
            <input type="file" name="foto" accept="image/*">
            <button type="submit">Actualizar Foto</button>
          </form>
        
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
      <button onclick="location.href='html/planes.html'">VER PLANES</button>
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
