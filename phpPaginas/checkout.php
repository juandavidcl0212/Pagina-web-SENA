<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../phpPaginas/inSesion.php");
    exit;
}

$planId = $_POST['plan_id'] ?? $_GET['plan_id'] ?? null;
$plan = $_POST['plan'] ?? $_GET['plan'] ?? '';

if ($planId !== null) {
    $planId = intval($planId);
}

$precio = '---';
$planInfo = null;

if ($planId !== null && $planId > 0) {
    $stmt = $conn->prepare("SELECT nombre, precio FROM membresias WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $planId);
    $stmt->execute();
    $result = $stmt->get_result();
    $planInfo = $result->fetch_assoc();
    $stmt->close();
}

if (!$planInfo) {
    header("Location: ../phpPaginas/planes.php");
    exit;
}

$plan = $planInfo['nombre'];
$precio = '$' . $planInfo['precio'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago del plan</title>
    <link rel="stylesheet" href="../CSS/style planes.css">
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #1B1F3B 0%, #136F63 100%);
            color: #EDF7F6;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .encabezado {
            background: transparent;
            padding: 18px 28px;
        }

        .encabezado .menu a {
            color: #EDF7F6;
        }

        .checkout-container {
            max-width: 540px;
            margin: 60px auto 40px;
            padding: 32px 30px;
            background: rgba(14, 22, 40, 0.92);
            border: 1px solid rgba(237, 247, 246, 0.2);
            border-radius: 28px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
        }

        .checkout-container h1 {
            color: #EDF7F6;
            margin-bottom: 18px;
            font-size: 2rem;
        }

        .checkout-container p {
            color: #EDF7F6;
            line-height: 1.7;
            margin-bottom: 18px;
        }

        .checkout-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 18px;
            align-items: center;
        }

        .checkout-form label {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 14px;
            width: 100%;
            max-width: 420px;
            padding: 16px 20px;
            background: rgba(255, 159, 28, 0.12);
            border: 1px solid rgba(255, 159, 28, 0.3);
            border-radius: 18px;
            cursor: pointer;
            color: #EDF7F6;
            transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
            box-shadow: inset 0 0 0 1px rgba(237, 247, 246, 0.06);
        }

        .checkout-form label:hover {
            background: rgba(255, 159, 28, 0.18);
            border-color: rgba(255, 159, 28, 0.5);
            transform: translateX(2px);
        }

        .checkout-form input[type="radio"] {
            accent-color: #FF9F1C;
            width: 18px;
            height: 18px;
        }

        .checkout-form label span {
            display: inline-block;
            color: #EDF7F6;
            font-weight: 600;
            font-size: 1rem;
        }

        .plan1B {
            width: 100%;
            padding: 14px 0;
            background: linear-gradient(135deg, #FF9F1C 0%, #9067C6 100%);
            color: #1B1F3B;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, filter 0.2s ease;
        }

        .plan1B:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }

        .btn-menu {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 22px;
            background: rgba(237, 247, 246, 0.15);
            color: #EDF7F6;
            border: 1px solid rgba(237, 247, 246, 0.18);
            border-radius: 14px;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .btn-menu:hover {
            background: rgba(255, 159, 28, 0.2);
        }

        @media (max-width: 640px) {
            .checkout-container {
                margin: 40px 16px;
                padding: 28px 20px;
            }
        }
    </style>
</head>
<body>

<header class="encabezado">
    <img src="../assets/fb p.png" alt="Logo fantasy box" class="fantasy box">
    <nav class="menu">
      <a href="../index.php" class="btn-menu">Inicio</a>
      <a href="../phpPaginas/servicios.php" class="btn-menu">Servicios</a>
      <a href="../phpPaginas/planes.php" class="btn-menu">Planes</a>
      <a href="../phpPaginas/contacto.php" class="btn-menu">Contacto</a>
    </nav>
</header>

<section class="checkout-container">
    <h1>Comprar plan: <?php echo htmlspecialchars($plan); ?></h1>
    <p>Precio aproximado: <strong><?php echo htmlspecialchars($precio); ?></strong></p>
    <p>Selecciona el banco con el que deseas realizar el pago.</p>

    <form method="POST" action="../phpFunciones/comprar_plan.php" class="checkout-form">
        <input type="hidden" name="plan_id" value="<?php echo htmlspecialchars($planId); ?>">
        <input type="hidden" name="plan" value="<?php echo htmlspecialchars($plan); ?>">

        <label>
            <input type="radio" name="banco" value="Bancolombia" required>
            <span>Bancolombia</span>
        </label>
        <label>
            <input type="radio" name="banco" value="Davivienda">
            <span>Davivienda</span>
        </label>
        <label>
            <input type="radio" name="banco" value="BBVA">
            <span>BBVA</span>
        </label>
        <label>
            <input type="radio" name="banco" value="Banco de Bogotá">
            <span>Banco de Bogotá</span>
        </label>
        <label>
            <input type="radio" name="banco" value="Nequi">
            <span>Nequi</span>
        </label>

        <button type="submit" class="plan1B">Continuar al pago</button>
    </form>

    <a href="../phpPaginas/planes.php" class="btn-menu" style="display:inline-block; margin-top:20px;">Volver a planes</a>
</section>

</body>
</html>
