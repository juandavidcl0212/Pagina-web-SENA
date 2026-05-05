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

 
  <style>
 body {
    margin: 0;
    font-family: Segoe UI, sans-serif;
    background: linear-gradient(135deg, #1B1F3B, #9067C6);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* CONTENEDOR PRINCIPAL */
.perfil-container {
    width: 400px;
    padding: 30px;
    border-radius: 20px;
    text-align: center;

    background: #EDF7F6; /* 🔥 fondo claro */
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);

    animation: aparecer 0.6s ease;
}

/* ANIMACIÓN */
@keyframes aparecer {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* TITULO */
.perfil-container h2 {
    color: #1B1F3B;
    margin-bottom: 20px;
}

/* FOTO PERFIL */
.avatar {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;

    border: 4px solid #FF9F1C;
    box-shadow: 0 0 20px rgba(255,159,28,0.5);

    margin-bottom: 20px;
}

/* INPUT FILE */
.perfil-container input[type="file"] {
    margin: 10px 0;
    color: #1B1F3B;
}

/* BOTON ACTUALIZAR */
.perfil-container button {
    background: linear-gradient(135deg, #FF9F1C, #d4d007);
    color: white;
    border: none;
    padding: 12px 18px;
    border-radius: 12px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.perfil-container button:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* BOTON VOLVER */
.btn-menu {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 18px;
    background: #136F63;
    color: white;
    border-radius: 12px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.btn-menu:hover {
    background: #0f5a50;
    transform: scale(1.05);
}
</style>

</head>
<body>
  <body>

  <div class="perfil-container">

    <h2>Editar Foto de Perfil</h2>

    <!-- FOTO -->
    <img id="previewImg" src="../uploads/<?php echo $fotoPerfil; ?>" class="avatar">

    <!-- FORM -->
    <form method="POST" action="../phpFunciones/subir_foto.php" enctype="multipart/form-data">

      <input type="file" id="inputFoto" name="foto" accept="image/*">

      <button type="submit" class="btn-actualizar">
        Actualizar Foto
      </button>

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