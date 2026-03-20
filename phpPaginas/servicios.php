<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Servicios | Decoraciones de Interiores</title>

  <link rel="stylesheet" href="../CSS/servicios.css">
  <link rel="stylesheet" href="../styles.css">
</head>
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

<body>
 <div class="content">
    <nav class="info">
    <h1>Nuestros Servicios</h1>
    <p>
      Diseñamos espacios únicos que reflejan tu estilo y personalidad.
      Nos especializamos en crear ambientes funcionales, modernos y elegantes.
    </p>
  </nav>

  <!-- SERVICIOS -->
  <nav class="servicios">

    <div class="servicio">
      <h2>Decoración Residencial</h2>
      <p>
        Transformamos casas y apartamentos en espacios acogedores,
        funcionales y con identidad propia.
      </p>
      <button class="btn naranja">Más información</button>
    </div>

    <div class="servicio">
      <h2>Decoración Comercial</h2>
      <p>
        Diseños estratégicos para oficinas, tiendas y locales comerciales
        que mejoran la experiencia del cliente.
      </p>
      <button class="btn morado">Ver proyectos</button>
    </div>

    <div class="servicio">
      <h2>Asesoría Personalizada</h2>
      <p>
        Te guiamos en la selección de colores, mobiliario y distribución
        para lograr el mejor resultado.
      </p>
      <button class="btn naranja">Solicitar asesoría</button>
    </div>

  </nav>
</nav>

</body>
</html>
