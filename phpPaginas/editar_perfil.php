<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    die("Debes iniciar sesión para editar tu perfil.");
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "prueba_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id = $_SESSION['id_usuario'];

// Consultar la foto guardada en la BD
$result = $conn->query("SELECT foto FROM usuarios WHERE id=$id");
$usuario = $result->fetch_assoc();

// Si existe foto en BD, usarla; si no, usar default.png
$fotoPerfil = ($usuario && $usuario['foto'] != '') ? $usuario['foto'] : 'default.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil</title>
  <link rel="stylesheet" href="../CSS/dashboard.css">
  <link rel="stylesheet" href="../styles.css">
  <style>
    .perfil-container {
      max-width: 400px;
      margin: 50px auto;
      padding: 20px;
      background: #f5f5f5;
      border-radius: 10px;
      text-align: center;
    }
    .avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="perfil-container">
    <h2>Editar Foto de Perfil</h2>
    <!-- Mostrar foto actual desde carpeta uploads -->
    <img src="../uploads/<?php echo $fotoPerfil; ?>" alt="Foto actual" class="avatar">

    <!-- Formulario para subir nueva foto -->
    <form method="POST" action="../phpFunciones/subir_foto.php" enctype="multipart/form-data">
      <input type="file" name="foto" accept="image/*">
      <button type="submit">Actualizar Foto</button>
    </form>
    <br>

    <!-- Botón de volver según rol -->
    <?php if(isset($_SESSION['rol'])): ?>
      <?php if($_SESSION['rol'] === 'admin'): ?>
        <a href="bibliotecaAdmin.php" class="btn-menu">Volver a Biblioteca Admin</a>
      <?php else: ?>
        <a href="biblioteca.php" class="btn-menu">Volver a Biblioteca</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</body>
</html>
.//