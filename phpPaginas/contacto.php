<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Contacto | Decoraciones de Interiores</title>
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../CSS/contacto.CSS">

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
<body>

  <section class="info">
    <h1>Contáctanos</h1>
    <p>
      ¿Tienes un proyecto en mente? Nos encantaría ayudarte a transformar tus espacios
      con diseño, estilo y personalidad.
    </p>

    <form class="form-contacto">
      <label>Nombre</label>
      <input type="text" placeholder="Tu nombre" required>
 

      <label>Correo electrónico</label>
      <input type="email" placeholder="tu@email.com" required>

      

      <label>Mensaje</label>
      <textarea placeholder="Cuéntanos sobre tu proyecto" rows="5" required></textarea>

      <button type="submit" class="btn naranja">Enviar mensaje</button>
    </form>
  </section>

  <section class="info">
    <h2>Nuestra Información</h2>
    <p><strong> Dirección:</strong> Av. Diseño Interior 123</p>
    <p><strong> Teléfono:</strong> +34 600 123 456</p>
    <p><strong>Email:</strong> contacto@decoraciones.com</p>

    <button class="btn morado">Solicitar Cotización</button>
  </section>

</body>
</html>
