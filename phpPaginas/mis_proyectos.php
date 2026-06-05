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

/* 🔔 NOTIFICACIONES AJAX */
if(isset($_GET['notif'])){
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM project_messages pm
        JOIN proyectos p ON pm.proyecto_id = p.id
        WHERE p.usuario_id = ?
        AND pm.autor = 'admin'
        AND pm.visto = 0
    ");

    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    echo json_encode(["count"=>$count]);
    exit();
}

/* ELIMINAR PROYECTO */
if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);

    $stmt = $conn->prepare("DELETE FROM proyectos WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $stmt->close();

    header("Location: mis_proyectos.php");
    exit();
}

/* PROYECTOS */
$proyectos = [];

$stmt = $conn->prepare("
    SELECT id, nombre, data, fecha 
    FROM proyectos 
    WHERE usuario_id=? 
    ORDER BY fecha DESC
");

$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()){
    $parsed = json_decode($row['data'] ?? '{}', true);
    $row['tipo'] = strtoupper($parsed['tipo'] ?? '2D');
    $row['thumbnail'] = $parsed['thumbnail'] ?? '';
    $proyectos[] = $row;
}

$stmt->close();
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
  --purple:#9067C6;
  --orange:#FF9F1C;
}

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

/* CAMPANA */
.bell{
  position:relative;
  font-size:22px;
  cursor:pointer;
}

.bell span{
  position:absolute;
  top:-8px;
  right:-10px;
  background:red;
  color:white;
  border-radius:50%;
  padding:3px 6px;
  font-size:11px;
}

/* MODAL NOTIF */
#notifModal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.6);
}

#notifBox{
  background:#1B1F3B;
  width:420px;
  max-height:70vh;
  overflow:auto;
  margin:80px auto;
  padding:20px;
  border-radius:12px;
}

/* GRID */
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
  gap:20px;
  padding:20px;
}

.card{
  background:#151a4a;
  border-radius:15px;
  padding:15px;
}

/* PREVIEW */
.preview{
  width:100%;
  height:180px;
  border-radius:12px;
  background:#11153a;
  margin-bottom:10px;
  border:1px dashed rgba(255,255,255,0.2);
  overflow:hidden;
  display:grid;
  place-items:center;
  color:rgba(255,255,255,0.72);
  font-weight:700;
}

.preview img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}

/* BOTONES */
.btn{
  padding:8px 10px;
  border:none;
  border-radius:8px;
  cursor:pointer;
}

.ver{ background:var(--green); color:white;}
.eliminar{ background:red; color:white;}

.btn-panel{
  padding:10px 15px;
  border:none;
  border-radius:10px;
  color:white;
  cursor:pointer;
}

.instrucciones{ background:var(--purple);}
.asesoria{ background:var(--green);}

.panel{
  display:none;
  margin:20px;
  padding:20px;
  background:#2a2f5c;
  border-radius:10px;
}

/* 🔥 FORMULARIO MEJORADO */
.form-wrapper{
  display:flex;
  justify-content:center;
}

.form-pro{
  width:420px;
  display:flex;
  flex-direction:column;
  gap:12px;
  background:#1b1f3b;
  padding:18px;
  border-radius:14px;
  box-shadow:0 10px 25px rgba(0,0,0,0.35);
}

.form-pro select,
.form-pro textarea,
.form-pro input{
  width:100%;
  padding:1px;
  border-radius:10px;
  border:none;
  background:#11153a;
  color:white;
  outline:none;
}

.form-pro textarea{
  min-height:160px;        /* antes 100px */
  resize:none;
  font-size:14px;
  line-height:1.4;
  padding:1px;
}

.form-pro textarea:focus{
  outline:2px solid #FF9F1C;
  background:#0f1230;
}

.form-pro button{
  background:#FF9F1C;
  color:#111827;
  border:none;
  padding:14px;
  border-radius:12px;
  font-weight:bold;
  cursor:pointer;
  transition:0.2s;
}

.form-pro button:hover{
  transform:scale(1.03);
}

/* BUSCADOR */
#buscar{
  margin:20px;
  padding:12px;
  border-radius:10px;
  border:none;
  width:400px;
  background:#11153a;
  color:white;
}
</style>

<script>

/* NOTIFICACIONES EN TIEMPO REAL */
function cargarNotificaciones(){
    fetch("mis_proyectos.php?notif=1")
    .then(r=>r.json())
    .then(d=>{
        let bell=document.getElementById("bellCount");
        if(d.count>0){
            bell.style.display="block";
            bell.innerText=d.count;
        }else{
            bell.style.display="none";
        }
    });
}

setInterval(cargarNotificaciones,5000);
document.addEventListener("DOMContentLoaded",cargarNotificaciones);

/* MODAL NOTIF */
function abrirNotif(){
    document.getElementById("notifModal").style.display="block";
}

function cerrarNotif(){
    document.getElementById("notifModal").style.display="none";
}

/* PANELES */
function mostrarInstrucciones(){
    let p=document.getElementById("instruccionesPanel");
    p.style.display=p.style.display==="block"?"none":"block";
}

function mostrarAsesoria(){
    let p=document.getElementById("asesoriaPanel");
    p.style.display=p.style.display==="block"?"none":"block";
}

