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
const ambientLight = new THREE.AmbientLight(0xffffff, 0.9);
scene.add(ambientLight);

const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.8);
hemiLight.position.set(0, 20, 0);
scene.add(hemiLight);

const dirLight = new THREE.DirectionalLight(0xffffff, 1.6);
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

const gltfLoader = new THREE.GLTFLoader();

// Piso y cuadricula base
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
floor.position.y = -0.01;
scene.add(floor);

// Escenarios 3D
const escenarios = {
    sala: {
        nombre: "Sala",
        modelo: "../assets/objetos/escenarios/sala.glb",
        escala: 12
    },
    oficina: {
        nombre: "Oficina",
        modelo: "../assets/objetos/escenarios/oficina.glb",
        escala: 12
    },
    salon: {
        nombre: "Salon de clases",
        modelo: "../assets/objetos/escenarios/salon-clases.glb",
        escala: 12
    }
};

let escenarioActual = null;

// Objetos por tematica
const objetos = {
    navidad: [
        {
            id: "arbol-navidad",
            nombre: "Arbol",
            modelo: "../assets/objetos/elementos/arbol-navidad.glb",
            preview: "../assets/objetos/arbol-navidad.png",
            escala: 6,
            posX: -3,
            posY: 0
        },
        {
            id: "regalo",
            nombre: "Regalo",
            modelo: "../assets/objetos/elementos/regalo.glb",
            preview: "../assets/objetos/regalo.png",
            escala: 1,
            posX: 2,
            posY: 0
        },
        {
            id: "esfera-nieve",
            nombre: "Esfera de nieve",
            modelo: "../assets/objetos/elementos/esfera-nieve.glb",
            preview: "../assets/objetos/esfera-nieve.png",
            escala: 0.5,
            posX: 4,
            posY: 0
        },
        {
            id: "muneco-nieve",
            nombre: "Muneco de nieve",
            modelo: "../assets/objetos/elementos/muñeco-nieve.glb",
            preview: "../assets/objetos/muñeco-nieve.png",
            escala: 6,
            posX: 0,
            posY: 3
        }
    ],

    halloween: [
        {
            id: "fantasma",
            nombre: "Fantasma",
            modelo: "../assets/objetos/elementos/fantasma.glb",
            escala: 2,
            posX: -3,
            posY: 0
        },
        {
            id: "murcielago",
            nombre: "Murcielago",
            modelo: "../assets/objetos/elementos/murcielago.glb",
            escala: 1.5,
            posX: 2,
            posY: 0
        },
        {
            id: "arana",
            nombre: "Arana",
            modelo: "../assets/objetos/elementos/araña.glb",
            escala: 1.5,
            posX: 4,
            posY: 0
        }
    ],

    cumple: [
        {
            id: "globos",
            nombre: "Globos",
            modelo: "../assets/objetos/elementos/globos.glb",
            escala: 6,
            posX: -3,
            posY: 0
        },
        {
            id: "decoracion-cumple",
            nombre: "Decoracion",
            modelo: "../assets/objetos/elementos/decoracion-cumpleaños.glb",
            escala: 2,
            posX: 2,
            posY: 0
        }
    ],

    sanvalentin: [
        {
            id: "corazon-flores",
            nombre: "Corazon de flores",
            modelo: "../assets/objetos/elementos/corazon-flores.glb",
            escala: 4,
            posX: -2,
            posY: 0
        },
        {
            id: "rosa",
            nombre: "Rosa",
            modelo: "../assets/objetos/elementos/rosa.glb",
            escala: 1.5,
            posX: 2,
            posY: 0
        }
    ],

    otros: [
        {
            id: "mesa",
            nombre: "Mesa",
            modelo: "../assets/objetos/elementos/mesa.glb",
            escala: 4.5,
            posX: -3,
            posY: 0
        },
        {
            id: "lampara",
            nombre: "Lampara",
            modelo: "../assets/objetos/elementos/lampara.glb",
            escala: 2,
            posX: 2,
            posY: 0
        },
        {
            id: "planta",
            nombre: "Planta",
            modelo: "../assets/objetos/elementos/planta.glb",
            escala: 2,
            posX: 4,
            posY: 0
        },
        {
            id: "tapete",
            nombre: "Tapete",
            modelo: "../assets/objetos/elementos/tapete.glb",
            escala: 4,
            posX: 0,
            posY: 3
        },
        {
            id: "reloj",
            nombre: "Reloj",
            modelo: "../assets/objetos/elementos/reloj.glb",
            escala: 1.5,
            posX: -4,
            posY: 3
        },
        {
            id: "cuadro",
            nombre: "Cuadro",
            modelo: "../assets/objetos/elementos/cuadro.glb",
            escala: 3,
            posX: 4,
            posY: 3
        },
        {
            id: "adornos-escritorio",
            nombre: "Adornos",
            modelo: "../assets/objetos/elementos/adornos-escritorio.glb",
            escala: 1.5,
            posX: 0,
            posY: -3
        },
        {
            id: "guirnalda",
            nombre: "Guirnalda",
            modelo: "../assets/objetos/elementos/guirnalda.glb",
            escala: 4,
            posX: -2,
            posY: -3
        }
    ]
};

const objetosInteractivos = [];
let dragControls;
let objetoSeleccionado = null;
const pasoVertical = 0.25;


function prepararModelo(modelo) {
    modelo.traverse((child) => {
        if (child.isMesh) {
            child.castShadow = true;
            child.receiveShadow = true;
        }
    });
}

