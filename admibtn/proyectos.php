<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'prueba_db');
if ($conn->connect_error) {
    die('Error de conexion: ' . $conn->connect_error);
}

if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    die('Acceso denegado');
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $project_id_del = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

    $stmt = $conn->prepare("UPDATE project_messages SET eliminado = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: proyectos.php?project_id=" . $project_id_del);
    exit;
}

$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$tipo_filtro = strtoupper($_GET['tipo'] ?? 'TODOS');
if (!in_array($tipo_filtro, ['TODOS', '2D', '3D'], true)) {
    $tipo_filtro = 'TODOS';
}

$message_sent = false;
$error = '';
$selected_project = null;
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'], $_POST['mensaje'])) {
    $project_id_post = intval($_POST['project_id']);
    $mensaje = trim($_POST['mensaje']);

    if ($project_id_post <= 0 || $mensaje === '') {
        $error = "Debes seleccionar proyecto y escribir mensaje.";
    } else {
        $stmt = $conn->prepare("SELECT id, usuario_id FROM proyectos WHERE id = ?");
        $stmt->bind_param("i", $project_id_post);
        $stmt->execute();
        $project = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($project) {
            $autor = "admin";

            $stmt = $conn->prepare("
                INSERT INTO project_messages (proyecto_id, usuario_id, autor, mensaje, creado)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("iiss", $project_id_post, $project['usuario_id'], $autor, $mensaje);
            $stmt->execute();
            $stmt->close();

            $message_sent = true;
            $project_id = $project_id_post;
        } else {
            $error = "Proyecto no encontrado";
        }
    }
}

