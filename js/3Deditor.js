const scene = new THREE.Scene();
scene.background = new THREE.Color(0x18191c); // Dark mode background

const camera = new THREE.PerspectiveCamera(75, (window.innerWidth-280)/window.innerHeight, 0.1, 1000);
camera.position.set(0, 10, 15);

const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setSize(window.innerWidth-280, window.innerHeight);
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap; // Soft shadows
document.getElementById("espacio3D").appendChild(renderer.domElement);

const controls = new THREE.OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.maxPolarAngle = Math.PI / 2 - 0.05; // Prevent camera from going below floor

// Iluminación
const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
scene.add(ambientLight);

const dirLight = new THREE.DirectionalLight(0xffffff, 1);
dirLight.position.set(10, 20, 10);
dirLight.castShadow = true;
dirLight.shadow.mapSize.width = 2048;
dirLight.shadow.mapSize.height = 2048;
dirLight.shadow.camera.near = 0.5;
dirLight.shadow.camera.far = 50;
dirLight.shadow.camera.left = -15;
dirLight.shadow.camera.right = 15;
dirLight.shadow.camera.top = 15;
dirLight.shadow.camera.bottom = -15;
scene.add(dirLight);

const textureLoader = new THREE.TextureLoader();

// Piso y Cuadrícula
const gridHelper = new THREE.GridHelper(30, 30, 0x5e72e4, 0x444444);
scene.add(gridHelper);

const floorGeometry = new THREE.PlaneGeometry(30, 30);
const floorMaterial = new THREE.MeshStandardMaterial({ 
    color: 0x222222, 
    roughness: 0.8 
});
const floor = new THREE.Mesh(floorGeometry, floorMaterial);
floor.rotation.x = -Math.PI / 2;
floor.receiveShadow = true;
floor.position.y = -0.01; // Just below the grid
scene.add(floor);

// Objetos por temática
const objetosPorTematica = {
    navidad: [
        { id:"arbol", nombre: "Árbol", img: "../assets/objetos/arbol-navidad.png", ancho: 2, alto: 4, posX: -5, posY: 0 },
        { id:"regalo", nombre: "Regalo", img: "../assets/objetos/regalo.png", ancho: 1, alto: 1, posX: 2, posY: 0 }
    ],
    halloween: [
        { id:"calabaza", nombre: "Calabaza", img: "../assets/objetos/calabaza.png", ancho: 1, alto: 1, posX: -2, posY: 0 },
        { id:"fantasma", nombre: "Fantasma", img: "../assets/objetos/fantasma.png", ancho: 1.5, alto: 2, posX: 4, posY: 0 }
    ],
    cumple: [
        { id:"pastel", nombre: "Pastel", img: "../assets/objetos/pastel.png", ancho: 2, alto: 1.5, posX: 0, posY: 0 },
        { id:"globos", nombre: "Globos", img: "../assets/objetos/globos.png", ancho: 1, alto: 3, posX: 6, posY: 0 }
    ],
    sanvalentin: [
        { id:"corazon", nombre: "Corazón", img: "../assets/objetos/globo-corazon.png", ancho: 1, alto: 2, posX: -3, posY: 0 }
    ],
    otros: [
        { id:"cama", nombre: "Cama", img: "../assets/objetos/cama.png", ancho: 3, alto: 1, posX: 0, posY: 3 },
        { id:"tocador", nombre: "Tocador", img: "../assets/objetos/tocador.png", ancho: 2, alto: 2, posX: 5, posY: -2 }
    ]
};

const objetosInteractivos = [];
let dragControls;

function colocarObjeto(obj) {
    if (scene.getObjectByName(obj.id)) return;
    const tex = textureLoader.load(obj.img);
    const material = new THREE.MeshStandardMaterial({ 
        map: tex,
        transparent: true, // Use transparency if PNG has alpha
        alphaTest: 0.1 
    });
    const geometry = new THREE.BoxGeometry(obj.ancho, obj.alto, obj.ancho);
    const mesh = new THREE.Mesh(geometry, material);
    mesh.position.set(obj.posX, obj.alto/2, obj.posY);
    mesh.name = obj.id;
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    scene.add(mesh);
    objetosInteractivos.push(mesh);
    
    if(dragControls) dragControls.dispose();
    dragControls = new THREE.DragControls(objetosInteractivos, camera, renderer.domElement);
    dragControls.addEventListener('dragstart', function () { controls.enabled = false; });
    dragControls.addEventListener('dragend', function () { controls.enabled = true; });
}

function cargarObjetos() {
    for (const categoria in objetosPorTematica) {
        const contenedor = document.getElementById(categoria);
        if(!contenedor) continue;
        objetosPorTematica[categoria].forEach(obj => {
            const div = document.createElement("div");
            div.className = "objeto";
            div.innerHTML = `<img src="${obj.img}"><span>${obj.nombre}</span>`;
            div.onclick = () => colocarObjeto(obj);
            contenedor.appendChild(div);
        });
    }
}
cargarObjetos();

function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
}
animate();

window.addEventListener("resize", () => {
    camera.aspect = (window.innerWidth - 280) / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth - 280, window.innerHeight);
});

window.toggleCategoria = function(elemento) {
    const lista = elemento.nextElementSibling;
    if(lista.style.display === "block") {
        lista.style.display = "none";
        elemento.parentElement.classList.remove('abierta');
    } else {
        lista.style.display = "block";
        elemento.parentElement.classList.add('abierta');
    }
};

/* ================== CARGAR PLANO COMO PISO ================== */
document.getElementById('inputPlano').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(event) {
        const img = new Image();
        img.onload = function() {
            // Adjust the plane geometry based on image aspect ratio
            const aspect = img.width / img.height;
            const newGeo = new THREE.PlaneGeometry(30 * aspect, 30);
            floor.geometry.dispose();
            floor.geometry = newGeo;

            const tex = new THREE.Texture(img);
            tex.needsUpdate = true;
            floor.material.map = tex;
            floor.material.color.setHex(0xffffff); // remove dark tint to show plan clearly
            floor.material.needsUpdate = true;
            
            // hide grid so it doesn't obstruct the plan
            gridHelper.visible = false;
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
});
