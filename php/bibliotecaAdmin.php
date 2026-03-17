<?php
session_start();

?>

<img src="../uploads/<?php echo $_SESSION['foto'] ?? '..assets/default.png'; ?>" 
     alt="Perfil" class="avatar" onclick="location.href='../php/editar_perfil.php'">

<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Administrador - Nook Studio</title>

<link rel="stylesheet" href="../CSS/admin.css">


</head>

<body>

<header class="topbar">
<h1>Panel Administrador</h1>

<nav class="admin-info">
<button onclick="location.href='../index.php'">Cerrar Sesión</button>
</nav>

</header>

<div class="container">

<aside class="sidebar">

<h2>Nook Studio</h2>

<ul>

<li><a href="../php/planes.php" class="btn-menu">Planes</a></li>

<li>📊 Dashboard</li>

<li><a href="../admibtn/usuario.php" class="btn-menu">👥 Usuarios</a></li>

<li><a href="../admibtn/proyectos.php" class="btn-menu">📁 Proyectos</a></li>

<li><a href="../admibtn/materiales.php" class="btn-menu">🛋 Materiales</a></li>

<li><a href="../admibtn/configuracion.php" class="btn-menu">⚙ Configuración</a></li>

<li><a href="../index.php" class="btn-menu">Volver al Inicio</a></li>

</ul>

</aside>

<main class="main">

<section class="stats">

<div class="card">
<h3>Usuarios</h3>
</div>

<div class="card">
<h3>Proyectos</h3>
<p class="number">48</p>
</div>

<div class="card">
<h3>Ventas</h3>
<p class="number">$3,450</p>
</div>

<div class="card">
<h3>Mensajes</h3>
<p class="number">12</p>
</div>

</section>


<section class="table-section">

<h2>Planes de Membresía</h2>

<table>

<thead>

<tr>

<th>Nombre</th>
<th>Descripción</th>
<th>Precio</th>
<th>Actualizar</th>

</tr>

</thead>

<tbody>

<?php

$result = $conn->query("SELECT * FROM membresias");

if($result->num_rows > 0){

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?php echo htmlspecialchars($row['nombre']); ?></td>

<td><?php echo htmlspecialchars($row['descripcion']); ?></td>

<td>

<form method="POST" action="actualizar_precio.php">

<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<input 
type="number" 
step="0.01"
name="precio"
value="<?php echo $row['precio']; ?>"
required
>

<button type="submit">Actualizar</button>

</form>

</td>

<td>$<?php echo $row['precio']; ?></td>

</tr>

<?php

}

}

?>

</tbody>

</table>

</section>

</main>

</div>

</body>

</html>

<?php
$conn->close();
?>