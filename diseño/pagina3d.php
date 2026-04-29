<?php
session_start();
include("../conexion.php");

// 🔐 1. Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../phpPaginas/inSesion.php");
    exit;
}

$id = $_SESSION['id_usuario'];

// 🔎 2. Obtener datos del usuario
$sql = "SELECT prueba_usada, plan FROM usuarios WHERE id = '$id'";
$resultado = mysqli_query($conn, $sql);

if (!$resultado) {
    die("Error SQL: " . mysqli_error($conn));
}

$usuario = mysqli_fetch_assoc($resultado);

// 🔒 3. Lógica de acceso
// Si NO tiene plan premium y YA usó la prueba → bloqueado
if ($usuario['plan'] != 'Personal' && 
    $usuario['plan'] != 'Familiar' && 
    $usuario['plan'] != 'Institucional' && 
    $usuario['prueba_usada'] == 1) {

    header("Location: ../phpPaginas/planes.php");
    exit;
}

// 🎯 4. Marcar prueba como usada (solo si no tiene plan)
if ($usuario['plan'] == 'gratis' && $usuario['prueba_usada'] == 0) {
    mysqli_query($conn, "UPDATE usuarios SET prueba_usada = 1 WHERE id = '$id'");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plano 3D Interactivo</title>
    <!-- Librerías Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/DragControls.js"></script>
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../CSS/3d.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <!-- Barra lateral -->
    <nav class="sidebar">
        <h2>Festividades</h2>
        <nav class="categoria"><h3 onclick="toggleCategoria(this)">Navidad</h3><nav class="objetos" id="navidad"></nav></nav>
        <nav class="categoria"><h3 onclick="toggleCategoria(this)">Halloween</h3><nav class="objetos" id="halloween"></nav></nav>
        <nav class="categoria"><h3 onclick="toggleCategoria(this)">Cumpleaños</h3><nav class="objetos" id="cumple"></nav></nav>
        <nav class="categoria"><h3 onclick="toggleCategoria(this)">San Valentín</h3><nav class="objetos" id="sanvalentin"></nav></nav>
        <nav class="categoria"><h3 onclick="toggleCategoria(this)">Otros</h3><nav class="objetos" id="otros"></nav></nav>
        
        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
        <div style="text-align: center;">
            <label for="inputPlano" class="btn" style="display: block; cursor: pointer; background: #5e72e4; padding: 10px; border-radius: 8px;">📷 Subir Plano (Piso)</label>
            <input type="file" id="inputPlano" accept="image/*" style="display: none;">
        </div>
</nav>

    <!-- Área 3D -->
    <nav id="espacio3D"></nav>
    <a href="../phpPaginas/biblioteca.php" class="btn-volver">Volver</a>

    <script src="../js/3Deditor.js"></script>
</body>
</html>
