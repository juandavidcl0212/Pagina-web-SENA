<?php
session_start();

$conn = new mysqli("localhost", "root", "", "prueba_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 🔴 CONTADOR (Paso 4)
$sqlCount = "SELECT COUNT(*) as total FROM project_messages WHERE visto = 0 AND eliminado = 0";
$resultCount = $conn->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalNuevos = $rowCount['total'];

// 🔹 OBTENER MENSAJES
$sql = "SELECT * FROM project_messages 
        WHERE eliminado = 0 
        ORDER BY creado DESC";

$result = $conn->query($sql);

// 🔹 MARCAR COMO VISTOS (Paso 5)
$conn->query("UPDATE project_messages SET visto = 1 WHERE visto = 0");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mensajes del sistema</title>

<style>
body {
    font-family: 'Inter', sans-serif;
    background: #0f172a;
    color: #edf7f6;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 900px;
    margin: auto;
}

h1 {
    text-align: center;
}

.notificacion {
    background: #1e293b;
    padding: 12px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
    font-weight: bold;
}

.mensaje {
    background: #1e293b;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 15px;
    border-left: 6px solid #FF9F1C;
}

.mensaje.visto {
    opacity: 0.7;
    border-left: 6px solid #22c55e;
}

.archivo {
    margin-top: 8px;
}

a {
    color: #60a5fa;
}
</style>
</head>

<body>

<div class="container">

    <h1>📩 Mensajes de usuarios</h1>

    <!-- 🔴 Paso 6 -->
    <div class="notificacion">
        <?php if ($totalNuevos > 0): ?>
            🔴 Tienes <?php echo $totalNuevos; ?> mensajes nuevos
        <?php else: ?>
            🟢 Todo al día
        <?php endif; ?>
    </div>

    <!-- LISTA DE MENSAJES -->
    <?php while($row = $result->fetch_assoc()): ?>

        <div class="mensaje <?php echo $row['visto'] ? 'visto' : ''; ?>">
            
            <strong>Proyecto ID:</strong> <?php echo $row['proyecto_id']; ?><br>
            <strong>Usuario ID:</strong> <?php echo $row['usuario_id']; ?><br>
            <strong>Mensaje:</strong><br>
            <?php echo nl2br(htmlspecialchars($row['mensaje'])); ?>

            <?php if (!empty($row['archivo'])): ?>
                <div class="archivo">
                    📎 <a href="../uploads/<?php echo $row['archivo']; ?>" target="_blank">
                        Ver archivo
                    </a>
                </div>
            <?php endif; ?>

            <br>
            <small>🕒 <?php echo $row['creado']; ?></small>

        </div>

    <?php endwhile; ?>

</div>

</body>
</html>