<?php

session_start();

/* =========================
   CONEXIÓN
========================= */
$conn = new mysqli('localhost', 'root', '', 'prueba_db');

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

/* =========================
   SOLO ADMIN
========================= */
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    die('Acceso denegado');
}

$usuario_id = $_SESSION['id_usuario'];

/* =========================
   ELIMINAR MENSAJE
========================= */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $project_id_del = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

    $stmt = $conn->prepare("UPDATE project_messages SET eliminado = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: proyectos.php?project_id=" . $project_id_del);
    exit();
}

/* =========================
   VARIABLES
========================= */
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$message_sent = false;
$error = '';

$selected_project = null;
$messages = [];

/* =========================
   ENVIAR MENSAJE ADMIN
========================= */
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
        } else {
            $error = "Proyecto no encontrado";
        }
    }
}

/* =========================
   LISTA PROYECTOS
========================= */
$projects = $conn->query("
    SELECT p.id, p.nombre, u.nombre AS usuario
    FROM proyectos p
    JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.fecha DESC
");

/* =========================
   PROYECTO SELECCIONADO (CORREGIDO)
========================= */
if ($project_id > 0) {

    $stmt = $conn->prepare("SELECT id, nombre, usuario_id FROM proyectos WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $selected_project = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($selected_project) {

        $stmt = $conn->prepare("
            SELECT id, autor, mensaje, archivo, creado
            FROM project_messages
            WHERE proyecto_id = ? AND eliminado = 0
            ORDER BY creado ASC
        ");

        $stmt->bind_param("i", $project_id);
        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        $stmt->close();
    }
    INSERT INTO project_messages 
(proyecto_id, usuario_id, autor, mensaje, creado, visto)
VALUES (?, ?, ?, ?, NOW(), 0)
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin Proyectos PRO</title>

<style>
body{
  margin:0;
  font-family:'Segoe UI', sans-serif;
  background:#1B1F3B;
  color:#EDF7F6;
}

header{
  background:#11152a;
  padding:18px;
  text-align:center;
  font-size:20px;
  font-weight:bold;
  border-bottom:2px solid rgba(255,255,255,0.05);
}

.container{
  display:grid;
  grid-template-columns:280px 1fr;
  gap:16px;
  padding:16px;
}

.panel{
  background:#11152a;
  border-radius:16px;
  padding:16px;
  border:1px solid rgba(255,255,255,0.05);
}

button{
  background:#FF9F1C;
  color:#1B1F3B;
  border:none;
  padding:10px 14px;
  border-radius:10px;
  font-weight:bold;
  cursor:pointer;
}

.chat-area{
  height:520px;
  overflow-y:auto;
  padding:12px;
  border-radius:16px;
  background:#141a33;
  border:1px solid rgba(255,255,255,0.05);
  display:flex;
  flex-direction:column;
  gap:12px;
}

.msg{
  max-width:70%;
  padding:12px;
  border-radius:14px;
}

.msg.cliente{background:#136F63; align-self:flex-end;}
.msg.admin{background:#9067C6; align-self:flex-start;}

.msg-actions{
  margin-top:8px;
  text-align:right;
}

.msg-actions a{
  color:#ff5c5c;
  font-size:11px;
  text-decoration:none;
}

.project{
  background:#141a33;
  padding:12px;
  border-radius:12px;
  margin-bottom:10px;
}

.project a{
  display:inline-block;
  margin-top:8px;
  padding:6px 12px;
  border-radius:8px;
  background:#FF9F1C;
  color:#1B1F3B;
  text-decoration:none;
}
.chat-area{
  height:520px;
  display:flex;
  flex-direction:column;
}

form{
  margin-top:auto;
}


</style>
</head>

<body>

<header>🛠️ Panel PRO - Asesoría de Proyectos</header>

<div class="container">

<!-- PROYECTOS -->
<div class="panel">
<h3>Proyectos</h3>

<?php while ($p = $projects->fetch_assoc()): ?>
<div class="project">

<strong><?php echo htmlspecialchars($p['nombre']); ?></strong><br>
Cliente: <?php echo htmlspecialchars($p['usuario']); ?><br>

<a href="proyectos.php?project_id=<?php echo $p['id']; ?>">
Abrir
</a>

</div>
<?php endwhile; ?>

</div>

<!-- CHAT -->
<div class="panel">

<h3>Conversación</h3>

<?php if ($message_sent): ?>
<p style="color:#136F63;">✔ Mensaje enviado</p>
<?php endif; ?>

<?php if ($error): ?>
<p style="color:#FF9F1C;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if (!$selected_project): ?>

<p>Selecciona un proyecto</p>

<?php else: ?>

<h4>
<?php echo htmlspecialchars($selected_project['nombre'] ?? 'Sin nombre'); ?>
</h4>

<div class="chat-area">

<?php if (empty($messages)): ?>
<p>No hay mensajes</p>
<?php else: ?>

<?php foreach ($messages as $m): ?>

<div class="msg <?php echo $m['autor']; ?>">

<?php echo nl2br(htmlspecialchars($m['mensaje'])); ?>

<small><?php echo $m['autor']; ?> · <?php echo $m['creado']; ?></small>

<div class="msg-actions">
    <a href="proyectos.php?delete=<?php echo $m['id']; ?>&project_id=<?php echo $selected_project['id']; ?>"
       onclick="return confirm('¿Eliminar este mensaje?')">
        🗑 Eliminar
    </a>
</div>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

<form method="POST">
<input type="hidden" name="project_id" value="<?php echo $selected_project['id']; ?>">
<textarea name="mensaje" required
style="
width:100%;
min-height:180px;
max-height:300px;
padding:14px;
border-radius:12px;
background:#1E2448;
color:#EDF7F6;
border:1px solid rgba(255,255,255,0.05);
resize:vertical;
outline:none;
font-size:14px;
line-height:1.4;
"
placeholder="Escribe tu respuesta..."></textarea>
<button type="submit">Enviar</button>
</form>

<?php endif; ?>

</div>

</div>

</body>
</html>