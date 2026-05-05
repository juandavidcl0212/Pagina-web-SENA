<?php
session_start();

$conn = new mysqli("localhost","root","","prueba_db");
if($conn->connect_error){
    die("Error de conexión");
}

/* BUSCADOR */
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : "";

/* FILTRO: todos los mensajes */
$sql = "SELECT pm.*, p.nombre AS proyecto, u.nombre AS usuario
        FROM project_messages pm
        JOIN proyectos p ON p.id = pm.proyecto_id
        JOIN usuarios u ON u.id = pm.usuario_id
        WHERE pm.mensaje LIKE ?
        ORDER BY pm.creado DESC";

$stmt = $conn->prepare($sql);
$like = "%$busqueda%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de Mensajes</title>

<style>
/* ===== COLORES ===== */
:root{
  --dark:#1B1F3B;
  --green:#136F63;
  --light:#EDF7F6;
  --purple:#9067C6;
  --orange:#FF9F1C;
}

/* ===== BODY ===== */
body{
  margin:0;
  font-family:Segoe UI;
  background:var(--dark);
  color:var(--light);
}

/* ===== CONTENEDOR ===== */
.container{
  max-width:1000px;
  margin:30px auto;
  padding:20px;
}

/* ===== HEADER ===== */
h2{
  margin-bottom:10px;
}

/* ===== SEARCH ===== */
.search-box{
  width:100%;
  padding:12px;
  border:none;
  border-radius:12px;
  outline:none;
  margin-bottom:20px;
  background:#11152a;
  color:white;
  border:1px solid rgba(255,255,255,0.1);
}

/* ===== BOTONES FILTRO ===== */
.filters{
  display:flex;
  gap:10px;
  margin-bottom:20px;
}

.filters button{
  padding:10px 14px;
  border:none;
  border-radius:10px;
  cursor:pointer;
  font-weight:bold;
  background:#FF9F1C;
  color:#1B1F3B;
  transition:0.2s;
}

.filters button:hover{
  transform:scale(1.05);
}

/* ===== CARD ===== */
.card{
  background:#11152a;
  padding:16px;
  border-radius:14px;
  margin-bottom:12px;
  border-left:4px solid var(--purple);
  transition:0.2s;
}

.card:hover{
  transform:translateY(-3px);
}

/* eliminado */
.deleted{
  opacity:0.5;
  border-left:4px solid red;
}

/* etiquetas */
.badge{
  display:inline-block;
  padding:4px 8px;
  font-size:11px;
  border-radius:8px;
  margin-top:6px;
}

.ok{background:var(--green);}
.del{background:#ff4d4d;}

/* texto */
small{
  display:block;
  margin-top:6px;
  opacity:0.7;
}

/* botón volver */
.back{
  display:inline-block;
  margin-bottom:20px;
  text-decoration:none;
  padding:10px 14px;
  background:var(--orange);
  color:#1B1F3B;
  border-radius:10px;
  font-weight:bold;
}
</style>
</head>

<body>

<div class="container">

<a class="back" href="proyectos.php">⬅ Volver</a>

<h2>📦 Historial de Mensajes</h2>

<!-- BUSCADOR -->
<input type="text" id="search" class="search-box" placeholder="Buscar mensaje, proyecto o usuario...">

<!-- FILTROS -->
<div class="filters">
    <button onclick="filtrar('todos')">Todos</button>
    <button onclick="filtrar('activos')">Activos</button>
    <button onclick="filtrar('eliminados')">Eliminados</button>
</div>

<!-- LISTA -->
<div id="lista">

<?php while($m = $result->fetch_assoc()): ?>

<div class="card <?php echo $m['eliminado'] ? 'deleted' : ''; ?>" 
     data-text="<?php echo strtolower($m['mensaje'].' '.$m['proyecto'].' '.$m['usuario']); ?>"
     data-status="<?php echo $m['eliminado'] ? 'eliminado' : 'activo'; ?>">

    <strong>📁 Proyecto:</strong> <?php echo htmlspecialchars($m['proyecto']); ?><br>
    <strong>👤 Usuario:</strong> <?php echo htmlspecialchars($m['usuario']); ?><br>

    <p><?php echo nl2br(htmlspecialchars($m['mensaje'])); ?></p>

    <?php if($m['eliminado']): ?>
        <span class="badge del">Eliminado</span>
    <?php else: ?>
        <span class="badge ok">Activo</span>
    <?php endif; ?>

    <small>📅 <?php echo date('d/m/Y H:i', strtotime($m['creado'])); ?></small>

</div>

<?php endwhile; ?>

</div>

</div>

<script id="history_js">

// 🔍 BUSCADOR EN TIEMPO REAL
document.getElementById("search").addEventListener("keyup", function(){
    let value = this.value.toLowerCase();
    let cards = document.querySelectorAll(".card");

    cards.forEach(card => {
        let text = card.getAttribute("data-text");
        card.style.display = text.includes(value) ? "block" : "none";
    });
});

// 📦 FILTROS
function filtrar(tipo){
    let cards = document.querySelectorAll(".card");

    cards.forEach(card => {

        let status = card.getAttribute("data-status");

        if(tipo === "todos"){
            card.style.display = "block";
        }

        else if(tipo === "activos"){
            card.style.display = (status === "activo") ? "block" : "none";
        }

        else if(tipo === "eliminados"){
            card.style.display = (status === "eliminado") ? "block" : "none";
        }

    });
}

</script>

</body>
</html>