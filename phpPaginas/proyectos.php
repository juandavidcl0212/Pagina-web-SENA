<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: inSesion.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'prueba_db');
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

$usuario_id = intval($_SESSION['id_usuario']);
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

$mensaje_enviado = false;
$error = '';

$messages = [];
$selected_project = null;

/* =========================
   ENVIAR MENSAJE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'], $_POST['mensaje'])) {

    $project_id = intval($_POST['project_id']);
    $mensaje = trim($_POST['mensaje']);

    if ($project_id <= 0 || $mensaje === '') {
        $error = 'Debe seleccionar un proyecto y escribir un mensaje.';
    } else {

        $stmt = $conn->prepare('SELECT id FROM proyectos WHERE id = ? AND usuario_id = ? LIMIT 1');
        $stmt->bind_param('ii', $project_id, $usuario_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $error = 'Proyecto no encontrado o sin permisos.';
        } else {
            $stmt->close();

            $stmt = $conn->prepare('INSERT INTO project_messages (proyecto_id, usuario_id, autor, mensaje, creado) VALUES (?, ?, "cliente", ?, NOW())');
            $stmt->bind_param('iis', $project_id, $usuario_id, $mensaje);
            $stmt->execute();
            $stmt->close();

            $mensaje_enviado = true;
        }
    }
}

/* =========================
   LISTAR PROYECTOS
========================= */
$projects = $conn->query("SELECT id, nombre, data FROM proyectos WHERE usuario_id = $usuario_id ORDER BY fecha DESC");

/* =========================
   PROYECTO SELECCIONADO + MENSAJES
========================= */
if ($project_id > 0) {

    $stmt = $conn->prepare('SELECT id, nombre, data FROM proyectos WHERE id = ? AND usuario_id = ?');
    $stmt->bind_param('ii', $project_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selected_project = $result->fetch_assoc();
    $stmt->close();

    if ($selected_project) {
        $selected_data = json_decode($selected_project['data'] ?? '{}', true);
        $selected_project['tipo'] = strtoupper($selected_data['tipo'] ?? '2D');
        $selected_project['thumbnail'] = $selected_data['thumbnail'] ?? '';

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

$back_url = ($selected_project && ($selected_project['tipo'] ?? '') === '3D') ? 'modelos_3d.php' : 'mis_proyectos.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orientación de Proyectos</title>

   


<body>
     <!-- ✅ RUTA CORRECTA SEGÚN TU ESTRUCTURA -->
    <link rel="stylesheet" href="../CSS/proyectos_admin.css">
</head>
<div class="top-bar">
    <h1>📬 Orientación de Proyectos</h1>

    <div style="display:flex; gap:10px; align-items:center;">
        
        <a href="<?php echo htmlspecialchars($back_url); ?>" class="button-secondary">← Volver a Biblioteca</a>
    </div>
</div>

<div class="info-box">
    <strong>¿Qué es esto?</strong>
    Aquí puedes enviar tu proyecto al administrador para recibir orientación personalizada.
</div>

<!-- PROYECTOS -->
<div class="project-list">
    <?php while ($row = $projects->fetch_assoc()): ?>
        <?php
            $project_data = json_decode($row['data'] ?? '{}', true);
            $project_tipo = strtoupper($project_data['tipo'] ?? '2D');
            $project_thumbnail = $project_data['thumbnail'] ?? '';
        ?>
        <div class="project-card">
            <?php if ($project_thumbnail): ?>
                <img src="<?php echo htmlspecialchars($project_thumbnail, ENT_QUOTES); ?>" alt="Vista previa de <?php echo htmlspecialchars($row['nombre']); ?>" style="width:100%;height:130px;object-fit:cover;border-radius:8px;margin-bottom:10px;">
            <?php endif; ?>
            <h2><?php echo htmlspecialchars($row['nombre']); ?></h2>
            <small>Proyecto <?php echo htmlspecialchars($project_tipo); ?></small>
            <a href="proyectos.php?project_id=<?php echo intval($row['id']); ?>">
                Seleccionar proyecto
            </a>
        </div>
    <?php endwhile; ?>
</div>

<!-- CHAT -->
<?php if ($selected_project): ?>
    <div class="chat-panel">

        <h2>Proyecto: <?php echo htmlspecialchars($selected_project['nombre']); ?></h2>

        <?php if (!empty($selected_project['thumbnail'])): ?>
            <img src="<?php echo htmlspecialchars($selected_project['thumbnail'], ENT_QUOTES); ?>" alt="Vista previa de <?php echo htmlspecialchars($selected_project['nombre']); ?>" style="width:100%;max-height:280px;object-fit:cover;border-radius:12px;margin:0 0 16px;border:1px solid rgba(255,255,255,0.12);">
        <?php endif; ?>

        <?php if ($mensaje_enviado): ?>
            <div class="status success">Tu mensaje fue enviado al asesor.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="status error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="messages">

            <?php if (count($messages) === 0): ?>
                <div class="message admin">
                    No hay mensajes todavía. Envía una pregunta para recibir orientación.
                </div>
            <?php endif; ?>

            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo ($message['autor'] === 'admin') ? 'admin' : 'cliente'; ?>">
                    <?php echo nl2br(htmlspecialchars($message['mensaje'])); ?>

                    <small>
                        <?php echo ($message['autor'] === 'admin') ? 'Administrador' : 'Tú'; ?>
                        · <?php echo date('d/m/Y H:i', strtotime($message['creado'])); ?>
                    </small>
                </div>
            <?php endforeach; ?>

        </div>

        <form method="post" class="new-message">
            <input type="hidden" name="project_id" value="<?php echo intval($selected_project['id']); ?>">

            <textarea name="mensaje" placeholder="Escribe tu consulta..." required></textarea>

            <button type="submit">Enviar mensaje</button>
        </form>

    </div>
<?php endif; ?>

</body>
</html>
