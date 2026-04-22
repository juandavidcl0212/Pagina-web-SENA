<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id_usuario'];

$user = mysqli_fetch_assoc(
    mysqli_query($enlace, "SELECT * FROM usuarios WHERE id = $id")
);

/* 🔐 Cambiar contraseña */
if (isset($_POST['cambiar_pass'])) {
    $nueva = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($enlace, "UPDATE usuarios SET password='$nueva' WHERE id=$id");
    $msg = "✅ Contraseña actualizada";
}

/* 🚪 Logout */
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Configuración</title>

<style>
:root{
  --dark:#1B1F3B;
  --green:#136F63;
  --light:#EDF7F6;
  --purple:#9067C6;
  --orange:#FF9F1C;
}

body{
  margin:0;
  font-family:Segoe UI;
  background:var(--light);
}

/* HEADER */
.header{
  background:var(--dark);
  color:white;
  padding:20px;
  text-align:center;
}

/* CONTENEDOR */
.container{
  max-width:900px;
  margin:30px auto;
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:20px;
}

/* CARD */
.card{
  background:white;
  padding:20px;
  border-radius:15px;
  box-shadow:0 10px 20px rgba(0,0,0,0.1);
  background-color: #1b1f3b;
}

/* PERFIL */
.profile{
  text-align:center;
}

.avatar{
  width:80px;
  height:80px;
  background:var(--purple);
  border-radius:50%;
  margin:auto;
  display:flex;
  align-items:center;
  justify-content:center;
  color:white;
  font-size:30px;
}

/* INPUTS */
input{
  width:100%;
  padding:10px;
  margin-top:10px;
  border-radius:8px;
  border:1px solid #ccc;
}

/* BOTONES */
button{
  width:100%;
  padding:10px;
  margin-top:10px;
  border:none;
  border-radius:8px;
  cursor:pointer;
  color:white;
  font-weight:bold;
}
.btn-menu{
  align-self: left;
  width: auto;
}

.btn-green{ background:var(--green); }
.btn-orange{ background:var(--orange); }
.btn-dark{ background:var(--dark); }

/* TEXTO */
small{
  color:gray;
}

.msg{
  color:var(--green);
  text-align:center;
}
</style>
<link rel="stylesheet" href="../styles.css">
</head>
<body>

<nav class="header">
  <h1>⚙️ Configuración del Sistema</h1>
</nav>
<button class="btn-menu" onclick="location.href='../phpPaginas/bibliotecaAdmin.php'">Volver</button>
<nav class="container">

  <!-- 👤 PERFIL -->
  <div class="card profile">
    <div class="avatar">
      <?= strtoupper($user['nombre'][0]) ?>
    </div>

    <h2><?= htmlspecialchars($user['nombre']) ?></h2>
    <small><?= htmlspecialchars($user['email']) ?></small>

    <p><strong>Rol:</strong> <?= $user['rol'] ?></p>
  </div>

  <!-- 🔐 CONTRASEÑA -->
  <div class="card">
    <h3>🔐 Seguridad</h3>

    <?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

    <form method="POST">
      <input type="password" name="password" placeholder="Nueva contraseña" required>
      <button class="btn-green" name="cambiar_pass">
        Actualizar contraseña
      </button>
    </form>
  </div>

  <!-- 🚪 ACCIONES -->
  <div class="card">
    <h3>🚪 Sesión</h3>

    <form method="POST">
      <button name="logout">
        Cerrar sesión
      </button>
    </form>
  </div>

  <!-- 🎨 INFO EXTRA -->
  <div class="card">
    <h3>📊 Sistema</h3>
    <p>Panel de administración de decoración de interiores</p>
    <p>Estado: 🟢 Activo</p>
  </div>

</nav>

</body>
</html>