/* CANVAS FIX */
function renderThumbnails(){
    document.querySelectorAll('.preview').forEach(preview=>{
        if (preview.dataset.thumbnail) {
            preview.innerHTML = `<img src="${preview.dataset.thumbnail}" alt="Vista previa del proyecto">`;
            return;
        }

        let data=preview.dataset.preview;
        let proyecto;

        try{
            proyecto=JSON.parse(data||'{}');
        }catch(e){
            preview.textContent="Sin preview";
            return;
        }

        if ((proyecto.tipo || '').toUpperCase() === '3D') {
            preview.textContent="Proyecto 3D";
            return;
        }

        let objetos=Array.isArray(proyecto.objetos) ? proyecto.objetos : [];

        if(!Array.isArray(objetos)||objetos.length===0){
            preview.textContent="Sin preview";
            return;
        }

        let card=preview.closest('.card');
        let canvas=document.createElement('canvas');
        canvas.width=300;
        canvas.height=200;
        let ctx=canvas.getContext('2d');

        ctx.fillStyle="#11153a";
        ctx.fillRect(0,0,canvas.width,canvas.height);

        let loaded=0;

        objetos.forEach(obj=>{
            if (!obj || !obj.img) {
                loaded++;
                return;
            }
            let img=new Image();
            img.src=obj.img;

            img.onload=()=>{
                const x=parseFloat(obj.x)||0;
                const y=parseFloat(obj.y)||0;
                const w=parseFloat(obj.w)||100;
                ctx.drawImage(img,x*0.2,y*0.2,w*0.2,w*0.2);
                loaded++;

                if(loaded===objetos.length){
                    let dataURL=canvas.toDataURL();
                    card.style.backgroundImage=`url(${dataURL})`;
                    preview.style.display="none";
                }
            };

            img.onerror=()=>loaded++;
        });
    });
}

document.addEventListener("DOMContentLoaded",renderThumbnails);

</script>

</head>

<body>

<header>
<h2>📁 Mis Proyectos</h2>

<div class="bell" onclick="abrirNotif()">
🔔
<span id="bellCount" style="display:none;">0</span>
</div>
</header>

<!-- NOTIFICACIONES -->
<div id="notifModal" onclick="cerrarNotif()">
<div id="notifBox" onclick="event.stopPropagation()">

<h3>🔔 Notificaciones</h3>

<?php
$stmt=$conn->prepare("
SELECT pm.mensaje,p.nombre
FROM project_messages pm
JOIN proyectos p ON pm.proyecto_id=p.id
WHERE p.usuario_id=?
AND pm.autor='admin'
AND pm.visto=0
ORDER BY pm.id DESC
LIMIT 10
");

$stmt->bind_param("i",$usuario_id);
$stmt->execute();
$res=$stmt->get_result();

while($n=$res->fetch_assoc()):
?>

<div style="padding:10px;border-bottom:1px solid #333;">
📌 <b><?php echo $n['nombre'];?></b><br>
<?php echo $n['mensaje'];?>
</div>

<?php endwhile;$stmt->close();?>

</div>
</div>

<!-- BOTONES -->
<div style="margin:20px;display:flex;gap:12px;">
<button onclick="mostrarInstrucciones()" class="btn-panel instrucciones">📘 Instrucciones</button>
<button onclick="mostrarAsesoria()" class="btn-panel asesoria">🧑‍💻 Solicitar asesoría</button>
</div>

<!-- INSTRUCCIONES (TU TEXTO ORIGINAL) -->
<div id="instruccionesPanel" class="panel">
<h3>¿Qué es esto?</h3>
<p>
Aquí puedes enviar tu proyecto al administrador para recibir recomendaciones y orientación personalizada.
Selecciona tu diseño, describe tus dudas y espera la respuesta del equipo.
</p>
</div>
<input type="text" id="buscar" placeholder="Buscar...">
<!-- FORMULARIO -->
<div id="asesoriaPanel" class="panel">

<div class="form-wrapper">

<form class="form-pro" action="../phpFunciones/subir_archivo_proyecto.php" method="post" enctype="multipart/form-data">

<select name="project_id" required>
<option value="">Selecciona proyecto</option>

<?php foreach($proyectos as $p): ?>
<option value="<?php echo $p['id'];?>">
<?php echo $p['nombre'];?>
</option>
<?php endforeach;?>
</select>

<textarea name="comentario" placeholder="Escribe tu mensaje..." required></textarea>

<input type="file" name="archivo">

<button type="submit">Enviar solicitud</button>

</form>

</div>

</div>

<!-- PROYECTOS -->
<div class="grid">

<?php foreach($proyectos as $row): ?>

<div class="card">

<div class="preview" data-preview='<?php echo htmlspecialchars($row['data'],ENT_QUOTES);?>' data-thumbnail="<?php echo htmlspecialchars($row['thumbnail'] ?? '', ENT_QUOTES); ?>"></div>

<h3><?php echo htmlspecialchars($row['nombre']);?></h3>
<p style="margin:0 0 10px;color:#cbd5e1;font-size:13px;">Proyecto <?php echo htmlspecialchars($row['tipo']); ?></p>

<?php if ($row['tipo'] === '3D'): ?>
<a class="btn ver" href="modelos_3d.php" style="text-decoration:none;display:inline-block;">Abrir 3D</a>
<?php else: ?>
<a class="btn ver" href="../html/DISEÑO2D.html?id=<?php echo intval($row['id']); ?>" style="text-decoration:none;display:inline-block;">Abrir 2D</a>
<?php endif; ?>
<a class="btn eliminar" href="mis_proyectos.php?eliminar=<?php echo intval($row['id']); ?>" onclick="return confirm('¿Eliminar este proyecto?')" style="text-decoration:none;display:inline-block;">Eliminar</a>

</div>

<?php endforeach; ?>

</div>

</body>
</html>