function ajustarModeloAlTamano(modelo, tamanoObjetivo) {
    const caja = new THREE.Box3().setFromObject(modelo);
    const medidas = new THREE.Vector3();
    caja.getSize(medidas);

    const dimensionMayor = Math.max(medidas.x, medidas.y, medidas.z);

    if (dimensionMayor > 0) {
        const factor = tamanoObjetivo / dimensionMayor;
        modelo.scale.multiplyScalar(factor);
    }

    const cajaAjustada = new THREE.Box3().setFromObject(modelo);
    modelo.position.y -= cajaAjustada.min.y;
}

function actualizarDragControls() {
    if (dragControls) {
        dragControls.dispose();
    }

    dragControls = new THREE.DragControls(objetosInteractivos, camera, renderer.domElement);

    dragControls.addEventListener("dragstart", (event) => {
        controls.enabled = false;
        objetoSeleccionado = event.object;
    });

    dragControls.addEventListener("drag", (event) => {
        const alturaMinima = event.object.userData.alturaMinima || 0;

        if (event.object.position.y < alturaMinima) {
            event.object.position.y = alturaMinima;
        }
    });

    dragControls.addEventListener("dragend", (event) => {
        controls.enabled = true;
        objetoSeleccionado = event.object;
    });
}


function colocarObjeto(obj) {
    gltfLoader.load(
        obj.modelo,
        (gltf) => {
            const modelo = gltf.scene;

            modelo.name = `${obj.id}-${Date.now()}`;
            modelo.position.set(obj.posX || 0, 0, obj.posY || 0);

            prepararModelo(modelo);
            ajustarModeloAlTamano(modelo, obj.escala || 2);

            modelo.userData.alturaBase = modelo.position.y;
            modelo.userData.alturaMinima = modelo.position.y;


            scene.add(modelo);
            objetosInteractivos.push(modelo);
            actualizarDragControls();
        },
        undefined,
        (error) => {
            console.error("Error cargando modelo:", obj.modelo, error);
            alert("No se pudo cargar el modelo: " + obj.nombre);
        }
    );
}

function cargarObjetos() {
    for (const categoria in objetos) {
        const contenedor = document.getElementById(categoria);
        if (!contenedor) continue;

        contenedor.innerHTML = "";

        objetos[categoria].forEach((obj) => {
            const div = document.createElement("div");
            div.className = "objeto";

            if (obj.preview) {
                div.innerHTML = `<img src="${obj.preview}" alt="${obj.nombre}"><span>${obj.nombre}</span>`;
            } else {
                div.innerHTML = `<span>${obj.nombre}</span>`;
            }

            div.onclick = () => colocarObjeto(obj);
            contenedor.appendChild(div);
        });
    }
}

function cargarEscenario(id) {
    if (!id || !escenarios[id]) return;

    if (escenarioActual) {
        scene.remove(escenarioActual);
        escenarioActual.traverse((child) => {
            if (child.geometry) child.geometry.dispose();
            if (child.material) {
                if (Array.isArray(child.material)) {
                    child.material.forEach((mat) => mat.dispose());
                } else {
                    child.material.dispose();
                }
            }
        });
        escenarioActual = null;
    }

    gltfLoader.load(
        escenarios[id].modelo,
        (gltf) => {
            escenarioActual = gltf.scene;
            escenarioActual.name = "escenario-actual";

            prepararModelo(escenarioActual);
            ajustarModeloAlTamano(escenarioActual, escenarios[id].escala || 12);

            scene.add(escenarioActual);

            floor.visible = false;
            gridHelper.visible = false;

            camera.position.set(0, 8, 14);
            controls.target.set(0, 1, 0);
            controls.update();
        },
        undefined,
        (error) => {
            console.error("Error cargando escenario:", escenarios[id].modelo, error);
            alert("No se pudo cargar el escenario seleccionado.");
        }
    );
}

cargarObjetos();

const escenarioSelect = document.getElementById("escenarioSelect");
if (escenarioSelect) {
    escenarioSelect.addEventListener("change", (e) => {
        cargarEscenario(e.target.value);
    });
}

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

    if (lista.style.display === "block") {
        lista.style.display = "none";
        elemento.parentElement.classList.remove("abierta");
    } else {
        lista.style.display = "block";
        elemento.parentElement.classList.add("abierta");
    }
};

window.addEventListener("keydown", (event) => {
    if (!objetoSeleccionado) return;

    const alturaMinima = objetoSeleccionado.userData.alturaMinima || 0;

    if (event.key === "q" || event.key === "Q") {
        objetoSeleccionado.position.y += pasoVertical;
    }

    if (event.key === "e" || event.key === "E") {
        objetoSeleccionado.position.y = Math.max(
            alturaMinima,
            objetoSeleccionado.position.y - pasoVertical
        );
    }
});
function eliminarObjetoSeleccionado() {
    if (!objetoSeleccionado) {
        alert("Selecciona un objeto primero.");
        return;
    }

    scene.remove(objetoSeleccionado);

    const index = objetosInteractivos.indexOf(objetoSeleccionado);
    if (index !== -1) {
        objetosInteractivos.splice(index, 1);
    }

    objetoSeleccionado.traverse((child) => {
        if (child.geometry) child.geometry.dispose();

        if (child.material) {
            if (Array.isArray(child.material)) {
                child.material.forEach((mat) => mat.dispose());
            } else {
                child.material.dispose();
            }
        }
    });

    objetoSeleccionado = null;
    actualizarDragControls();
}

const btnEliminarObjeto = document.getElementById("btnEliminarObjeto");

if (btnEliminarObjeto) {
    btnEliminarObjeto.addEventListener("click", eliminarObjetoSeleccionado);

    if (event.key === "Delete" || event.key === "Backspace") {
    eliminarObjetoSeleccionado();
}
}
