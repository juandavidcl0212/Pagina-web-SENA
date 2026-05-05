<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Materiales</title>

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
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:20px;
  background:#11153a;
}

/* BOTÓN VOLVER */
.volver{
  background:var(--orange);
  border:none;
  padding:10px 15px;
  border-radius:10px;
  color:white;
  cursor:pointer;
  font-weight:bold;
}

/* CONTROLES */
.controls{
  display:flex;
  gap:10px;
}

/* BUSCADOR */
.search{
  padding:10px;
  border-radius:10px;
  border:none;
  width:200px;
}

/* SELECT */
select{
  padding:10px;
  border-radius:10px;
  border:none;
}

/* GRID */
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
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
  height:140px;
  object-fit:cover;
  border-radius:10px;
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

.usar{ background:var(--green); color:white;}
.info{ background:var(--purple); color:white;}

.btn:hover{
  opacity:0.85;
}

/* MODAL */
.modal{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.7);
  display:none;
  justify-content:center;
  align-items:center;
}

.modal-content{
  background:#1f245a;
  padding:20px;
  border-radius:15px;
  width:300px;
  text-align:center;
}

.close{
  background:#ff4d4d;
  color:white;
  border:none;
  padding:5px 10px;
  cursor:pointer;
  border-radius:5px;
}
</style>
</head>

<body>

<header>
  <button class="volver" onclick="window.location='biblioteca.php'">
    ⬅ Biblioteca
  </button>

  <h2>🧱 Materiales</h2>

  <div class="controls">
    <input type="text" id="buscar" class="search" placeholder="Buscar...">

    <select id="filtro">
      <option value="todos">Todos</option>
      <option value="madera">Madera</option>
      <option value="piedra">Piedra</option>
      <option value="tela">Tela</option>
      <option value="metal">Metal</option>
    </select>
  </div>
</header>

<div id="contenedor" class="grid"></div>

<!-- MODAL -->
<div id="modal" class="modal">
  <div class="modal-content">
    <h3 id="titulo"></h3>
    <p id="descripcion"></p>
    <button class="close" onclick="cerrarModal()">Cerrar</button>
  </div>
</div>

<script>

/* 🔥 LISTA DE MATERIALES */
const materiales = [
  {nombre:"Madera Roble", tipo:"madera", img:"../assets/materiales/roble.jpg", desc:"Madera resistente y elegante"},
  {nombre:"Mármol Blanco", tipo:"piedra", img:"../assets/materiales/marmol.jpg", desc:"Material premium para lujo"},
  {nombre:"Tela Lino", tipo:"tela", img:"../assets/materiales/lino.jpg", desc:"Suave y moderna"},
  {nombre:"Acero", tipo:"metal", img:"../assets/materiales/acero.jpg", desc:"Industrial y fuerte"},
  {nombre:"Madera Pino", tipo:"madera", img:"../assets/materiales/pino.jpg", desc:"Económica y ligera"}
];

const cont = document.getElementById("contenedor");

/* 🔥 RENDER */
function render(){
  cont.innerHTML = "";

  const texto = document.getElementById("buscar").value.toLowerCase();
  const filtro = document.getElementById("filtro").value;

  materiales.forEach(m => {

    if((filtro !== "todos" && m.tipo !== filtro) || 
       !m.nombre.toLowerCase().includes(texto)) return;

    const card = document.createElement("div");
    card.className = "card";

    card.innerHTML = `
      <img src="${m.img}">
      <h3>${m.nombre}</h3>

      <button class="btn usar" onclick="usarMaterial('${m.nombre}')">
        Aplicar
      </button>

      <button class="btn info" onclick="verInfo('${m.nombre}','${m.desc}')">
        Info
      </button>
    `;

    cont.appendChild(card);
  });
}

/* 🔥 USAR MATERIAL */
function usarMaterial(nombre){
  alert("Material aplicado: " + nombre);
  // 🔥 aquí luego lo conectas con el editor
}

/* 🔥 MODAL */
function verInfo(nombre,desc){
  document.getElementById("titulo").innerText = nombre;
  document.getElementById("descripcion").innerText = desc;
  document.getElementById("modal").style.display = "flex";
}

function cerrarModal(){
  document.getElementById("modal").style.display = "none";
}

/* EVENTOS */
document.getElementById("buscar").addEventListener("keyup", render);
document.getElementById("filtro").addEventListener("change", render);

/* INIT */
render();

</script>

</body>
</html>