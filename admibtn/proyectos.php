<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proyectos - Admin</title>

<link rel="stylesheet" href="../CSS/proyectos.css">
<link rel="stylesheet" href="../styles.css">

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
  font-family:Segoe UI;
  background:var(--light);
  margin:0;
  padding:0;
}

/* HEADER */
header{
  background:var(--dark);
  color:white;
  padding:20px;
  text-align:center;
  font-size:22px;
}

/* CONTENEDOR */
.container{
  max-width:800px;
  margin:30px auto;
  padding:20px;
}

/* CARD FORM */
.proyecto-form{
  background:white;
  padding:20px;
  border-radius:15px;
  box-shadow:0 10px 25px rgba(0,0,0,0.1);
  margin-bottom:20px;
}

.proyecto-form h2{
  margin-bottom:10px;
}

/* INPUT */
input{
  width:70%;
  padding:12px;
  border:2px solid #eee;
  border-radius:10px;
  outline:none;
  transition:0.3s;
}

input:focus{
  border-color:var(--purple);
  box-shadow:0 0 8px rgba(144,103,198,0.4);
}

/* BOTÓN */
button{
  padding:12px 15px;
  border:none;
  border-radius:10px;
  cursor:pointer;
  background:linear-gradient(135deg,var(--orange),var(--purple));
  color:white;
  font-weight:bold;
  transition:0.3s;
}

button:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 20px rgba(0,0,0,0.2);
}

/* LISTA */
.lista-proyectos{
  list-style:none;
  padding:0;
}

/* ITEM */
.proyecto{
  background:white;
  margin-bottom:10px;
  padding:15px;
  border-radius:12px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  box-shadow:0 5px 15px rgba(0,0,0,0.08);
  transition:0.3s;
}

.proyecto:hover{
  transform:scale(1.01);
}

/* TEXTO */
.proyecto span{
  font-weight:bold;
}

/* BOTONES INTERNOS */
.proyecto button{
  margin-left:5px;
  padding:8px 10px;
  border-radius:8px;
  background:var(--orange);
}

/* COMPLETADO */
.proyecto.completado{
  background:var(--green);
  color:white;
  text-decoration:line-through;
}

/* ANIMACIÓN */
.proyecto{
  animation:fade 0.3s ease;
}

@keyframes fade{
  from{opacity:0; transform:translateY(10px);}
  to{opacity:1; transform:translateY(0);}
}

/* RESPONSIVE */
@media(max-width:600px){
  input{
    width:100%;
    margin-bottom:10px;
  }
}
</style>

</head>
<body>

<header>
  🛋️ Panel de Proyectos - Decoración Interior
</header>

<div class="container">

<!-- FORM -->
<div class="proyecto-form">
  <h2>➕ Agregar nuevo proyecto</h2>

  <input type="text" id="nombreProyecto" placeholder="Nombre del proyecto">
  <button onclick="agregarProyecto()">Agregar</button>
</div>

<!-- LISTA -->
<h2>📋 Lista de proyectos</h2>
<ul id="listaProyectos" class="lista-proyectos"></ul>

</div>

<script>
const lista = document.getElementById("listaProyectos");

/* CARGAR */
window.onload = () => {
  const data = JSON.parse(localStorage.getItem("proyectos")) || [];
  data.forEach(p => render(p.nombre, p.completado));
};

/* AGREGAR */
function agregarProyecto(){
  const input = document.getElementById("nombreProyecto");
  const nombre = input.value.trim();

  if(!nombre){
    alert("Escribe un proyecto");
    return;
  }

  render(nombre,false);
  guardar();
  input.value="";
}

/* RENDER */
function render(nombre,estado){
  const li = document.createElement("li");
  li.className="proyecto";
  if(estado) li.classList.add("completado");

  li.innerHTML=`
    <span>${nombre}</span>
    <div>
      <button onclick="toggle(this)">✔</button>
      <button onclick="eliminar(this)">✖</button>
    </div>
  `;

  lista.appendChild(li);
}

/* COMPLETAR */
function toggle(btn){
  const item = btn.closest(".proyecto");
  item.classList.toggle("completado");
  guardar();
}

/* ELIMINAR */
function eliminar(btn){
  btn.closest(".proyecto").remove();
  guardar();
}

/* GUARDAR */
function guardar(){
  const data=[];
  document.querySelectorAll(".proyecto").forEach(p=>{
    data.push({
      nombre:p.querySelector("span").textContent,
      completado:p.classList.contains("completado")
    });
  });

  localStorage.setItem("proyectos",JSON.stringify(data));
}
</script>

</body>
</html>