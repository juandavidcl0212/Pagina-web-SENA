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
            $error = 'Proyecto no encontrado o no tienes permisos.';
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

$projects = $conn->query("SELECT id, nombre FROM proyectos WHERE usuario_id = $usuario_id ORDER BY fecha DESC");
$selected_project = null;
$messages = [];

if ($project_id > 0) {
    $stmt = $conn->prepare('SELECT id, nombre FROM proyectos WHERE id = ? AND usuario_id = ? LIMIT 1');
    $stmt->bind_param('ii', $project_id, $usuario_id);
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
    <title>Orientación de Proyectos</title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, rgba(255, 159, 28, 0.16), transparent 24%),
                        radial-gradient(circle at bottom right, rgba(144, 103, 198, 0.18), transparent 28%),
                        linear-gradient(135deg, #111827 0%, #0F172A 55%, #134E4A 100%);
            color: #EDF7F6;
            min-height: 100vh;
        }
        .container,
        .top-bar,
        .info-box,
        .project-list,
        .project-card,
        .chat-panel,
        .messages,
        .new-message {
            margin: 0;
            padding: 0;
            box-shadow: none;
        }
        .container {
            max-width: 1040px;
            margin: 40px auto;
            padding: 28px;
        }
        .top-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
        }
        .top-bar h1 {
            margin: 0;
            font-size: 2rem;
            letter-spacing: 0.02em;
        }
        .top-bar .button-secondary {
            background: rgba(255, 159, 28, 0.95);
            color: #111827;
            border: none;
            padding: 14px 20px;
            border-radius: 16px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 14px 30px rgba(255, 159, 28, 0.18);
        }
        .top-bar .button-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 32px rgba(255, 159, 28, 0.24);
        }
        .info-box {
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(226, 232, 240, 0.12);
            border-radius: 22px;
            padding: 22px;
            margin-bottom: 28px;
            line-height: 1.75;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.03);
        }
        .project-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 32px;
        }
        .project-card {
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 22px;
            padding: 22px;
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        }
        .project-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 159, 28, 0.35);
            box-shadow: 0 22px 48px rgba(15, 23, 42, 0.25);
        }
        .project-card h2 {
            margin: 0 0 14px;
            font-size: 1.2rem;
            line-height: 1.3;
        }
        .project-card a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            background: linear-gradient(135deg, #FF9F1C 0%, #9067C6 100%);
            color: #111827;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(255, 159, 28, 0.18);
        }
        .project-card a:hover {
            transform: translateY(-1px);
        }
        .chat-panel {
            background: rgba(15, 23, 42, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.2);
        }
        .chat-panel h2 {
            margin-top: 0;
            font-size: 1.5rem;
            margin-bottom: 18px;
        }
        .messages {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 24px;
            max-height: 520px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .message {
            max-width: 82%;
            padding: 16px 20px;
            border-radius: 22px;
            line-height: 1.75;
            font-size: 0.98rem;
            word-break: break-word;
        }
        .message.cliente {
            align-self: flex-end;
            background: rgba(255, 159, 28, 0.18);
            border: 1px solid rgba(255, 159, 28, 0.35);
            color: #111827;
            backdrop-filter: blur(8px);
        }
        .message.admin {
            align-self: flex-start;
            background: rgba(144, 103, 198, 0.18);
            border: 1px solid rgba(144, 103, 198, 0.4);
            color: #EDF7F6;
            backdrop-filter: blur(8px);
        }
        .message small {
            display: block;
            margin-top: 10px;
            color: rgba(237, 247, 246, 0.68);
            font-size: 0.84rem;
        }
        .new-message {
            display: grid;
            gap: 14px;
        }
        .new-message textarea {
            width: 100%;
            min-height: 140px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.05);
            color: #EDF7F6;
            padding: 18px;
            resize: vertical;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
        }
        .new-message textarea::placeholder {
            color: rgba(237, 247, 246, 0.6);
        }
        .new-message button {
            align-self: flex-start;
            background: linear-gradient(135deg, #FF9F1C 0%, #9067C6 100%);
            color: #111827;
            border: none;
            border-radius: 18px;
            padding: 16px 24px;
            cursor: pointer;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 16px 30px rgba(255, 159, 28, 0.18);
        }
        .new-message button:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 32px rgba(255, 159, 28, 0.24);
        }
        .status {
            margin-bottom: 18px;
            padding: 14px 18px;
            border-radius: 16px;
            font-weight: 600;
        }
        .status.success {
            background: rgba(32, 124, 74, 0.18);
            border: 1px solid rgba(32, 124, 74, 0.34);
            color: #E6F5EA;
        }
        .status.error {
            background: rgba(217, 83, 79, 0.18);
            border: 1px solid rgba(217, 83, 79, 0.35);
            color: #F8E1E0;
        }
        @media (max-width: 900px) {
            .container {
                margin: 28px 18px;
                padding: 18px;
            }
            .top-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .project-card,
            .chat-panel {
                padding: 20px;
            }
        }
        @media (max-width: 640px) {
            .project-card {
                padding: 18px;
            }
            .new-message textarea {
                min-height: 120px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <h1>📬 Orientación de Proyectos</h1>
        <a href="mis_proyectos.php" class="button-secondary">Volver a Mis Proyectos</a>
    </div>

    <div class="info-box">
        <strong>¿Qué es esto?</strong> Aquí puedes enviar tu proyecto al administrador para recibir recomendaciones y orientación personalizada. Selecciona tu diseño, describe tus dudas y espera la respuesta del equipo.
    </div>

    <div class="project-list">
        <?php while ($row = $projects->fetch_assoc()): ?>
            <div class="project-card">
                <h2><?php echo htmlspecialchars($row['nombre']); ?></h2>
                <a href="proyectos.php?project_id=<?php echo intval($row['id']); ?>">Seleccionar proyecto</a>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if ($selected_project): ?>
        <div class="chat-panel">
            <h2>Proyecto: <?php echo htmlspecialchars($selected_project['nombre']); ?></h2>
            <?php if ($mensaje_enviado): ?>
                <div class="status success">Tu mensaje fue enviado al asesor.</div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="status error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="messages">
                <?php if (count($messages) === 0): ?>
                    <div class="message admin">
                        No hay mensajes todavía. Envía una pregunta para recibir orientación.
                    </div>
                <?php endif; ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?php echo $message['autor'] === 'admin' ? 'admin' : 'cliente'; ?>">
                        <?php echo nl2br(htmlspecialchars($message['mensaje'])); ?>
                        <small><?php echo $message['autor'] === 'admin' ? 'Administrador' : 'Tú'; ?> · <?php echo date('d/m/Y H:i', strtotime($message['creado'])); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="post" class="new-message">
                <input type="hidden" name="project_id" value="<?php echo intval($selected_project['id']); ?>">
                <textarea name="mensaje" placeholder="Escribe tu consulta para el asesor..." required></textarea>
                <button type="submit">Enviar mensaje al asesor</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
