
     <?php session_start(); ?>
<section class="perfil">
  <h3>Editar Foto de Perfil</h3>

  <!-- Mostrar foto actual -->
  <img src="../uploads/<?php echo $_SESSION['foto'] ?? 'default.png'; ?>" 
       alt="Foto de perfil" class="avatar">

  <!-- Formulario para subir nueva foto -->
  <form method="POST" action="../php/subir_foto.php" enctype="multipart/form-data">
    <input type="file" name="foto" accept="image/*">
    <button type="submit">Actualizar Foto</button>
  </form>
</section>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nook Studio - Panel</title>
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
                <a href="../php/mis_proyectos.php" class="btn-menu"><li>Mis Proyectos</li></a>
                <a href="../html/inspiracion.html" class="btn-menu"><li>Inspiración</li></a>
                <a href="../html/modelos_3d.html" class="btn-menu"><li>Modelos 3D</li></a>
                <a href="../html/materiales.html" class="btn-menu"><li>Materiales</li></a>
                <a href="../html/cuenta.html" class="btn-menu"><li>Cuenta</li></a>
            </ul>
        </aside>

        <main class="main">

            <section class="perfil">
                <h3>Editar Perfil</h3>
              
                <!-- Mostrar foto actual -->
                <img src="../uploads/<?php echo $_SESSION['foto'] ?? 'default.png'; ?>" 
                     alt="Foto de perfil" class="avatar">
              
                <!-- Formulario para subir nueva foto -->
                <form method="POST" action="../php/subir_foto.php" enctype="multipart/form-data">
                  <label for="foto">Subir nueva foto:</label>
                  <input type="file" name="foto" id="foto" accept="image/*">
                  <button type="submit">Actualizar Foto</button>
                </form>
              </section>
              
            <h2>Bienvenido a tu panel</h2>
            <p>Explora ideas, diseña ambientes y administra tus proyectos.</p>

            <nav class="cards-container">
                <nav class="card">
                    <h3>Nuevo Proyecto</h3>
                    <p>Crea un diseño desde cero.</p>
                    <button onclick="location.href='../html/DISEÑO2D.html'">CREAR DISEÑO 3D</button>
                </nav>

                <nav class="card">
                    <h3>Inspiración</h3>
                    <p>Explora estilos y decoraciones.</p>
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
