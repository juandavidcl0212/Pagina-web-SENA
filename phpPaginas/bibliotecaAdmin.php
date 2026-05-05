<?php
session_start();
?>

<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "prueba_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
<title>Dashboard Administrador - Nooke Studio</title>
    <link rel="stylesheet" href="../CSS/admin.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header class="encabezado">
    <!-- Logo a la izquierda -->
    <img src="../assets/fb p.png" alt="Logo fantasy box" class="fantasy box">


    <nav class="cuenta">
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
              ? '../uploads/' . $_SESSION['foto'] 
              : '../assets/default.png'; ?>" 
             alt="Perfil" class="avatar" onclick="toggleMenu()">


        <!-- Ventana emergente -->
        <nav id="menuPopup" class="menu-popup">

          <button onclick="location.href='../phpPaginas/editar_perfil.php'">Editar Perfil</button>
          <button onclick="location.href='../phpFunciones/logout.php'">Cerrar Sesión</button>
  </nav>
      <?php endif; ?>
  </nav>

    <script src="../script.js"></script>
  </header>
        <nav class="container">
            <aside class="sidebar">
            <h2>Nook Studio</h2>
                <ul>
                    <li><a href="../phpPaginas/planes copy.php" class="btn-menu">📖 Administrar Planes</a></li>
                    <li><a href="../admibtn/usuario.php" class="btn-menu">👥 Usuarios</a></li>
                    <li><a href="../admibtn/proyectos.php" class="btn-menu">📁 Proyectos</a></li>
                    <li><a href="../admibtn/materiales.php" class="btn-menu">🛋 Materiales</a></li>
                    <li><a href="../admibtn/configuracion.php" class="btn-menu">⚙ Configuración</a></li>
                    <li><a href="../index.php" class="btn-menu">Volver al Inicio</a></li>
                </ul>
            </aside>
            <main class="main">
    <section class="table-section">
        <h2>Estadísticas</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuarios</th>
                    <th>Proyectos</th>
                    <th>Ventas</th>
                    <th>Mensajes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>120</td> <!-- aquí puedes poner dinámico con PHP -->
                    <td>48</td>
                    <td>$3,450</td>
                    <td>12</td>
                </tr>
            </tbody>
        </table>
    </section>

</main>
</nav>
</body>
</html>
<?php
$conn->close();
?>
