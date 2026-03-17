<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    die("Debes iniciar sesión para editar tu perfil.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil</title>
  <link rel="stylesheet" href="../CSS/dashboard.css">
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
    <img src="../uploads/<?php echo $_SESSION['foto'] ?? 'default.png'; ?>" 
         alt="Foto actual" class="avatar">

    <form method="POST" action="subir_foto.php" enctype="multipart/form-data">
      <input type="file" name="foto" accept="image/*" required>
      <button type="submit">Actualizar Foto</button>
    </form>
    <br>
    <a href="biblioteca.php">Volver a Biblioteca</a>
    <a href="bibliotecaAdmin.php">Volver a Biblioteca Admin</a>
  </div>
</body>
</html>
