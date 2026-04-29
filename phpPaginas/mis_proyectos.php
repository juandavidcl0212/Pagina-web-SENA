<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    die("Debes iniciar sesión para ver tus proyectos.");
}

$usuario_id = intval($_SESSION['id_usuario']); // usar la misma clave
$conn = new mysqli("localhost", "root", "", "prueba_db");

$result = $conn->query("SELECT * FROM proyectos WHERE usuario_id=$usuario_id ORDER BY fecha DESC");

while($row = $result->fetch_assoc()) {
    echo "<div class='proyecto-card'>";
    echo "<h3>".$row['nombre']."</h3>";
    echo "<img src='".$row['imagen']."' alt='Vista previa' style='width:200px; border:1px solid #ccc;'>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                <li><a href="../phpPaginas/biblioteca.php" class="btn-menu">Volver a la biblioteca</a></li>
                <li><a href="../html/inspiracion.html" class="btn-menu">Inspiración</a></li>
                <li><a href="../html/modelos_3d.html" class="btn-menu">Modelos 3D</a></li>
                <li><a href="../html/materiales.html" class="btn-menu">Materiales</a></li>
                <li><a href="../phpPaginas/cuenta.php" class="btn-menu">Cuenta</a></li>
            </ul>
        </aside>

        <main class="main">
            <div id="listaProyectos"></div>
            <h2>Mis Proyectos</h2>
<div class="lista-proyectos">
  <?php while($row = $result->fetch_assoc()) { ?>
    <div class="proyecto-card">
      <h3><?php echo $row['nombre']; ?></h3>
      <img src="<?php echo $row['imagen']; ?>" alt="Vista previa" style="width:200px; border:1px solid #ccc;">
    </div>
  <?php } ?>
</div>

<script>
window.onload = function() {
  let proyectos = JSON.parse(localStorage.getItem("proyectos")) || [];
  const contenedor = document.getElementById("listaProyectos");

  proyectos.forEach(p => {
    const div = document.createElement("div");
    div.classList.add("proyecto-card");
    div.innerHTML = `
      <h3>${p.nombre}</h3>
      <img src="${p.imagen}" alt="Vista previa" style="width:200px; border:1px solid #ccc;">
    `;
    contenedor.appendChild(div);
  });
};
</script>

        </main>

    </nav>
</body>
</html>