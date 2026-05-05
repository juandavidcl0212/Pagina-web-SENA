<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("Debes iniciar sesión");
}

$conn = new mysqli("localhost", "root", "", "prueba_db");

$project_id = intval($_GET['id'] ?? 0);
$usuario_id = $_SESSION['id_usuario'] ?? 0;

if ($project_id > 0 && $usuario_id > 0) {

    $conn->query("
        UPDATE project_messages 
        SET visto = 1 
        WHERE proyecto_id = $project_id 
        AND autor = 'admin'
    ");
}

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$usuario_id = intval($_SESSION['id_usuario']);
$project_id = intval($_GET['id'] ?? 0);

if ($project_id <= 0) {
    die("Proyecto inválido");
}

/* =========================
   MARCAR MENSAJES COMO VISTOS
   (cuando el usuario entra)
========================= */
$conn->query("
    UPDATE project_messages 
    SET visto = 1 
    WHERE proyecto_id = $project_id 
    AND autor = 'admin'
");

/* =========================
   PROYECTO
========================= */
$stmt = $conn->prepare("
    SELECT * FROM proyectos 
    WHERE id = ? AND usuario_id = ?
");
$stmt->bind_param("ii", $project_id, $usuario_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    die("Proyecto no encontrado");
}

/* =========================
   MENSAJES
========================= */
$stmt = $conn->prepare("
    SELECT * FROM project_messages 
    WHERE proyecto_id = ? 
    ORDER BY creado ASC
");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$messages = $stmt->get_result();
$stmt->close();

/* =========================
   ENVIAR MENSAJE USUARIO
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($mensaje !== '') {

        $stmt = $conn->prepare("
            INSERT INTO project_messages 
            (proyecto_id, usuario_id, autor, mensaje, creado, visto)
            VALUES (?, ?, 'usuario', ?, NOW(), 0)
        ");

        $stmt->bind_param("iis", $project_id, $usuario_id, $mensaje);
        $stmt->execute();
        $stmt->close();

        header("Location: ver_proyecto.php?id=" . $project_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proyecto</title>

<style>
body{
    margin:0;
    font-family:Segoe UI;
    background:#1B1F3B;
    color:white;
}

header{
    padding:15px;
    background:#11152a;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.container{
    padding:20px;
}

.chat{
    height:450px;
    overflow-y:auto;
    background:#141a33;
    padding:15px;
    border-radius:12px;
}

.msg{
    padding:10px;
    margin-bottom:10px;
    border-radius:10px;
    max-width:70%;
}

.msg.usuario{
    background:#136F63;
    margin-left:auto;
}

.msg.admin{
    background:#9067C6;
    margin-right:auto;
}

textarea{
    width:100%;
    min-height:120px;
    padding:10px;
    border-radius:10px;
    border:none;
    margin-top:10px;
    background:#11152a;
    color:white;
}

button{
    margin-top:10px;
    padding:10px 14px;
    background:#FF9F1C;
    border:none;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}
</style>
</head>

<body>

<header>
    <h3><?php echo htmlspecialchars($project['nombre']); ?></h3>
    <a href="mis_proyectos.php" style="color:white;text-decoration:none;">⬅ Volver</a>
</header>

<div class="container">

    <div class="chat">

        <?php while($m = $messages->fetch_assoc()): ?>

            <div class="msg <?php echo $m['autor']; ?>">

                <?php echo nl2br(htmlspecialchars($m['mensaje'])); ?>

                <br>
                <small>
                    <?php echo $m['autor']; ?> · 
                    <?php echo $m['creado']; ?>
                </small>

            </div>

        <?php endwhile; ?>

    </div>

    <form method="POST">
        <textarea name="mensaje" placeholder="Escribe tu mensaje..." required></textarea>
        <button type="submit">Enviar</button>
    </form>

</div>

</body>
</html>