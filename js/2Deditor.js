const espacio = document.getElementById("espacio");

let gridActivo = false;
let elementoSeleccionado = null;
let zIndexGlobal = 1;

// Objetos por temática
    const objetosPorTematica = {
      navidad: [
        {id: "arbol-navidad", nombre: "Árbol de Navidad", img: "../assets/objetos/arbol-navidad.png", ancho: 120, alto: 180, posX: 100, posY: 150},
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

// Colocar objeto en el espacio de diseño

// Función para colocar objetos en la interfaz 2D
function colocarObjeto2D(obj) {
    const contenedor = document.getElementById("espacio"); // contenedor principal en HTML

    // Generar posición aleatoria dentro del contenedor
    const maxX = contenedor.offsetWidth - obj.ancho;
    const maxY = contenedor.offsetHeight - obj.alto;
    const posX = Math.floor(Math.random() * maxX);
    const posY = Math.floor(Math.random() * maxY);

    // Crear elemento imagen
    const img = document.createElement("img");
    img.src = obj.img;
    img.alt = obj.nombre;
    img.style.width = obj.ancho + "px";
    img.style.height = obj.alto + "px";
    img.style.position = "absolute";
    img.style.left = posX + "px";
    img.style.top = posY + "px";

    // Insertar en el contenedor
    contenedor.appendChild(img);
}

// Generar lista en la barra lateral
function cargarObjetos2D() {
    for (const categoria in objetosPorTematica) {
        const contenedor = document.getElementById(categoria);
        objetosPorTematica[categoria].forEach(obj => {
            const div = document.createElement("div");
            div.className = "objeto";
            div.innerHTML = `<img src="${obj.img}" width="40" height="40"><span>${obj.nombre}</span>`;
            div.onclick = () => colocarObjeto2D(obj);
            contenedor.appendChild(div);
        });
    }
}
cargarObjetos2D();



// Inicializar con temática por defecto
mostrarObjetos();
    function toggleDropdown() {
  const menu = document.getElementById("listaObjetos");
  menu.classList.toggle("show");
}



/* ===================== ARRASTRAR + SNAP GRID ===================== */
function hacerArrastrable(elemento) {
    let isDragging = false;
    let offsetX = 0;
    let offsetY = 0;

    // Al presionar el mouse sobre el objeto
    elemento.addEventListener("mousedown", (e) => {
        if (e.target.classList.contains("btn-eliminar")) return; // no arrastrar si se hace clic en eliminar

        isDragging = true;
        elementoSeleccionado = elemento;
        elemento.style.zIndex = ++zIndexGlobal;

        // Calcular diferencia entre posición del mouse y el objeto
        const rect = elemento.getBoundingClientRect();
        offsetX = e.clientX - rect.left;
        offsetY = e.clientY - rect.top;

        // Evitar que el navegador seleccione texto
        e.preventDefault();
    });

    // Movimiento mientras se arrastra
    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;

        const espacioRect = espacio.getBoundingClientRect();
        let x = e.clientX - espacioRect.left - offsetX;
        let y = e.clientY - espacioRect.top - offsetY;

        // Ajustar a cuadrícula si está activa
        if (gridActivo) {
            const gridSize = 25;
            x = Math.round(x / gridSize) * gridSize;
            y = Math.round(y / gridSize) * gridSize;
        }

        // Limitar dentro del área
        x = Math.max(0, Math.min(espacioRect.width - elemento.offsetWidth, x));
        y = Math.max(0, Math.min(espacioRect.height - elemento.offsetHeight, y));

        elemento.style.left = x + "px";
        elemento.style.top = y + "px";
    });

    // Al soltar el mouse
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