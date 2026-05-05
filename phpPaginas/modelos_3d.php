<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proyectos 3D</title>

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
  grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
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

/* PREVIEW 3D */
.preview{
  width:100%;
  height:180px;
  background:#0f1230;
  border-radius:10px;
  display:flex;
  justify-content:center;
  align-items:center;
  overflow:hidden;
}

/* IMG */
.preview img{
  width:100%;
  object-fit:cover;
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

.ver{ background:var(--green); color:white;}
.editar{ background:var(--purple); color:white;}
.eliminar{ background:#ff4d4d; color:white;}

.btn:hover{
  opacity:0.8;
}

/* TEXTO VACÍO */
.vacio{
  text-align:center;
  margin-top:50px;
  opacity:0.6;
}
</style>
</head>

<body>

<header>
  <button class="volver" onclick="window.location='biblioteca.php'">
    ⬅ Biblioteca
  </button>

  <h2>🧊 Proyectos 3D</h2>

  <input type="text" id="buscar" class="search" placeholder="Buscar proyecto...">
</header>

<div id="contenedor" class="grid"></div>

<p id="vacio" class="vacio" style="display:none;">
  No tienes proyectos 3D aún 🚀
</p>

<script>

/* 🔥 CARGAR PROYECTOS 3D */
fetch("/prueba_db/phpFunciones/cargar_proyectos.php")
  .then(res => res.json())
  .then(data => {

    const cont = document.getElementById("contenedor");
    const vacio = document.getElementById("vacio");

    // filtrar solo 3D
    const proyectos = data.filter(p => p.tipo === "3D");

    if(proyectos.length === 0){
      vacio.style.display = "block";
      return;
    }

    proyectos.forEach(p => {

      const card = document.createElement("div");
      card.className = "card";
      card.dataset.nombre = p.nombre.toLowerCase();

      card.innerHTML = `
        <div class="preview">
          <img src="../assets/preview3d.png">
        </div>

        <h3>${p.nombre}</h3>

        <button class="btn ver" onclick="ver(${p.id})">Ver</button>
        <button class="btn editar" onclick="editar(${p.id})">Editar</button>
        <button class="btn eliminar" onclick="eliminar(${p.id})">Eliminar</button>
      `;

      cont.appendChild(card);
    });

  });

/* 🔍 BUSCAR */
document.getElementById("buscar").addEventListener("keyup", function(){
  const filtro = this.value.toLowerCase();

  document.querySelectorAll(".card").forEach(card=>{
    card.style.display = card.dataset.nombre.includes(filtro) ? "block" : "none";
  });
});

/* ACCIONES */
function ver(id){
  window.location = "editor3d.php?id=" + id;
}

function editar(id){
  window.location = "editor3d.php?id=" + id;
}

function eliminar(id){
  if(confirm("¿Eliminar proyecto?")){
    window.location = "/prueba_db/phpPaginas/mis_proyectos.php?eliminar=" + id;
  }
}

</script>

</body>
</html>