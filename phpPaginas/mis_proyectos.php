<?php
session_start();

if(!isset($_SESSION['id_usuario'])){
    die("Debes iniciar sesión");
}

$conn = new mysqli("localhost","root","","prueba_db");

if($conn->connect_error){
    die("Error de conexión");
}

$usuario_id = intval($_SESSION['id_usuario']);

/* ELIMINAR */
if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM proyectos WHERE id=$id AND usuario_id=$usuario_id");
    header("Location: mis_proyectos.php");
    exit();
}

/* CONSULTA */
$result = $conn->query("SELECT id, nombre, datos, tipo FROM proyectos 
                        WHERE usuario_id=$usuario_id AND tipo='2D'
                        ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Proyectos</title>

<style>
:root{
  --dark:#1B1F3B;
  --green:#136F63;
  --light:#EDF7F6;
  --purple:#9067C6;
  --orange:#FF9F1C;
}

/* BODY */
body{
  margin:0;
  font-family:Segoe UI;
  background:var(--dark);
  color:white;
}

/* HEADER */
header{
  padding:20px;
  background:#11153a;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

/* GRUPO IZQUIERDA */
.header-left{
  display:flex;
  align-items:center;
  gap:15px;
}

/* BUSCADOR */
.search{
  padding:10px;
  border-radius:10px;
  border:none;
  width:250px;
}

/* GRID */
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
  gap:20px;
  padding:20px;
}

/* CARD */
.card{
  background:#151a4a;
  border-radius:15px;
  padding:15px;
  transition:0.3s;
  border:1px solid rgba(255,255,255,0.05);
}

.card:hover{
  transform:translateY(-5px);
  border:1px solid var(--orange);
}

/* IMG */
.card img{
  width:100%;
  border-radius:10px;
}

.preview{
  width:100%;
  min-height:180px;
  border-radius:12px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:radial-gradient(circle at top left, rgba(255,159,28,0.17), transparent 35%), rgba(17,21,58,0.96);
  color:#edf7f6;
  font-size:1rem;
  font-weight:bold;
  text-align:center;
  padding:20px;
  margin-bottom:12px;
  border:1px dashed rgba(255,255,255,0.12);
}

/* TITULO */
.card h3{
  margin:10px 0;
}

/* BOTONES */
.btn{
  padding:8px 10px;
  border:none;
  border-radius:8px;
  cursor:pointer;
  font-size:12px;
  margin:3px;
}

/* COLORES */
.ver{ background:var(--green); color:white;}
.eliminar{ background:#ff4d4d; color:white;}
.volver{ background:var(--orange); color:white; font-weight:bold;}

.btn:hover{
  opacity:0.85;
}
</style>
</head>

<body>

<header style="padding:20px; background:#11153a; display:flex; justify-content:space-between; align-items:center;">

<div style="display:flex; flex-wrap:wrap; align-items:center; gap:15px; width:100%;">
    
    <!-- BOTÓN VOLVER (FORZADO) -->
    <button onclick="window.location.href='biblioteca.php'" 
      style="
        background:#FF9F1C;
        color:white;
        border:none;
        padding:10px 15px;
        border-radius:10px;
        font-weight:bold;
        cursor:pointer;
      ">
      ⬅ Volver a Biblioteca
    </button>

    <h2 style="margin:0;">📁 Mis Proyectos</h2>

    <button onclick="window.location.href='proyectos.php'"
      style="
        background:#9067C6;
        color:white;
        border:none;
        padding:10px 16px;
        border-radius:10px;
        font-weight:bold;
        cursor:pointer;
      ">
      📘 Instrucciones
    </button>
  </div>

  <form action="../phpFunciones/subir_archivo_proyecto.php" method="post" enctype="multipart/form-data" style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end; margin-top:20px; padding:20px; background:#11153a; border-radius:15px;">
    <div style="display:flex; flex-direction:column; gap:8px; min-width:220px; flex:1;">
      <label for="project_id" style="color:#edf7f6; font-weight:bold;">Proyecto 2D</label>
      <select name="project_id" id="project_id" required style="padding:10px; border-radius:10px; border:none; background:#151a4a; color:#edf7f6;">
        <option value="">Selecciona un proyecto</option>
        <?php
        mysqli_data_seek($result, 0);
        while ($rowOption = $result->fetch_assoc()): ?>
          <option value="<?php echo intval($rowOption['id']); ?>"><?php echo htmlspecialchars($rowOption['nombre']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div style="display:flex; flex-direction:column; gap:8px; min-width:220px; flex:2;">
      <label for="comentario" style="color:#edf7f6; font-weight:bold;">Comentario para el asesor</label>
      <textarea name="comentario" id="comentario" rows="3" placeholder="Describe lo que necesitas..." required style="width:100%; border-radius:12px; border:1px solid rgba(255,255,255,0.12); background:#11153a; color:#edf7f6; padding:12px;"></textarea>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px; min-width:200px;">
      <label style="color:#edf7f6; font-weight:bold;">Archivo</label>
      <button type="button" onclick="document.getElementById('archivoInput').click();" style="background:#1D4ED8; color:white; border:none; padding:12px 16px; border-radius:12px; cursor:pointer;">⬆ Seleccionar archivo</button>
      <span id="archivoNombre" style="color:#edf7f6; font-size:0.95rem; min-height:1.2em;"></span>
      <input type="file" name="archivo" id="archivoInput" accept="image/*,.pdf,.zip" style="display:none;" required />
      <button type="submit" style="background:#FF9F1C; color:#111827; border:none; padding:12px 16px; border-radius:12px; font-weight:bold; cursor:pointer;">Enviar al asesor</button>
    </div>
  </form>

  <?php if (isset($_GET['success'])): ?>
    <div style="margin-top:20px; padding:14px 18px; border-radius:14px; background:rgba(16,185,129,0.18); border:1px solid rgba(16,185,129,0.45); color:#d1fae5;">
      Tu archivo y comentario se enviaron al asesor correctamente.
    </div>
  <?php endif; ?>

  <input type="text" id="buscar" placeholder="Buscar..." 
    style="margin-top:20px; padding:10px; border-radius:10px; border:none; width:250px; background:#11153a; color:#edf7f6;">
</header>

<div class="grid">
  <?php
  mysqli_data_seek($result, 0);
  while($row = $result->fetch_assoc()) { ?>
    <div class="card" data-nombre="<?php echo strtolower($row['nombre']); ?>">
        <div class="preview">Proyecto 2D guardado</div>
        <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>

        <!-- BOTONES -->
        <button class="btn ver" 
            onclick="window.location='editor.php?id=<?php echo $row['id']; ?>'">
            Ver / Editar
        </button>

        <button class="btn eliminar" 
            onclick="eliminarProyecto(<?php echo $row['id']; ?>)">
            Eliminar
        </button>

        <button class="btn" style="background:#9067C6; color:white;" 
            onclick="window.location='proyectos.php?project_id=<?php echo $row['id']; ?>'">
            Solicitar orientación
        </button>
    </div>
  <?php } ?>
</div>

<script>

/* ELIMINAR */
function eliminarProyecto(id){
  if(confirm("¿Seguro que deseas eliminar este proyecto?")){
    window.location="mis_proyectos.php?eliminar="+id;
  }
}

/* BUSCADOR */
document.getElementById("buscar").addEventListener("keyup",function(){
  let filtro = this.value.toLowerCase();
  document.querySelectorAll(".card").forEach(card=>{
    let nombre = card.dataset.nombre;
    card.style.display = nombre.includes(filtro) ? "block" : "none";
  });
});

document.getElementById('archivoInput').addEventListener('change', function() {
  const etiqueta = document.getElementById('archivoNombre');
  if (this.files.length > 0) {
    etiqueta.textContent = 'Archivo: ' + this.files[0].name;
  } else {
    etiqueta.textContent = '';
  }
});
</script>

</body>
</html>