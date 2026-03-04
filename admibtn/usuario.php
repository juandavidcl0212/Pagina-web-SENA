<?php
include("../conexion.php");

// Obtener todos los usuarios
$sqlUsuarios = "SELECT id, nombre, email, fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
$usuarios = mysqli_query($enlace, $sqlUsuarios);

// Obtener usuarios por mes
$sqlGrafica = "
  SELECT DATE_FORMAT(fecha_registro, '%Y-%m') AS mes, COUNT(*) AS total
  FROM usuarios
  GROUP BY mes
  ORDER BY mes ASC
";
$datos = mysqli_query($enlace, $sqlGrafica);

$meses = [];
$totales = [];

while ($fila = mysqli_fetch_assoc($datos)) {
  $meses[] = $fila['mes'];
  $totales[] = (int)$fila['total']; // 🔑 Forzar a entero
}

// Usuarios nuevos este mes
$sqlNuevos = "
  SELECT COUNT(*) AS nuevos
  FROM usuarios
  WHERE MONTH(fecha_registro) = MONTH(CURDATE())
    AND YEAR(fecha_registro) = YEAR(CURDATE())
";
$resNuevos = mysqli_query($enlace, $sqlNuevos);
$nuevos = (int)mysqli_fetch_assoc($resNuevos)['nuevos']; // 🔑 Forzar a entero
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Usuarios</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../CSS/admi_usuario.css">
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .grafico { max-width: 700px; margin-top: 40px; }
  </style>
</head>
<body>

  <h1>Panel de Administración - Usuarios</h1>

  <p><strong>Usuarios nuevos este mes:</strong> <?= $nuevos ?></p>

  <div class="grafico">
    <canvas id="graficaUsuarios"></canvas>
  </div>

  <h2>Lista de Usuarios Registrados</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Email</th>
      <th>Fecha de Registro</th>
    </tr>
    <?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
      <tr>
        <td><?= (int)$u['id'] ?></td> <!-- 🔑 ID como entero -->
        <td><?= htmlspecialchars($u['nombre']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= $u['fecha_registro'] ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <script>
    const ctx = document.getElementById('graficaUsuarios').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($meses) ?>,
        datasets: [{
          label: 'Usuarios registrados por mes',
          data: <?= json_encode($totales, JSON_NUMERIC_CHECK) ?>, // 🔑 Forzar números en JSON
          backgroundColor: 'rgba(75, 192, 192, 0.6)'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1, // 🔑 Mostrar solo enteros
              callback: function(value) {
                return Number.isInteger(value) ? value : null;
              }
            }
          }
        }
      }
    });
  </script>

</body>
</html>
