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
    <!-- Mostrar foto actual -->
    <img src="../assets/<?php echo (isset($_SESSION['foto']) && $_SESSION['foto'] !== '')  
      ? $_SESSION['foto']
       : 'default.png'; ?>" 
        alt="Foto actual" class="avatar">

   <!-- Ajustar la ruta del action -->
   <form method="POST" action="../phpFunciones/subir_foto.php" enctype="multipart/form-data">
     <input type="file" name="foto" accept="image/*">
     <button type="submit">Actualizar Foto</button>
   </form>
   <br>
   
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
../