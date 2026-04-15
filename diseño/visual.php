<?php
include("../conexion.php");

$sql = "SELECT * FROM membresias";
$result = $conn->query($sql);

if(!$result){
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Visualizador 2D</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="main">
        <section class="table-section">
            <h2>Planes de Membresía</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            echo "<tr>
                                <td>".htmlspecialchars($row['nombre'])."</td>
                                <td>".htmlspecialchars($row['descripcion'])."</td>
                                <td>$".htmlspecialchars($row['precio'])."</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No hay registros</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
<?php
$conn->close();
?>


