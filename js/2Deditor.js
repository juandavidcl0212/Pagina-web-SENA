document.addEventListener("DOMContentLoaded", () => {

const espacio = document.getElementById("espacio");

let elementoSeleccionado = null;
let isDragging = false;
let offsetX = 0;
let offsetY = 0;
let zIndex = 1;
let gridActivo = false;
const gridSize = 25;

/* ===================== BOTONES ===================== */
document.getElementById("btnGrid").onclick = toggleGrid;
document.getElementById("btnGuardar").onclick = guardarProyecto;
document.getElementById("btnCargar").onclick = cargarDiseno;
document.getElementById("btnFrente").onclick = traerAlFrente;
document.getElementById("btnFondo").onclick = enviarAlFondo;

/* ===================== OBJETOS ===================== */
const objetos = {
  navidad: [
    { nombre: "Árbol", img: "../assets/objetos/arbol-navidad.png" },
    { nombre: "Regalo", img: "../assets/objetos/regalo.png" },
    { nombre: "Muñeco nieve", img: "../assets/objetos/muñeco-nieve.png" }
  ],
  halloween: [
    { nombre: "Calabaza", img: "../assets/objetos/calabaza.png" },
    { nombre: "Fantasma", img: "../assets/objetos/fantasma.png" }
  ],
  cumple: [
    { nombre: "Pastel", img: "../assets/objetos/pastel.png" },
    { nombre: "Globos", img: "../assets/objetos/globos.png" }
  ],
  sanvalentin: [
    { nombre: "Corazón", img: "../assets/objetos/corazon.png" }
  ],
  otros: [
    { nombre: "Planta", img: "../assets/objetos/planta.png" }
  ]
};

/* ===================== MOSTRAR OBJETOS ===================== */
window.mostrarObjetos = () => {
  const tipo = document.getElementById("tematicaSelect").value;
  const lista = document.getElementById("listaObjetos");
  lista.innerHTML = "";

  objetos[tipo].forEach(obj => {
    const btn = document.createElement("button");
    btn.textContent = obj.nombre;
    btn.onclick = () => crearObjeto(obj.img);
    lista.appendChild(btn);
  });
};

/* ===================== CREAR OBJETO ===================== */
function crearObjeto(src) {
  if (!src) return alert("Objeto sin imagen");

  const cont = document.createElement("div");
  cont.classList.add("objeto");

  cont.style.left = "50px";
  cont.style.top = "50px";
  cont.style.width = "100px";
  cont.style.zIndex = zIndex++;

  const img = document.createElement("img");
  img.src = src;

  const btnEliminar = document.createElement("button");
  btnEliminar.className = "btn-eliminar";
  btnEliminar.textContent = "×";
  btnEliminar.onclick = () => cont.remove();

  const resize = document.createElement("div");
  resize.className = "resize-handle";

  cont.appendChild(img);
  cont.appendChild(btnEliminar);
  cont.appendChild(resize);

  espacio.appendChild(cont);

  activarEventos(cont, resize);
}

/* ===================== EVENTOS ===================== */
function activarEventos(el, resize) {

  el.addEventListener("mousedown", (e) => {
    elementoSeleccionado = el;
    el.style.zIndex = zIndex++;
    isDragging = true;

    offsetX = e.offsetX;
    offsetY = e.offsetY;
  });

  document.addEventListener("mousemove", (e) => {
    if (!isDragging || !elementoSeleccionado) return;

    let x = e.clientX - espacio.offsetLeft - offsetX;
    let y = e.clientY - espacio.offsetTop - offsetY;

    if (gridActivo) {
      x = Math.round(x / gridSize) * gridSize;
      y = Math.round(y / gridSize) * gridSize;
    }

    elementoSeleccionado.style.left = x + "px";
    elementoSeleccionado.style.top = y + "px";
  });

  document.addEventListener("mouseup", () => {
    isDragging = false;
  });

  el.addEventListener("wheel", (e) => {
    e.preventDefault();
    let rot = el.dataset.rot || 0;
    rot = parseInt(rot) + (e.deltaY > 0 ? 10 : -10);
    el.style.transform = `rotate(${rot}deg)`;
    el.dataset.rot = rot;
  });

  resize.addEventListener("mousedown", (e) => {
    e.stopPropagation();

    const startX = e.clientX;
    const startWidth = el.offsetWidth;

    function mover(e2) {
      let size = startWidth + (e2.clientX - startX);
      el.style.width = size + "px";
    }

    function stop() {
      document.removeEventListener("mousemove", mover);
      document.removeEventListener("mouseup", stop);
    }

    document.addEventListener("mousemove", mover);
    document.addEventListener("mouseup", stop);
  });
}

/* ===================== FUNCIONES ===================== */
function toggleGrid() {
  gridActivo = !gridActivo;
  espacio.classList.toggle("grid-activa");
}

function traerAlFrente() {
  if (elementoSeleccionado) elementoSeleccionado.style.zIndex = zIndex++;
}

function enviarAlFondo() {
  if (elementoSeleccionado) elementoSeleccionado.style.zIndex = 1;
}

/* ===================== GUARDAR (DB REAL) ===================== */
function guardarProyecto() {
  const nombre = prompt("Nombre del proyecto:");
  if (!nombre) return;

  const objetos = [];

  document.querySelectorAll(".objeto").forEach(el => {
    objetos.push({
      img: el.querySelector("img").src,
      x: el.style.left,
      y: el.style.top,
      w: el.style.width,
      rot: el.dataset.rot || 0
    });
  });

  fetch("../php/guardar_proyecto.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      nombre,
      objetos,
      tipo: "2D"
    })
  })
  .then(res => res.text())
  .then(msg => alert(msg));
}

/* ===================== CARGAR ===================== */
function cargarDiseno() {
  fetch("../php/cargar_proyectos.php")
    .then(res => res.json())
    .then(data => {

      if (data.length === 0) return alert("No hay proyectos");

      const lista = data.map(p => `${p.id} - ${p.nombre} (${p.tipo})`).join("\n");
      const id = prompt("Elige ID:\n" + lista);

      const proyecto = data.find(p => p.id == id);
      if (!proyecto) return;

      espacio.innerHTML = "";

      const objetos = JSON.parse(proyecto.datos);

      objetos.forEach(obj => {
        const cont = document.createElement("div");
        cont.classList.add("objeto");

        cont.style.left = obj.x;
        cont.style.top = obj.y;
        cont.style.width = obj.w;
        cont.style.transform = `rotate(${obj.rot}deg)`;
        cont.dataset.rot = obj.rot;

        const img = document.createElement("img");
        img.src = obj.img;

        const resize = document.createElement("div");
        resize.className = "resize-handle";

        cont.appendChild(img);
        cont.appendChild(resize);

        espacio.appendChild(cont);

        activarEventos(cont, resize);
      });

    });
}

/* INIT */
mostrarObjetos();

});