<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: inSesion.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "prueba_db");
if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}

$usuario_id = intval($_SESSION['id_usuario']);

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $stmt->close();

    header("Location: modelos_3d.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, nombre, data, fecha FROM proyectos WHERE usuario_id = ? ORDER BY fecha DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$proyectos = [];
while ($row = $result->fetch_assoc()) {
    $data = json_decode($row['data'] ?? '{}', true);
    if (strtoupper($data['tipo'] ?? '') !== '3D') {
        continue;
    }

    $row['thumbnail'] = $data['thumbnail'] ?? '';
    $row['paredes'] = is_array($data['paredes'] ?? null) ? count($data['paredes']) : 0;
    $row['objetos'] = is_array($data['objetos'] ?? null) ? count($data['objetos']) : 0;
    $proyectos[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Proyectos 3D</title>
<link rel="stylesheet" href="../CSS/modelos3d.css">
<link rel="stylesheet" href="../styles.css">

</head>

<body>
<header>
  <div>
    <h1>Proyectos 3D</h1>
    <span class="meta">Tus ambientes guardados con paredes, colores y objetos.</span>
  </div>

  <div class="header-actions">
    <a class="volver" href="biblioteca.php">Biblioteca</a>
    <a class="crear" href="../diseño/pagina3d.php">Nuevo 3D</a>
    <input type="text" id="buscar" class="search" placeholder="Buscar proyecto...">
  </div>
</header>

<?php if (empty($proyectos)): ?>
  <div class="vacio">
    <h2>No tienes proyectos 3D todavia</h2>
    <p>Crea un ambiente, dibuja sus paredes en 2D y guardalo para verlo aqui.</p>
  </div>
<?php else: ?>
  <main class="grid">
    <?php foreach ($proyectos as $p): ?>
      <article class="card" data-nombre="<?php echo htmlspecialchars(strtolower($p['nombre']), ENT_QUOTES); ?>">
        <div class="preview">
          <?php if (!empty($p['thumbnail'])): ?>
            <img src="<?php echo htmlspecialchars($p['thumbnail'], ENT_QUOTES); ?>" alt="Vista previa de <?php echo htmlspecialchars($p['nombre']); ?>">
          <?php else: ?>
            <div class="preview-placeholder">Proyecto 3D</div>
          <?php endif; ?>
        </div>

        <h2><?php echo htmlspecialchars($p['nombre']); ?></h2>
        <p class="meta">
          <?php echo intval($p['paredes']); ?> paredes · <?php echo intval($p['objetos']); ?> objetos ·
          <?php echo date('d/m/Y', strtotime($p['fecha'])); ?>
        </p>

        <div class="actions">
          <a class="editar" href="../diseño/pagina3d.php?id=<?php echo intval($p['id']); ?>">Editar</a>
          <a class="mensaje" href="proyectos.php?project_id=<?php echo intval($p['id']); ?>">Mensaje admin</a>
          <a class="eliminar" href="modelos_3d.php?eliminar=<?php echo intval($p['id']); ?>" onclick="return confirm('¿Eliminar este proyecto 3D?')">Eliminar</a>
        </div>
      </article>
    <?php endforeach; ?>
  </main>
<?php endif; ?>

<script>
document.getElementById("buscar").addEventListener("input", function(){
  const filtro = this.value.toLowerCase().trim();
  document.querySelectorAll(".card").forEach(card => {
    card.style.display = card.dataset.nombre.includes(filtro) ? "block" : "none";
  });
});
</script>
</body>
</html>
