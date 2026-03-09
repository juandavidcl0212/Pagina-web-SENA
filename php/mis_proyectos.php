<?php
$conn = new mysqli("localhost", "root", "", "prueba_db");
$result = $conn->query("SELECT * FROM proyectos WHERE usuario_id=1 ORDER BY fecha DESC");
?>
<h2>Mis Proyectos Recientes</h2>
<ul>
  <?php while($row = $result->fetch_assoc()) { ?>
    <li>
      <?php echo $row['nombre']; ?> - <?php echo $row['fecha']; ?>
      <button onclick="location.href='cargar_proyecto.php?id=<?php echo $row['id']; ?>'">Abrir</button>
    </li>
  <?php } ?>
</ul>

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
            
        </main>

    </nav>
</body>
</html>