$result = $conn->query("
    SELECT p.id, p.nombre, p.data, p.fecha, u.nombre AS usuario, u.apellido
    FROM proyectos p
    JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.fecha DESC
");

$projects = [];
while ($row = $result->fetch_assoc()) {
    $data = json_decode($row['data'] ?? '{}', true);
    $row['tipo'] = strtoupper($data['tipo'] ?? '2D');
    $row['thumbnail'] = $data['thumbnail'] ?? '';

    if ($tipo_filtro !== 'TODOS' && $row['tipo'] !== $tipo_filtro) {
        continue;
    }

    $projects[] = $row;
}

if ($project_id > 0) {
    $stmt = $conn->prepare("
        SELECT p.id, p.nombre, p.usuario_id, p.data, u.nombre AS usuario, u.apellido
        FROM proyectos p
        JOIN usuarios u ON u.id = p.usuario_id
        WHERE p.id = ?
    ");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $selected_project = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($selected_project) {
        $data = json_decode($selected_project['data'] ?? '{}', true);
        $selected_project['tipo'] = strtoupper($data['tipo'] ?? '2D');

        $stmt = $conn->prepare("
            SELECT id, autor, mensaje, creado
            FROM project_messages
            WHERE proyecto_id = ?
            ORDER BY creado ASC
        ");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $message_result = $stmt->get_result();

        while ($row = $message_result->fetch_assoc()) {
            $messages[] = $row;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Proyectos</title>

<style>
:root{
  --dark:#1B1F3B;
  --panel:#11152a;
  --surface:#141a33;
  --green:#136F63;
  --purple:#9067C6;
  --orange:#FF9F1C;
  --light:#EDF7F6;
}

*{box-sizing:border-box}
body{
  margin:0;
  font-family:'Segoe UI', Arial, sans-serif;
  background:var(--dark);
  color:var(--light);
}

header{
  background:var(--panel);
  padding:18px 22px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:18px;
  border-bottom:1px solid rgba(255,255,255,0.08);
}

header h1{
  margin:0;
  font-size:22px;
}

.back{
  color:var(--light);
  text-decoration:none;
  background:#2a305c;
  padding:10px 14px;
  border-radius:8px;
  font-weight:700;
}

.container{
  display:grid;
  grid-template-columns:340px 1fr;
  gap:16px;
  padding:16px;
}

.panel{
  background:var(--panel);
  border-radius:8px;
  padding:16px;
  border:1px solid rgba(255,255,255,0.08);
}

.filters{
  display:grid;
  grid-template-columns:1fr 1fr 1fr;
  gap:8px;
  margin-bottom:14px;
}

.filters a{
  text-align:center;
  text-decoration:none;
  color:var(--light);
  background:#1E2448;
  padding:9px;
  border-radius:7px;
  font-weight:700;
  font-size:13px;
}

.filters a.active{
  background:var(--orange);
  color:#11152a;
}

.project{
  background:var(--surface);
  padding:12px;
  border-radius:8px;
  margin-bottom:10px;
  border:1px solid rgba(255,255,255,0.06);
}

.project strong{display:block;margin-bottom:4px}
.project small{color:#b9c4d0}

.badge{
  display:inline-block;
  margin-top:8px;
  margin-right:6px;
  padding:4px 8px;
  border-radius:999px;
  background:var(--purple);
  font-size:12px;
  font-weight:700;
}

.project a{
  display:inline-block;
  margin-top:10px;
  padding:7px 12px;
  border-radius:7px;
  background:var(--orange);
  color:#11152a;
  text-decoration:none;
  font-weight:700;
}

.chat-area{
  min-height:360px;
  max-height:520px;
  overflow-y:auto;
  padding:12px;
  border-radius:8px;
  background:var(--surface);
  border:1px solid rgba(255,255,255,0.06);
  display:flex;
  flex-direction:column;
  gap:12px;
}

.msg{
  max-width:72%;
  padding:12px;
  border-radius:8px;
  line-height:1.4;
}

.msg.cliente,.msg.usuario{background:var(--green); align-self:flex-end;}
.msg.admin{background:var(--purple); align-self:flex-start;}
.msg small{display:block;margin-top:8px;color:rgba(255,255,255,0.72);font-size:11px}

form{margin-top:12px}
textarea{
  width:100%;
  min-height:150px;
  padding:14px;
  border-radius:8px;
  background:#1E2448;
  color:var(--light);
  border:1px solid rgba(255,255,255,0.08);
  resize:vertical;
  outline:none;
  font-size:14px;
}

button{
  margin-top:8px;
  background:var(--orange);
  color:#11152a;
  border:none;
  padding:10px 14px;
  border-radius:8px;
  font-weight:700;
  cursor:pointer;
}

.status{font-weight:700}
.success{color:#7ee0c1}
.error{color:var(--orange)}

@media(max-width:860px){
  header{align-items:flex-start;flex-direction:column}
  .container{grid-template-columns:1fr}
}
</style>
</head>

<body>
<header>
  <h1>Panel admin - Proyectos</h1>
  <a class="back" href="../phpPaginas/bibliotecaAdmin.php">Volver a Biblioteca</a>
</header>

<main class="container">
  <aside class="panel">
    <h2>Proyectos</h2>

    <div class="filters">
      <a class="<?php echo $tipo_filtro === 'TODOS' ? 'active' : ''; ?>" href="proyectos.php">Todos</a>
      <a class="<?php echo $tipo_filtro === '2D' ? 'active' : ''; ?>" href="proyectos.php?tipo=2D">Proyecto 2D</a>
      <a class="<?php echo $tipo_filtro === '3D' ? 'active' : ''; ?>" href="proyectos.php?tipo=3D">Proyecto 3D</a>
    </div>

    <?php if (empty($projects)): ?>
      <p>No hay proyectos en esta categoria.</p>
    <?php endif; ?>

    <?php foreach ($projects as $p): ?>
      <div class="project">
        <strong><?php echo htmlspecialchars($p['nombre']); ?></strong>
        <small>Cliente: <?php echo htmlspecialchars(trim($p['usuario'] . ' ' . $p['apellido'])); ?></small><br>
        <span class="badge">Proyecto <?php echo htmlspecialchars($p['tipo']); ?></span>
        <a href="proyectos.php?project_id=<?php echo intval($p['id']); ?>&tipo=<?php echo urlencode($tipo_filtro); ?>">Abrir</a>
      </div>
    <?php endforeach; ?>
  </aside>

  <section class="panel">
    <h2>Conversacion</h2>

    <?php if ($message_sent): ?>
      <p class="status success">Mensaje enviado.</p>
    <?php endif; ?>

    <?php if ($error): ?>
      <p class="status error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!$selected_project): ?>
      <p>Selecciona un proyecto para revisar sus mensajes y responder al cliente.</p>
    <?php else: ?>
      <h3>
        <?php echo htmlspecialchars($selected_project['nombre']); ?>
        <span class="badge">Proyecto <?php echo htmlspecialchars($selected_project['tipo']); ?></span>
      </h3>

      <div class="chat-area">
        <?php if (empty($messages)): ?>
          <p>No hay mensajes todavia.</p>
        <?php else: ?>
          <?php foreach ($messages as $m): ?>
            <div class="msg <?php echo htmlspecialchars($m['autor']); ?>">
              <?php echo nl2br(htmlspecialchars($m['mensaje'])); ?>
              <small><?php echo htmlspecialchars($m['autor']); ?> · <?php echo htmlspecialchars($m['creado']); ?></small>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <form method="POST">
        <input type="hidden" name="project_id" value="<?php echo intval($selected_project['id']); ?>">
        <textarea name="mensaje" placeholder="Escribe tu respuesta..." required></textarea>
        <button type="submit">Enviar respuesta</button>
      </form>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
