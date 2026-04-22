<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Contacto | Decoraciones</title>
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../CSS/contacto.css">
</head>

<body>

<!-- ENCABEZADO -->
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
          
        <!-- Botón para volver a la última página -->
        <?php if(isset($_SESSION['rol'])): ?>
  <?php if($_SESSION['rol'] === 'admin'): ?>
    <button onclick="location.href='../phpPaginas/bibliotecaAdmin.php'">Volver a la Biblioteca de Administrador</button>
  <?php else: ?>
    <button onclick="location.href='../phpPaginas/biblioteca.php'">Volver a la Biblioteca</button>
  <?php endif; ?>
<?php endif; ?>

          <button onclick="location.href='../phpPaginas/editar_perfil.php'">Editar Perfil</button>
          <button onclick="location.href='../phpFunciones/logout.php'">Cerrar Sesión</button>
        </div>
      <?php endif; ?>
    </nav>

    <script src="../script.js"></script>
  </header>

<section class="info">

  <h1>Contáctanos</h1>
  <p>Cuéntanos tu idea y te ayudamos a diseñar tu espacio ideal.</p>

  <form class="form-contacto" id="formContacto">

    <label>Nombre</label>
    <input type="text" id="nombre" required>

    <label>Correo electrónico</label>
    <input type="email" id="email" required>

    <label>Mensaje</label>
    <textarea id="mensaje" rows="5" required></textarea>

    <button type="submit" class="btn naranja" id="btnEnviar">
      Enviar mensaje
    </button>

    <p id="respuesta"></p>

  </form>

</section>

<section class="info">

  <h2>📍 Información</h2>

  <p>📌 Dirección: Av. Diseño Interior 123</p>
  <p>📞 Teléfono: +57 3153529606</p>
  <p>📧 Email: contacto@decoraciones.com</p>

  <button class="btn morado">Solicitar Cotización</button>

</section>

<!-- 💬 WHATSAPP -->
<a href="https://wa.me/34600123456" class="whatsapp" target="_blank">
  💬
</a>

<!-- AJAX -->
<script>
document.getElementById("formContacto").addEventListener("submit", function(e){
    e.preventDefault();

    let formData = new FormData();
    formData.append("nombre", document.getElementById("nombre").value);
    formData.append("email", document.getElementById("email").value);
    formData.append("mensaje", document.getElementById("mensaje").value);

    fetch("enviar_contacto.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {

        let r = document.getElementById("respuesta");

        if(data.trim() === "ok"){
            r.innerHTML = "✅ Mensaje enviado correctamente";
            r.style.color = "green";
            document.getElementById("formContacto").reset();
        } else {
            r.innerHTML = "❌ Error al enviar";
            r.style.color = "red";
        }
    });
});
</script>

</body>
</html>
