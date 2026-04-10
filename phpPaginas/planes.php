<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta de planes ordenados por id
$result = $conn->query("SELECT * FROM membresias ORDER BY id ASC");

// Guardar en array asociativo por ID
$planes = [];
while($row = $result->fetch_assoc()){
    $planes[$row['id']] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planes</title>
    <link rel="stylesheet" href="../CSS/style planes.css">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>

<header class="encabezado">
    <img src="../assets/fb p.png" alt="Logo fantasy box" class="fantasy box">

    <nav class="menu">
        <a href="../index.php" class="btn-menu">Inicio</a>
        <a href="../phpPaginas/servicios.php" class="btn-menu">Servicios</a>
        <a href="../phpPaginas/planes.php" class="btn-menu">Planes</a>
        <a href="../phpPaginas/contacto.php" class="btn-menu">Contacto</a>
    </nav>

    <div class="cuenta">
        <?php if(!isset($_SESSION['id_usuario'])): ?>
            <nav class="cuentas">
                <a href="../phpPaginas/Registrarse.php" class="btn-menu">¡Crear una cuenta!</a>
            </nav>
            <nav>
                <a href="../phpPaginas/inSesion.php" class="btn-menu">¿Ya tienes cuenta? Inicia sesión</a>
            </nav>
        <?php else: ?>
            <img src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] !== '' 
                ? '../uploads/' . $_SESSION['foto'] 
                : '../assets/default.png'; ?>" 
                alt="Perfil" class="avatar" onclick="toggleMenu()">

            <div id="menuPopup" class="menu-popup">
                <?php if(isset($_SESSION['rol'])): ?>
                    <?php if($_SESSION['rol'] === 'admin'): ?>
                        <button onclick="location.href='../phpPaginas/bibliotecaAdmin.php'">Volver a Biblioteca Administrador</button>
                    <?php else: ?>
                        <button onclick="location.href='../phpPaginas/biblioteca.php'">Volver a Biblioteca</button>
                    <?php endif; ?>
                <?php endif; ?>

                <button onclick="location.href='../phpPaginas/editar_perfil.php'">Editar Perfil</button>
                <button onclick="location.href='../phpFunciones/logout.php'">Cerrar Sesión</button>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
    <div style="text-align:center; margin:20px;">
        <a href="../phpPaginas/planes copy.php">
            <button style="padding:10px 15px; background:#1f7a6b; color:white; border:none; border-radius:5px; cursor:pointer;">
                regresar a planes (Admin)
            </button>
        </a>
    </div>
<?php endif; ?>
    </div>
</header>

<h1 style="text-align:center;">PLANES</h1>

<div class="planes">

    <!-- PLAN PERSONAL -->
    <div class="plan1">
        <h2>Personal</h2>
        <div class="precio1">
            <h3>$<?php echo isset($planes[1]) ? $planes[1]['precio'] : '0'; ?></h3>
            <p>/mes</p>
        </div>
        <ul>
            <li>Consulta de diseño de interiores.</li>
            <li>1 habitación/sala decorada</li>
            <li>Soporte por e-mail</li>
        </ul>
        <button class="plan1B">COMPRAR</button>
    </div>

    <!-- PLAN FAMILIAR -->
    <div class="plan2">
        <h2>Familiar</h2>
        <div class="precio2">
            <h3>$<?php echo isset($planes[2]) ? $planes[2]['precio'] : '0'; ?></h3>
            <p>/mes</p>
        </div>
        <ul>
            <li>Consulta de diseño de interiores.</li>
            <li>3 habitaciones/salas decoradas</li>
            <li>Soporte diario</li>
        </ul>
        <button class="plan2B">COMPRAR</button>
    </div>

    <!-- PLAN INSTITUCIONAL -->
    <div class="plan3">
        <h2>Institucional</h2>
        <div class="precio3">
            <h3>$<?php echo isset($planes[3]) ? $planes[3]['precio'] : '0'; ?></h3>
            <p>/mes</p>
        </div>
        <ul>
            <li>Consulta de diseño de interiores.</li>
            <li>12 habitaciones/salas decoradas</li>
            <li>Soporte dedicado</li>
        </ul>
        <button class="plan3B">COMPRAR</button>
    </div>

</div>

<script src="../script.js"></script>
</body>
</html>