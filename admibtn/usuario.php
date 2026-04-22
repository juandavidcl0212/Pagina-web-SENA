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
  <link rel="stylesheet" href="../CSS/usuario.css">
  <link rel="stylesheet" href="../styles.css">
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

  <style>
  body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #EDF7F6;
  color: #1B1F3B;
}

/* Título */
h1 {
  text-align: center;
  background: #1B1F3B;
  color: white;
  padding: 20px;
  margin: 0;
  letter-spacing: 1px;
}

/* Subtítulos */
h2 {
  margin-top: 40px;
  text-align: center;
  color: #136F63;
}

/* Texto resumen */
p {
  text-align: center;
  font-size: 18px;
  margin: 20px;
}

/* Contenedor gráfico */
.grafico {
  width: 80%;
  margin: 30px auto;
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
}

/* Tabla */
table {
  width: 90%;
  margin: 30px auto;
  border-collapse: collapse;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
}

/* Encabezado */
th {
  background: #136F63;
  color: white;
  padding: 12px;
}

/* Filas */
td {
  padding: 12px;
  text-align: center;
}

/* Alternar colores */
tr:nth-child(even) {
  background: #EDF7F6;
}

/* Hover */
tr:hover {
  background: #9067C6;
  color: white;
  transition: 0.3s;
}

/* Botones (si agregas después) */
button {
  background: #FF9F1C;
  border: none;
  padding: 10px 15px;
  color: white;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
}

button:hover {
  background: #1B1F3B;
}
 </style>


</body>
</html>
