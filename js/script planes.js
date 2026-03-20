<script>
// Objetos por temática
const objetosPorTematica = {
  navidad: [
    {nombre: "Árbol de Navidad", img: "../assets/objetos/arbol_navidad.png"},
    {nombre: "Regalo", img: "../assets/objetos/regalo.png"},
    {nombre: "Guirnalda", img: "../assets/objetos/guirnalda.png"}
  ],
  halloween: [
    {nombre: "Calabaza", img: "../assets/objetos/calabaza.png"},
    {nombre: "Fantasma", img: "../assets/objetos/fantasma.png"},
    {nombre: "Murciélago", img: "../assets/objetos/murcielago.png"}
  ],
  cumple: [
    {nombre: "Pastel", img: "../assets/objetos/pastel.png"},
    {nombre: "Globos", img: "../assets/objetos/globos.png"},
    {nombre: "Sombrero Fiesta", img: "../assets/objetos/sombrero.png"}
  ],
  sanvalentin: [
    {nombre: "Corazón", img: "../assets/objetos/corazon.png"},
    {nombre: "Rosa", img: "../assets/objetos/rosa.png"},
    {nombre: "Carta Amor", img: "../assets/objetos/carta.png"}
  ],
  otros: [
    {nombre: "Planta", img: "../assets/objetos/planta.png"},
    {nombre: "Cuadro", img: "../assets/objetos/cuadro.png"}
  ]
};

// Mostrar objetos según temática
function mostrarObjetos() {
  const tematica = document.getElementById("tematicaSelect").value;
  const lista = document.getElementById("listaObjetos");
  lista.innerHTML = "";

  objetosPorTematica[tematica].forEach(obj => {
    const div = document.createElement("div");
    div.innerHTML = `
      <img src="${obj.img}" alt="${obj.nombre}" style="width:80px; cursor:pointer;" 
           onclick="colocarObjeto('${obj.img}')">
      <p>${obj.nombre}</p>
    `;
    lista.appendChild(div);
  });
}

// Colocar objeto en el espacio
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

// Inicializar con temática por defecto
mostrarObjetos();
</script>