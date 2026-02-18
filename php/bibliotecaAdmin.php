<?php
session_start();
include("../conexion.php"); // Ajusta la ruta a tu conexión

// 🔒 Verificar que sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Obtener usuarios de la base de datos
$resultado = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");
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
    <div class="admin-info">
        <span><?php echo $_SESSION['nombre']; ?> (Admin)</span>
        <button onclick="location.href='../logout.php'">Cerrar Sesión</button>
    </div>
</header>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>Nook Studio</h2>
        <ul>
            <li>📊 Dashboard</li>
            <li>👥 Usuarios</li>
            <li>📁 Proyectos</li>
            <li>🛋 Materiales</li>
            <li>⚙ Configuración</li>
        </ul>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main">

        <!-- TARJETAS -->
        <section class="stats">
            <div class="card">
                <h3>Usuarios</h3>
                <p class="number"><?php echo $resultado->num_rows; ?></p>
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

        <!-- TABLA DE USUARIOS -->
        <section class="table-section">
            <h2>Últimos Usuarios Registrados</h2>

            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Plan</th>
                        <th>Último Login</th>
                        <th>Cambiar Plan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['plan']; ?></td>
                        <td><?php echo $row['ultimo_login']; ?></td>
                        <td>
                            <form method="POST" action="cambiar_plan.php">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <select name="plan">
                                    <option value="gratis" <?php if($row['plan']=='gratis') echo 'selected'; ?>>Gratis</option>
                                    <option value="premium" <?php if($row['plan']=='premium') echo 'selected'; ?>>Premium</option>
                                    <option value="pro" <?php if($row['plan']=='pro') echo 'selected'; ?>>Pro</option>
                                </select>
                                <button type="submit">Actualizar</button>
                                

                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

    </main>

</div>

</body>
</html>
