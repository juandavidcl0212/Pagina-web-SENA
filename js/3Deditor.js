// Escena y cámara
const scene = new THREE.Scene();
scene.background = new THREE.Color(0xf0f0f0);

const camera = new THREE.PerspectiveCamera(
    75,
    (window.innerWidth - 250) / window.innerHeight,
    0.1,
    1000
);
camera.position.set(0, 5, 15);

const renderer = new THREE.WebGLRenderer();
renderer.setSize(window.innerWidth - 250, window.innerHeight);
document.getElementById("espacio3D").appendChild(renderer.domElement);

const controls = new THREE.OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;

// Luces
const light = new THREE.DirectionalLight(0xffffff, 1);
light.position.set(5, 10, 7.5);
scene.add(light);

const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
scene.add(ambientLight);

// Texturas básicas (piso y pared)
colocarEscenario(escenariosDisponibles[0]); // Sala

const floor = new THREE.Mesh(
    new THREE.PlaneGeometry(20, 20),
    new THREE.MeshStandardMaterial({ map: floorTexture })
);
floor.rotation.x = -Math.PI / 2;
scene.add(floor);

const wall = new THREE.Mesh(
    new THREE.BoxGeometry(20, 5, 0.5),
    new THREE.MeshStandardMaterial({ map: wallTexture })
);
wall.position.set(0, 2.5, -10);
scene.add(wall);

// Loader GLTF
const loader = new THREE.GLTFLoader();

// Objetos por temática
const objetosPorTematica = {
    navidad: [
        { id:"arbol", nombre:"Árbol de Navidad", modelo:"../assets/objetos3d/modelos/arbol-navidad.glb", img:"../assets/objetos/arbol.png", ancho:2, alto:4, posX:-5, posY:0 },
        { id:"regalo", nombre:"Regalo", modelo:"../assets/objetos3d/modelos/regalo.glb", img:"../assets/objetos/regalo.png", ancho:1, alto:1, posX:2, posY:0 }
    ],
    halloween: [
        { id:"calabaza", nombre:"Calabaza", modelo:"../assets/objetos3d/modelos/calabaza.glb", img:"../assets/objetos/calabaza.png", ancho:1, alto:1, posX:-2, posY:0 },
        { id:"fantasma", nombre:"Fantasma", modelo:"../assets/objetos3d/modelos/fantasma.glb", img:"../assets/objetos/fantasma.png", ancho:1.5, alto:2, posX:4, posY:0 }
    ],
    cumple: [
        { id:"pastel", nombre:"Pastel", modelo:"../assets/objetos3d/modelos/pastel.glb", img:"../assets/objetos/pastel.png", ancho:2, alto:1.5, posX:0, posY:0 },
        { id:"globos", nombre:"Globos", modelo:"../assets/objetos3d/modelos/globos.glb", img:"../assets/objetos/globos.png", ancho:1, alto:3, posX:6, posY:0 }
    ],
    sanvalentin: [
        { id:"corazon", nombre:"Globo Corazón", modelo:"../assets/objetos3d/modelos/globo-corazon.glb", img:"../assets/objetos/corazon.png", ancho:1, alto:2, posX:-3, posY:0 }
    ],
    otros: [
        { id:"cama", nombre:"Cama", modelo:"../assets/objetos3d/modelos/cama.glb", img:"../assets/objetos/cama.png", ancho:3, alto:1, posX:0, posY:3 },
        { id:"tocador", nombre:"Tocador", modelo:"../assets/objetos3d/modelos/tocador.glb", img:"../assets/objetos/tocador.png", ancho:2, alto:2, posX:5, posY:-2 }
    ]
};

// Escenarios disponibles
const escenariosDisponibles = [
    { id:"sala", nombre:"Sala", modelo:"../assets/objetos3d/escenarios/sala.glb" },
    { id:"oficina", nombre:"Oficina", modelo:"../assets/objetos3d/escenarios/oficina.glb" },
    { id:"clase", nombre:"Salón de clases", modelo:"../assets/objetos3d/escenarios/salon-clase.glb" }
];

let escenarioActual = null;

function colocarEscenario(esc) {
    if (escenarioActual) {
        scene.remove(escenarioActual);
    }
    loader.load(esc.modelo, function(gltf) {
        escenarioActual = gltf.scene;
        escenarioActual.name = esc.id;
        escenarioActual.position.set(0,0,0);
        scene.add(escenarioActual);
    }, undefined, function(error) {
        console.error("Error cargando escenario:", error);
    });
}

// Objetos interactivos
const objetosInteractivos = [];
let dragControls;

function colocarObjeto(obj) {
    if (scene.getObjectByName(obj.id)) return;

    if (obj.modelo) {
        loader.load(obj.modelo, function(gltf) {
            const modelo = gltf.scene;
            modelo.scale.set(obj.ancho, obj.alto, 1);
            modelo.position.set(obj.posX, obj.alto/2, obj.posY);
            modelo.name = obj.id;
            scene.add(modelo);
            objetosInteractivos.push(modelo);
            dragControls = new THREE.DragControls(objetosInteractivos, camera, renderer.domElement);
        }, undefined, function(error) {
            console.error("Error cargando modelo:", error);
        });
    } else {
        const tex = textureLoader.load(obj.img);
        const material = new THREE.MeshStandardMaterial({ map: tex });
        const geometry = new THREE.BoxGeometry(obj.ancho, obj.alto, obj.ancho);
        const mesh = new THREE.Mesh(geometry, material);
        mesh.position.set(obj.posX, obj.alto/2, obj.posY);
        mesh.name = obj.id;
        scene.add(mesh);
        objetosInteractivos.push(mesh);
        dragControls = new THREE.DragControls(objetosInteractivos, camera, renderer.domElement);
    }
}

// Cargar lista de objetos y escenarios
function cargarObjetos() {
    for (const categoria in objetosPorTematica) {
        const contenedor = document.getElementById(categoria);
        objetosPorTematica[categoria].forEach(obj => {
            const div = document.createElement("div");
            div.className = "objeto";
            div.innerHTML = `<img src="${obj.img}"><span>${obj.nombre}</span>`;
            div.onclick = () => colocarObjeto(obj);
            contenedor.appendChild(div);
        });
    }

    // Escenarios
    const contEscenarios = document.getElementById("escenarios");
    escenariosDisponibles.forEach(esc => {
        const div = document.createElement("div");
        div.className = "objeto";
        div.innerHTML = `<span>${esc.nombre}</span>`;
        div.onclick = () => colocarEscenario(esc);
        contEscenarios.appendChild(div);
    });
}

cargarObjetos();

// Escenario inicial por defecto (Sala)
colocarEscenario(escenariosDisponibles[0]);

// Animación
function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
}
animate();

// Resize
window.addEventListener("resize", () => {
    camera.aspect = (window.innerWidth - 250) / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth - 250, window.innerHeight);
});

// Toggle categorías
function toggleCategoria(elemento) {
    const lista = elemento.nextElementSibling;
    lista.style.display = (lista.style.display === "block") ? "none" : "block";
}
