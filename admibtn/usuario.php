<?php
include("../conexion.php");

// Obtener todos los usuarios
$sqlUsuarios = "SELECT id, nombre, email, fecha_registro 
                FROM usuarios 
                ORDER BY fecha_registro DESC";
$usuarios = mysqli_query($enlace, $sqlUsuarios);

// Obtener usuarios por mes (para la gráfica)
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
  $totales[] = (int)$fila['total'];
}

// Usuarios nuevos este mes
$sqlNuevos = "
  SELECT COUNT(*) AS nuevos
  FROM usuarios
  WHERE MONTH(fecha_registro) = MONTH(CURDATE())
    AND YEAR(fecha_registro) = YEAR(CURDATE())
";
$resNuevos = mysqli_query($enlace, $sqlNuevos);
$nuevos = (int)mysqli_fetch_assoc($resNuevos)['nuevos'];
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

  <h1> Administración de Usuarios</h1>

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
        <td><?= (int)$u['id'] ?></td>
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
          data: <?= json_encode($totales, JSON_NUMERIC_CHECK) ?>,
          backgroundColor: '#136F63'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
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
 /* Fondo general */
body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #EDF7F6 0%, #FFFFFF 100%);
  color: #1B1F3B;
}

/* Encabezado principal */
h1 {
  text-align: center;
  background: #1B1F3B;
  color: white;
  padding: 25px;
  margin: 0;
  font-size: 2.2rem;
  letter-spacing: 2px;
  box-shadow: 0px 4px 12px rgba(0,0,0,0.2);
}

/* Subtítulos */
h2 {
  margin-top: 40px;
  text-align: center;
  color: #136F63;
  font-size: 1.8rem;
}

/* Texto resumen */
p {
  text-align: center;
  font-size: 20px;
  margin: 20px;
  font-weight: bold;
}

/* Contenedor gráfico */
.grafico {
  width: 85%;
  margin: 40px auto;
  background: white;
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0px 8px 20px rgba(0,0,0,0.15);
  transition: transform 0.3s ease;
}
.grafico:hover {
  transform: scale(1.02);
}

/* Tabla */
table {
  width: 95%;
  margin: 40px auto;
  border-collapse: collapse;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0px 8px 20px rgba(0,0,0,0.15);
  font-size: 16px;
}

/* Encabezado */
th {
  background: #136F63;
  color: white;
  padding: 14px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Filas */
td {
  padding: 14px;
  text-align: center;
  border-bottom: 1px solid #EDF7F6;
}

/* Alternar colores */
tr:nth-child(even) {
  background: #EDF7F6;
}

/* Hover filas */
tr:hover {
  background: #9067C6;
  color: white;
  transition: 0.3s;
  cursor: pointer;
}

/* Botones */
button {
  background: #FF9F1C;
  border: none;
  padding: 12px 18px;
  color: white;
  border-radius: 10px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  transition: 0.3s;
  margin: 10px;
}

button:hover {
  background: #1B1F3B;
  transform: scale(1.05);
}

  </style>

</body>
</html>
