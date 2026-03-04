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
    <link rel="stylesheet" href="../styles.css">
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
        </ul>
    </aside>

    <main class="main">
        <section class="stats">
            <div class="card"><h3>Usuarios</h3></div>
            <div class="card"><h3>Proyectos</h3><p class="number">48</p></div>
            <div class="card"><h3>Ventas</h3><p class="number">$3,450</p></div>
            <div class="card"><h3>Mensajes</h3><p class="number">12</p></div>
        </section>

        <section class="table-section">
            <h2>Planes de Membresía</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th><th>Descripción</th><th>Precio</th><th>Actualizar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM membresias");
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['nombre']."</td>";
                        echo "<td>".$row['descripcion']."</td>";
                        echo "<td>
                                <form method='POST' action='actualizar_precio.php'>
                                    <input type='hidden' name='id' value='".$row['id']."'>
                                    <input type='text' name='precio' value='".$row['precio']."'>
                                    <button type='submit'>Actualizar</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>
