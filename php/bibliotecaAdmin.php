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
        <li><a href="../html/planes.html" class="btn-menu">Planes</a></li>
            <li>📊 Dashboard</li>
            <li>👥 Usuarios</li>
            <li>📁 Proyectos</li>
            <li>🛋 Materiales</li>
            <li>⚙ Configuración</li>
        </ul>
    </aside>

    <main class="main">
        <section class="stats">
            <div class="card">
                <h3>Usuarios</h3>
              
            </div>
            <div class="card"><h3>Proyectos</h3><p class="number">48</p></div>
            <div class="card"><h3>Ventas</h3><p class="number">$3,450</p></div>
            <div class="card"><h3>Mensajes</h3><p class="number">12</p></div>
        </section>

        <section class="table-section">
            <h2>Últimos Usuarios Registrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th><th>Email</th><th>Plan</th><th>Último Login</th><th>Cambiar Plan</th>
                    </tr>
                </thead>
              
            </table>
        </section>
    </main>
</div>
</body>
</html>
