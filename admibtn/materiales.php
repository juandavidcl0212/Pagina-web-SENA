<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'admin';
}

$esAdmin = $_SESSION['rol'] === 'admin';

/* ➕ AGREGAR */
if ($esAdmin && isset($_POST['agregar'])) {

    $nombre = mysqli_real_escape_string($enlace, $_POST['nombre']);
    $precio = (float) $_POST['precio'];

    /* 🖼️ SUBIR IMAGEN */
    $imagen = $_FILES['imagen']['name'];
    $tmp = $_FILES['imagen']['tmp_name'];

    $ruta = "../uploads" . $imagen;
    move_uploaded_file($tmp, $ruta);

    mysqli_query($enlace, "INSERT INTO catalogo (nombre, precio, imagen)
    VALUES ('$nombre', $precio, '$ruta')");

    header("Location: materiales.php");
    exit();
}

/* ❌ ELIMINAR */
if ($esAdmin && isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];

    mysqli_query($enlace, "DELETE FROM catalogo WHERE id=$id");
    header("Location: materiales.php");
    exit();
}

/* 📦 DATOS */
$materiales = mysqli_query($enlace, "SELECT * FROM catalogo ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Catálogo PRO</title>

<style>
:root{
  --dark:#1B1F3B;
  --green:#136F63;
  --light:#EDF7F6;
  --purple:#9067C6;
  --orange:#FF9F1C;
}

body{
  margin:0;
  font-family:Segoe UI;
  background:var(--light);
}

h1{
  background:var(--dark);
  color:white;
  text-align:center;
  padding:20px;
  margin:0;
}

/* GRID */
.grid{
  display:flex;
  flex-wrap:wrap;
  gap:20px;
  justify-content:center;
  padding:20px;
}

/* CARD */
.card{
  width:220px;
  background:white;
  border-radius:15px;
  overflow:hidden;
  box-shadow:0 10px 25px rgba(0,0,0,0.1);
  text-align:center;
}

.card img{
  width:100%;
  height:150px;
  object-fit:cover;
}

.card h3{
  margin:10px 0;
}

.card p{
  color:gray;
}

/* BOTÓN */
.btn{
  background:linear-gradient(135deg,#FF9F1C,#9067C6);
  color:white;
  padding:8px;
  border:none;
  border-radius:8px;
  cursor:pointer;
  width:90%;
  margin-bottom:10px;
}

/* MODAL */
.modal{
  display:none;
  position:fixed;
  top:0;left:0;
  width:100%;height:100%;
  background:rgba(27,31,59,0.7);
  backdrop-filter:blur(5px);
}

.modal-content{
  background:white;
  width:350px;
  margin:8% auto;
  padding:20px;
  border-radius:15px;
  text-align:center;
  box-shadow:0 20px 40px rgba(0,0,0,0.3);
}

/* INPUTS */
input{
  width:100%;
  padding:10px;
  margin:8px 0;
  border-radius:10px;
  border:1px solid #ccc;
}

/* DROP ZONE */
#dropZone{
  border:2px dashed #9067C6;
  padding:15px;
  border-radius:12px;
  background:#EDF7F6;
  cursor:pointer;
  margin-top:10px;
}

#preview{
  width:100%;
  margin-top:10px;
  border-radius:10px;
  display:none;
}

/* BOTÓN DISABLED */
button:disabled{
  background:gray !important;
  cursor:not-allowed;
}
</style>
<link rel="stylesheet" href="../styles.css">
</head>
<body>
<h1>🛋️ Catálogo de Decoración PRO</h1>
<button class="btn-menu" onclick="location.href='../phpPaginas/bibliotecaAdmin.php'">Volver</button>
<?php if($esAdmin): ?>
<nav style="text-align:center; margin:15px;">
  <button class="btn" onclick="abrirModal()">➕ Agregar Material</button>
</nav>
<?php endif; ?>

<!-- 🛒 CATÁLOGO -->
<nav class="grid">

<?php while($m = mysqli_fetch_assoc($materiales)): ?>

<div class="card">

  <img src="<?= $m['imagen'] ?>">

  <h3><?= htmlspecialchars($m['nombre']) ?></h3>
  <p>$<?= $m['precio'] ?></p>

  <?php if($esAdmin): ?>
    <a href="?eliminar=<?= $m['id'] ?>">
      <button class="btn">❌ Eliminar</button>
    </a>
  <?php endif; ?>

</div>

<?php endwhile; ?>

</div>

<!-- 🪟 MODAL -->
<div class="modal" id="modal">
  <div class="modal-content">

    <form method="POST" enctype="multipart/form-data" id="formMaterial">

      <input type="text" name="nombre" id="nombre" placeholder="Nombre">
      <input type="number" name="precio" id="precio" placeholder="Precio">

      <!-- DRAG & DROP -->
      <div id="dropZone">
        📁 Arrastra la imagen o haz clic
        <input type="file" name="imagen" id="imagen" hidden>
      </div>

      <img id="preview">

      <br><br>

      <button class="btn" type="submit" name="agregar" id="btnGuardar" disabled>
        Guardar
      </button>

    </form>

  </div>
  </nav>

<script>
const modal = document.getElementById("modal");
const dropZone = document.getElementById("dropZone");
const input = document.getElementById("imagen");
const preview = document.getElementById("preview");

const nombre = document.getElementById("nombre");
const precio = document.getElementById("precio");
const btn = document.getElementById("btnGuardar");
const form = document.getElementById("formMaterial");

/* abrir modal */
function abrirModal(){
  modal.style.display = "block";
}

/* cerrar modal */
window.onclick = e=>{
  if(e.target == modal){
    modal.style.display = "none";
  }
}

/* click */
dropZone.onclick = ()=> input.click();

/* drag */
dropZone.ondragover = e=>{
  e.preventDefault();
}

/* drop */
dropZone.ondrop = e=>{
  e.preventDefault();
  input.files = e.dataTransfer.files;
  mostrar(e.dataTransfer.files[0]);
}

/* input file */
input.onchange = ()=> mostrar(input.files[0]);

function mostrar(file){
  const reader = new FileReader();
  reader.onload = e=>{
    preview.src = e.target.result;
    preview.style.display = "block";
  }
  reader.readAsDataURL(file);
  validar();
}

/* validación */
function validar(){
  if(nombre.value && precio.value && input.files.length>0){
    btn.disabled = false;
  }else{
    btn.disabled = true;
  }
}

nombre.oninput = validar;
precio.oninput = validar;
input.onchange = validar;
</script>

</body>
</html>