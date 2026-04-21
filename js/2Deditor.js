const espacio = document.getElementById("espacio");

let gridActivo = false;
let elementoSeleccionado = null;
let zIndexGlobal = 1;


/* ===================== ARRASTRAR + SNAP GRID ===================== */
function hacerArrastrable(elemento) {
    let isDragging = false;
    let dragOffsetX = 0;
    let dragOffsetY = 0;

    elemento.addEventListener("mousedown", (e) => {
        if (e.target.classList.contains("btn-eliminar")) return;

        elementoSeleccionado = elemento;
        elemento.style.zIndex = zIndexGlobal++;
        elemento.classList.add("color-activo");

        const espacioRect = espacio.getBoundingClientRect();
        dragOffsetX = e.clientX - espacioRect.left - parseInt(elemento.style.left);
        dragOffsetY = e.clientY - espacioRect.top - parseInt(elemento.style.top);

        isDragging = true;
    });

    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;

        const espacioRect = espacio.getBoundingClientRect();
        let x = e.clientX - espacioRect.left - dragOffsetX;
        let y = e.clientY - espacioRect.top - dragOffsetY;

        if (gridActivo) {
            const gridSize = 25;
            x = Math.round(x / gridSize) * gridSize;
            y = Math.round(y / gridSize) * gridSize;
        }

        x = Math.max(0, Math.min(espacioRect.width - elemento.offsetWidth, x));
        y = Math.max(0, Math.min(espacioRect.height - elemento.offsetHeight, y));

        elemento.style.left = x + "px";
        elemento.style.top = y + "px";
    });

    document.addEventListener("mouseup", () => {
        if (isDragging) {
            isDragging = false;
            if (elementoSeleccionado) {
                elementoSeleccionado.classList.remove("color-activo");
            }
        }
    });
}
/* ===================== CUADRÍCULA ===================== */
function toggleGrid() {
    gridActivo = !gridActivo;
    espacio.classList.toggle("grid-activa");
}

/* ===================== SISTEMA DE CAPAS ===================== */
function traerAlFrente() {
    if (elementoSeleccionado) elementoSeleccionado.style.zIndex = zIndexGlobal++;
}

function enviarAlFondo() {
    if (elementoSeleccionado) elementoSeleccionado.style.zIndex = 1;
}

/* ===================== GUARDAR DISEÑO ===================== */
function guardarProyecto() {
  document.getElementById("modalGuardar").style.display = "block";
}

function cerrarModal() {
  document.getElementById("modalGuardar").style.display = "none";
}

function confirmarGuardar() {
  const nombre = document.getElementById("nombreProyecto").value;
  if(!nombre) {
    alert("Debes poner un nombre.");
    return;
  }

  const espacio = document.getElementById("espacio"); // tu contenedor del plano
  html2canvas(espacio).then(canvas => {
    const imagen = canvas.toDataURL("image/png");

    fetch("../php/guardar_proyecto.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ nombre: nombre, imagen: imagen })
    })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      cerrarModal();
    });
  });
}

/* ===================== CARGAR DISEÑO ===================== */
function cargarDiseno() {
    const datos = JSON.parse(localStorage.getItem("disenoInterior"));
    if (!datos) return alert("No hay diseño guardado");

    espacio.innerHTML = "";

    datos.forEach(d => {
        crearElemento(d.tipo);
        const ultimo = espacio.lastChild;
        ultimo.style.left = d.left;
        ultimo.style.top = d.top;
        ultimo.style.zIndex = d.zIndex;
    });
}

/* ===================== CAMBIAR COLOR DE MUEBLES ===================== */
function cambiarColorElemento(color) {
    if (elementoSeleccionado) {
        const hijos = elementoSeleccionado.querySelectorAll("div");
        hijos.forEach(hijo => hijo.style.background = color);
    }
}

/* ===================== MEDIDOR EN METROS ===================== */
function mostrarMedidor() {
    const medidor = document.createElement("div");
    medidor.classList.add("medidor");
    medidor.style.width = "100px";
    medidor.style.left = "50px";
    medidor.style.top = "50px";
    medidor.textContent = "1m";
    espacio.appendChild(medidor);
}

// Objetos por temática
    const objetosPorTematica = {
      navidad: [
        {nombre: "Árbol de Navidad", img: "../assets/objetos/arbol-navidad.png"},
        {nombre: "Regalo", img: "../assets/objetos/regalo.png"},
        {nombre: "Guirnaldas", img: ""},
        {nombre: "Muñecos de nieve", img: "../assets/objetos/muñeco-nieve.png"},
        {nombre: "Esferas de nieve", img: "../assets/objetos/esfera-nieve.png"},
        {nombre: "Decoracion de puerta", img: ""}
      ],
      halloween: [
        {nombre: "Calabaza", img: "../assets/objetos/calabaza.png"},
        {nombre: "Fantasma", img: "../assets/objetos/fantasma.png"},
        {nombre: "Murcielagos", img: ""},
        {nombre: "Arañas", img: ""},
        {nombre: "Telarañas", img: ""},
        {nombre: "Sangre falsa", img: ""},
        {nombre: "Decoracion de puerta", img: ""}
      ],
      cumple: [
        {nombre: "Letreros de cumpleaños", img: "../assets/objetos/pastel.png"},
        {nombre: "Globos", img: "../assets/objetos/globos.png"},
        {nombre: "Mesas", img: ""},
        {nombre: "Manteles", img: ""},
        {nombre: "Cortina", img: ""},
        {nombre: "Globos de numeros", img: ""},
      ],
      sanvalentin: [
        {nombre: "Globos de corazon", img: "../assets/objetos/corazon.png"},
        {nombre: "Manteles", img: "../assets/objetos/rosa.png"},
        {nombre: "Adornos colgantes", img: ""}
      ],
      otros: [
        {nombre: "Planta", img: "../assets/objetos/planta.png"},
        {nombre: "Luces", img: "../assets/objetos/cuadro.png"},
        {nombre: "Adornos de escritorio", img: ""},
        {nombre: "Reloj", img: ""},
        {nombre: "Cuadro", img: ""},
        {nombre: "Tapete", img: ""}
      ]
    };

    // Mostrar objetos según temática
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

    // Colocar objeto en el área de diseño
    function colocarObjeto(imgSrc) {
      const espacio = document.getElementById("espacio");
      const nuevo = document.createElement("img");
      nuevo.src = imgSrc;
      nuevo.style.position = "absolute";
      nuevo.style.left = "50px";
      nuevo.style.top = "50px";
      nuevo.style.width = "100px";
      nuevo.style.cursor = "move";
      espacio.appendChild(nuevo);
    }

    // Mostrar/ocultar desplegable
    function toggleDropdown() {
      document.getElementById("listaObjetos").classList.toggle("show");
    }

    // Inicializar con temática por defecto
    mostrarObjetos();