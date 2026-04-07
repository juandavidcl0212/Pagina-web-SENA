const espacio = document.getElementById("espacio");

let gridActivo = false;
let elementoSeleccionado = null;
let zIndexGlobal = 1;

/* ===================== CREAR ELEMENTOS ===================== */
function crearElemento(tipo) {
    const elemento = document.createElement("nav");
    elemento.classList.add("mueble", tipo);

    // Asignar tamaño inicial según el tipo
    switch(tipo){
        case "sofa": elemento.style.width="150px"; elemento.style.height="80px"; break;
        case "cama": elemento.style.width="160px"; elemento.style.height="100px"; break;
        case "silla": elemento.style.width="40px"; elemento.style.height="80px"; break;
        case "mesa": elemento.style.width="110px"; elemento.style.height="110px"; break;
        case "mesanoche": elemento.style.width="60px"; elemento.style.height="60px"; break;
        case "planta": elemento.style.width="60px"; elemento.style.height="100px"; break;
        case "alfombra": elemento.style.width="150px"; elemento.style.height="100px"; break;
        case "reloj": elemento.style.width="40px"; elemento.style.height="40px"; break;
        case "estante": elemento.style.width="120px"; elemento.style.height="50px"; break;
        case "cuadro": elemento.style.width="80px"; elemento.style.height="60px"; break;
        case "cortina": elemento.style.width="80px"; elemento.style.height="150px"; break;
        case "ventana": elemento.style.width="100px"; elemento.style.height="80px"; break;
        case "pared": elemento.style.width="200px"; elemento.style.height="20px"; break;
    }

    // Crear hijos según tipo
    switch(tipo) {
        case "sofa":
            elemento.innerHTML = `
                <div class="sofa-base"></div>
                <div class="sofa-respaldo"></div>
                <div class="sofa-cojin" style="left:20px;"></div>
                <div class="sofa-cojin" style="right:20px;"></div>
            `;
            break;

        case "cama":
            elemento.innerHTML = `
                <div class="cama-base"></div>
                <div class="cama-almohada"></div>
            `;
            break;

        case "silla":
            elemento.innerHTML = `
                <div class="silla-asiento"></div>
                <div class="silla-respaldo"></div>
                <div class="silla-pata1"></div>
                <div class="silla-pata2"></div>
            `;
            break;

        case "mesa":
            elemento.innerHTML = `<div class="mesa-tabla"></div>`;
            break;

        case "planta":
            elemento.innerHTML = `
                <div class="planta-hojas"></div>
                <div class="planta-maceta"></div>
            `;
            break;

        case "mesanoche":
            elemento.innerHTML = `
                <div class="mesanoche-superficie"></div>
                <div class="mesanoche-pata1"></div>
                <div class="mesanoche-pata2"></div>
            `;
            break;

        case "alfombra":
        case "reloj":
        case "estante":
        case "cuadro":
        case "cortina":
        case "ventana":
        case "pared":
            elemento.innerHTML = `<div class="${tipo}"></div>`;
            break;
    }

    // Posición absoluta dentro del contenedor
  elemento.style.position = "absolute";

  // Coordenadas iniciales aleatorias dentro del espacio
  const espacioRect = espacio.getBoundingClientRect();
  const x = Math.floor(Math.random() * (espacioRect.width - 80));
  const y = Math.floor(Math.random() * (espacioRect.height - 80));
  elemento.style.left = x + "px";
  elemento.style.top = y + "px";

    // Botón eliminar
    const btnEliminar = document.createElement("button");
    btnEliminar.textContent="✖";
    btnEliminar.classList.add("btn-eliminar");
    btnEliminar.onclick = () => elemento.remove();
    elemento.appendChild(btnEliminar);

    // Agregar al espacio y hacerlo arrastrable
    espacio.appendChild(elemento);
    hacerArrastrable(elemento);
}

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

function mostrarObjetos() {
  const menu = document.getElementById("menuPopup");
  menu.classList.toggle("show");
}