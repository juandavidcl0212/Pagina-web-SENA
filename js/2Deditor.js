document.addEventListener("DOMContentLoaded", () => {

const espacio = document.getElementById("espacio");
const currentPath = window.location.pathname;
const rootPathIndex = currentPath.lastIndexOf('/html');
const rootPath = rootPathIndex !== -1 ? currentPath.substring(0, rootPathIndex) : '';

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
document.getElementById("btnFrente").onclick = traerAlFrente;
document.getElementById("btnFondo").onclick = enviarAlFondo;

/* 🔥 BOTÓN CARGAR (REDIRECCIÓN) */
const btnCargar = document.getElementById("btnCargar");
if(btnCargar){
  btnCargar.onclick = () => {
    window.location.href = rootPath + "/phpPaginas/mis_proyectos.php";
  };
}

/* ===================== OBJETOS ===================== */
const objetos = {
  navidad: [
    { nombre: "Árbol", img: "../assets/objetos/arbol-navidad.png" },
    { nombre: "Regalo", img: "../assets/objetos/regalo.png" }
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
  const cont = document.createElement("div");
  cont.classList.add("objeto");

  cont.style.left = "50px";
  cont.style.top = "50px";
  cont.style.width = "100px";
  cont.style.zIndex = zIndex++;

  const img = document.createElement("img");
  img.src = src;

  const btnEliminar = document.createElement("button");
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

/* ===================== GUARDAR ===================== */
function guardarProyecto() {
  const nombre = prompt("Nombre del proyecto:");
  if (!nombre) return;

  const objetosGuardar = [];

  document.querySelectorAll(".objeto").forEach(el => {
    objetosGuardar.push({
      img: el.querySelector("img").src,
      x: el.style.left,
      y: el.style.top,
      w: el.style.width,
      rot: el.dataset?.rot || 0
    });
  });

  fetch(rootPath + "/phpFunciones/guardar_proyecto.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      nombre,
      objetos: objetosGuardar,
      tipo: "2D"
    })
  })
  .then(res => res.text())
  .then(() => {
    alert("Proyecto guardado ✔");
  })
  .catch(() => alert("Error al guardar"));
}

/* ===================== CARGAR DESDE URL ===================== */
function cargarDesdeURL() {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  if (!id) return;

  fetch(rootPath + "/phpFunciones/cargar_proyectos.php?id=" + id)
    .then(res => res.json())
    .then(proyecto => {

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

        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = "×";
        btnEliminar.onclick = () => cont.remove();

        const resize = document.createElement("div");
        resize.className = "resize-handle";

        cont.appendChild(img);
        cont.appendChild(btnEliminar);
        cont.appendChild(resize);

        espacio.appendChild(cont);

        activarEventos(cont, resize);
      });

    })
    .catch(() => alert("Error al cargar"));
}

/* ===================== EXPORTAR PNG ===================== */
function exportarPNG() {
  html2canvas(espacio).then(canvas => {
    const link = document.createElement("a");
    link.download = "proyecto.png";
    link.href = canvas.toDataURL();
    link.click();
  });
}

/* ===================== EXPORTAR PDF ===================== */
function exportarPDF() {
  html2canvas(espacio).then(canvas => {
    const img = canvas.toDataURL("image/png");
    const pdf = new jspdf.jsPDF();
    pdf.addImage(img, 'PNG', 10, 10, 180, 100);
    pdf.save("proyecto.pdf");
  });
}

/* ===================== BOTONES EXPORT ===================== */
const btnPNG = document.getElementById("btnPNG");
if(btnPNG) btnPNG.onclick = exportarPNG;

const btnPDF = document.getElementById("btnPDF");
if(btnPDF) btnPDF.onclick = exportarPDF;

/* INIT */
mostrarObjetos();
cargarDesdeURL();

});