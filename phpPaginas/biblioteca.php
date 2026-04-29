<?php session_start();

?>
  </form>
</section>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nooke Studio - Panel</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>

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

          <button onclick="location.href='../phpPaginas/editar_perfil.php'">Editar Perfil</button>
          <button onclick="location.href='../phpFunciones/logout.php'">Cerrar Sesión</button>
        </div>
      <?php endif; ?>
    </nav>

    <script src="../script.js"></script>
  </header>

    <nav class="layout">

        <aside class="sidebar">
            <h2>Menú</h2>
            <ul>
                <li><a href="../phpPaginas/mis_proyectos.php" class="btn-menu">Mis Proyectos</a></li>
                <li><a href="../html/inspiracion.html" class="btn-menu">Inspiración</a></li>
                <li><a href="../html/modelos_3d.html" class="btn-menu">Modelos 3D</a></li>
                <li><a href="../html/materiales.html" class="btn-menu">Materiales</a></li>
                <li><a href="../phpPaginas/cuenta.php" class="btn-menu">Cuenta</a></li>
            </ul>
        </aside>

        <main class="main">
              
            <h2>Bienvenido a tu panel</h2>
            <p>Explora ideas, diseña ambientes y administra tus proyectos.</p>

            <nav class="cards-container">
                <nav class="card">
                    <h3>Nuevo Proyecto</h3>
                    <p>Crea un diseño desde cero.</p>
                    <button onclick="location.href='../html/DISEÑO2D.html'">CREAR DISEÑO 2D</button>
                </nav>

                <nav class="card">
                    <h3>Diseño 3d</h3>
                    <p>Explora estilos y decoraciones.</p>
                    <button onclick="location.href='../diseño/pagina3d.php'">CREAR DISEÑO 3D</button>
                </nav>

                <nav class="card">
                    <h3>Materiales</h3>
                    <p>Texturas, colores y objetos 3D.</p>
                </nav>
            </nav>
        </main>

    </nav>

</body>
</html>
