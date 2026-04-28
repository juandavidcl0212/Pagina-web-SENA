const espacio = document.getElementById("espacio");

let gridActivo = false;
let elementoSeleccionado = null;
let zIndexGlobal = 1;

/* ===================== ARRASTRAR ===================== */
function hacerArrastrable(elemento) {
  let isDragging = false;
  let offsetX = 0;
  let offsetY = 0;

  elemento.addEventListener("mousedown", (e) => {
    if (e.target.classList.contains("resize-handle") || e.target.classList.contains("rotate-handle")) return;

    elementoSeleccionado = elemento;
    elemento.style.zIndex = zIndexGlobal++;
    elemento.classList.add("activo");

    offsetX = e.offsetX;
    offsetY = e.offsetY;

    isDragging = true;
  });

  document.addEventListener("mousemove", (e) => {
    if (!isDragging) return;

    const rect = espacio.getBoundingClientRect();

    let x = e.clientX - rect.left - offsetX;
    let y = e.clientY - rect.top - offsetY;

    if (gridActivo) {
      const grid = 25;
      x = Math.round(x / grid) * grid;
      y = Math.round(y / grid) * grid;
    }

    elemento.style.left = x + "px";
    elemento.style.top = y + "px";
  });

  document.addEventListener("mouseup", () => {
    isDragging = false;
  });
}

/* ===================== REDIMENSIONAR ===================== */
function agregarResize(elemento) {
  const handle = document.createElement("div");
  handle.classList.add("resize-handle");
  elemento.appendChild(handle);

  let isResizing = false;

  handle.addEventListener("mousedown", (e) => {
    e.stopPropagation();
    isResizing = true;
  });

  document.addEventListener("mousemove", (e) => {
    if (!isResizing) return;

    let width = e.offsetX;
    let height = e.offsetY;

    elemento.style.width = width + "px";
    elemento.style.height = height + "px";
  });

  document.addEventListener("mouseup", () => {
    isResizing = false;
  });
}

/* ===================== ROTAR ===================== */
function agregarRotacion(elemento) {
  const handle = document.createElement("div");
  handle.classList.add("rotate-handle");
  elemento.appendChild(handle);

  let rotando = false;

  handle.addEventListener("mousedown", (e) => {
    e.stopPropagation();
    rotando = true;
  });

  document.addEventListener("mousemove", (e) => {
    if (!rotando) return;

    const rect = elemento.getBoundingClientRect();
    const centroX = rect.left + rect.width / 2;
    const centroY = rect.top + rect.height / 2;

    const angulo = Math.atan2(e.clientY - centroY, e.clientX - centroX);
    const grados = angulo * (180 / Math.PI);

    elemento.style.transform = `rotate(${grados}deg)`;
  });

  document.addEventListener("mouseup", () => {
    rotando = false;
  });
}

/* ===================== GRID ===================== */
function toggleGrid() {
  gridActivo = !gridActivo;
  espacio.classList.toggle("grid-activa");
}

/* ===================== CAPAS ===================== */
function traerAlFrente() {
  if (elementoSeleccionado) {
    elementoSeleccionado.style.zIndex = zIndexGlobal++;
  }
}

function enviarAlFondo() {
  if (elementoSeleccionado) {
    elementoSeleccionado.style.zIndex = 1;
  }
}

/* ===================== OBJETOS ===================== */
const objetosPorTematica = {
  navidad: [
    { nombre: "Árbol", img: "../assets/objetos/arbol-navidad.png" },
    { nombre: "Regalo", img: "../assets/objetos/regalo.png" }
  ],
  halloween: [
    { nombre: "Calabaza", img: "../assets/objetos/calabaza.png" }
  ],
  cumple: [
    { nombre: "Pastel", img: "../assets/objetos/pastel.png" }
  ],
  sanvalentin: [
    { nombre: "Corazón", img: "../assets/objetos/corazon.png" }
  ],
  otros: [
    { nombre: "Planta", img: "../assets/objetos/planta.png" }
  ]
};

function mostrarObjetos() {
  const tematica = document.getElementById("tematicaSelect").value;
  const lista = document.getElementById("listaObjetos");
  lista.innerHTML = "";

  objetosPorTematica[tematica].forEach(obj => {
    const btn = document.createElement("button");
    btn.textContent = obj.nombre;
    btn.onclick = () => colocarObjeto(obj.img);
    lista.appendChild(btn);
  });
}

/* ===================== CREAR OBJETO ===================== */
function colocarObjeto(imgSrc) {
  if (!imgSrc) return alert("Objeto sin imagen");

  const contenedor = document.createElement("div");
  contenedor.style.position = "absolute";
  contenedor.style.left = "50px";
  contenedor.style.top = "50px";

  const img = document.createElement("img");
  img.src = imgSrc;
  img.style.width = "100px";
  img.style.display = "block";

  contenedor.appendChild(img);
  espacio.appendChild(contenedor);

  hacerArrastrable(contenedor);
  agregarResize(contenedor);
  agregarRotacion(contenedor);
}

/* ===================== DROPDOWN ===================== */
function toggleDropdown() {
  document.getElementById("listaObjetos").classList.toggle("show");
}

/* ===================== INIT ===================== */
mostrarObjetos();
// =========================
// CONTROL DE ACCESO (PRUEBA GRATIS)
// =========================

const usuario = sessionStorage.getItem("usuario");
const pruebaUsada = sessionStorage.getItem("prueba_usada");

// Si no está logueado
if (!usuario) {
  alert("Debes iniciar sesión");
  window.location.href = "../phpPaginas/inSesion.php";
}

// Si ya usó la prueba
if (pruebaUsada === "1") {
  alert("Ya usaste tu prueba gratuita");
  window.location.href = "../phpPaginas/planes.php";
}