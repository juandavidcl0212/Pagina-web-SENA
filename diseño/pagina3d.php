<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plano 3D Interactivo</title>
    <!-- Librerías Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/DragControls.js"></script>
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
</nav>

    <!-- Área 3D -->
    <nav id="espacio3D"></nav>
    <a href="../phpPaginas/biblioteca.php" class="btn-volver">Volver</a>

    <script src="../js/3Deditor.js"></script>
</body>
</html>
