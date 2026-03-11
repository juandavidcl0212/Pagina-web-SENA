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
        <header class="top">
        <h1>Nooke Studio</h1>
        <p>Decoración · Interiores · Diseños 3D</p>
        <nav>
            <a href="../index.php" class="btn-menu">Volver al Inicio</a>
        </nav>
    </header>

    <nav class="layout">

        <aside class="sidebar">
            <h2>Menú</h2>
            <ul>
                <a href="../html/biblioteca.html" class="btn-menu"><li>General</li></a>
                <a href="../html/inspiracion.html" class="btn-menu"><li>Inspiración</li></a>
                <a href="../html/modelos_3d.html" class="btn-menu"><li>Modelos 3D</li></a>
                <a href="../html/materiales.html" class="btn-menu"><li>Materiales</li></a>
                <a href="../html/cuenta.html" class="btn-menu"><li>Cuenta</li></a>
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