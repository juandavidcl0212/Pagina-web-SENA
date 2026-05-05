<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    die('Acceso denegado');
}

$conn = new mysqli('localhost', 'root', '', 'prueba_db');
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$message_sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'], $_POST['mensaje'])) {
    $project_id = intval($_POST['project_id']);
    $mensaje = trim($_POST['mensaje']);

    if ($project_id <= 0 || $mensaje === '') {
        $error = 'Selecciona un proyecto y escribe un mensaje.';
    } else {
        $stmt = $conn->prepare('SELECT id, usuario_id FROM proyectos WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $project = $result->fetch_assoc();
        $stmt->close();

        if (!$project) {
            $error = 'Proyecto no encontrado.';
        } else {
            $usuario_id = intval($project['usuario_id']);
            $stmt = $conn->prepare('INSERT INTO project_messages (proyecto_id, usuario_id, autor, mensaje, creado) VALUES (?, ?, "admin", ?, NOW())');
            $stmt->bind_param('iis', $project_id, $usuario_id, $mensaje);
            $stmt->execute();
            $stmt->close();
            $message_sent = true;
        }
    }
}

$projects = $conn->query('SELECT p.id, p.nombre, u.nombre AS usuario FROM proyectos p JOIN usuarios u ON u.id = p.usuario_id ORDER BY p.fecha DESC');
$selected_project = null;
$messages = [];

function renderMessageHtml($mensaje) {
    $mensaje = htmlspecialchars($mensaje);
    $mensaje = preg_replace('/(uploads\/project_files\/[\w\-\.]+)/', '<a href="../$1" target="_blank">$1</a>', $mensaje);
    return nl2br($mensaje);
}

if ($project_id > 0) {
    $stmt = $conn->prepare('SELECT p.id, p.nombre, u.nombre AS usuario FROM proyectos p JOIN usuarios u ON u.id = p.usuario_id WHERE p.id = ? LIMIT 1');
    $stmt->bind_param('i', $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selected_project = $result->fetch_assoc();
    $stmt->close();

    if ($selected_project) {
        $stmt = $conn->prepare('SELECT autor, mensaje, creado FROM project_messages WHERE proyecto_id = ? ORDER BY creado ASC');
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
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
<title>Proyectos - Admin</title>
<link rel="stylesheet" href="../CSS/proyectos.css">
<link rel="stylesheet" href="../styles.css">
<style>
:root{
  --dark:#1B1F3B;
  --green:#136F63;
  --light:#EDF7F6;
  --purple:#9067C6;
  --orange:#FF9F1C;
}
body{
  font-family:Segoe UI, sans-serif;
  background:var(--light);
  margin:0;
  padding:0;
  color:#1B1F3B;
}
header{
  background:var(--dark);
  color:white;
  padding:20px;
  text-align:center;
  font-size:22px;
}
.container{
  max-width:1000px;
  margin:30px auto;
  padding:20px;
}
.grid-layout{
  display:grid;
  grid-template-columns:280px 1fr;
  gap:20px;
}
.sidebar-panel,
.chat-panel{
  background:white;
  border-radius:18px;
  box-shadow:0 10px 30px rgba(0,0,0,0.08);
  padding:20px;
}
.sidebar-panel h2,
.chat-panel h2{
  margin-top:0;
}
.project-item{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:14px 12px;
  border-radius:14px;
  border:1px solid rgba(0,0,0,0.08);
  margin-bottom:12px;
  transition:0.2s;
}
.project-item.selected{
  border-color:var(--purple);
  background:rgba(144,103,198,0.08);
}
.project-item a{
  color:#1B1F3B;
  background:var(--orange);
  border-radius:10px;
  padding:8px 12px;
  text-decoration:none;
  font-size:0.9rem;
}
.messages{
  display:flex;
  flex-direction:column;
  gap:14px;
  max-height:500px;
  overflow:auto;
  margin-bottom:20px;
}
.message{
  padding:16px;
  border-radius:18px;
  line-height:1.6;
}
.message.admin{
  background:rgba(144,103,198,0.12);
  border:1px solid rgba(144,103,198,0.3);
  align-self:flex-start;
}
.message.cliente{
  background:rgba(255,159,28,0.16);
  border:1px solid rgba(255,159,28,0.35);
  align-self:flex-end;
}
.message small{
  display:block;
  margin-top:10px;
  color:rgba(0,0,0,0.55);
}
textarea{
  width:100%;
  min-height:140px;
  border-radius:16px;
  border:1px solid rgba(0,0,0,0.12);
  padding:16px;
  font-family:Segoe UI, sans-serif;
}
button, .send-button{
  border:none;
  background:linear-gradient(135deg,var(--orange),var(--purple));
  color:white;
  padding:12px 18px;
  border-radius:14px;
  cursor:pointer;
}
.status{
  padding:12px 16px;
  border-radius:14px;
  margin-bottom:14px;
}
.status.success{background:rgba(46,125,50,0.12);border:1px solid rgba(46,125,50,0.25);}
.status.error{background:rgba(211,47,47,0.12);border:1px solid rgba(211,47,47,0.25);}
@media(max-width:900px){
  .grid-layout{grid-template-columns:1fr;}
}
</style>
</head>
<body>
<header>🛠️ Orientación de Proyectos - Administrador</header>
<div class="container">
  <div class="grid-layout">
    <aside class="sidebar-panel">
      <h2>Proyectos</h2>
      <?php while ($row = $projects->fetch_assoc()): ?>
        <div class="project-item <?php echo $project_id === intval($row['id']) ? 'selected' : ''; ?>">
          <div>
            <strong><?php echo htmlspecialchars($row['nombre']); ?></strong><br>
            <small>Cliente: <?php echo htmlspecialchars($row['usuario']); ?></small>
          </div>
          <a href="proyectos.php?project_id=<?php echo intval($row['id']); ?>">Abrir</a>
        </div>
      <?php endwhile; ?>
    </aside>
    <section class="chat-panel">
      <h2>Conversación</h2>
      <?php if ($message_sent): ?>
        <div class="status success">Tu mensaje fue enviado correctamente.</div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="status error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if (!$selected_project): ?>
        <p>Selecciona un proyecto del panel izquierdo para ver la conversación y responder al cliente.</p>
      <?php else: ?>
        <p><strong>Proyecto:</strong> <?php echo htmlspecialchars($selected_project['nombre']); ?> / <strong>Cliente:</strong> <?php echo htmlspecialchars($selected_project['usuario']); ?></p>
        <div class="messages">
          <?php if (count($messages) === 0): ?>
            <div class="message admin">No hay mensajes aún. Envía una orientación al cliente.</div>
          <?php endif; ?>
          <?php foreach ($messages as $message): ?>
            <div class="message <?php echo $message['autor'] === 'admin' ? 'admin' : 'cliente'; ?>">
              <?php echo renderMessageHtml($message['mensaje']); ?>
              <small><?php echo $message['autor'] === 'admin' ? 'Admin' : 'Cliente'; ?> · <?php echo date('d/m/Y H:i', strtotime($message['creado'])); ?></small>
            </div>
          <?php endforeach; ?>
        </div>
        <form method="post">
          <input type="hidden" name="project_id" value="<?php echo intval($selected_project['id']); ?>">
          <textarea name="mensaje" placeholder="Escribe tu orientación para el cliente..."></textarea>
          <button type="submit" class="send-button">Enviar orientación</button>
        </form>
      <?php endif; ?>
    </section>
  </div>
</div>
</body>
</html>
