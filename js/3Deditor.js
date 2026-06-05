const scene = new THREE.Scene();
scene.background = new THREE.Color(0x18191c); // Dark mode background

function obtenerAnchoRender() {
    return Math.max(320, window.innerWidth - 290);
}

const camera = new THREE.PerspectiveCamera(75, obtenerAnchoRender() / window.innerHeight, 0.1, 1000);
camera.position.set(0, 10, 15);

const renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
renderer.setSize(obtenerAnchoRender(), window.innerHeight);
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap; // Soft shadows
renderer.outputEncoding = THREE.sRGBEncoding;
renderer.physicallyCorrectLights = true;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.05;
document.getElementById("espacio3D").appendChild(renderer.domElement);

const controls = new THREE.OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.maxPolarAngle = Math.PI / 2 - 0.05; // Prevent camera from going below floor

// Iluminación
const ambientLight = new THREE.AmbientLight(0xb8c7d9, 0.42);
scene.add(ambientLight);

const hemiLight = new THREE.HemisphereLight(0xffffff, 0x2b3038, 0.85);
hemiLight.position.set(0, 20, 0);
scene.add(hemiLight);

const dirLight = new THREE.DirectionalLight(0xfff4df, 2.25);
dirLight.position.set(10, 20, 10);
dirLight.castShadow = true;
dirLight.shadow.mapSize.width = 4096;
dirLight.shadow.mapSize.height = 4096;
dirLight.shadow.camera.near = 0.5;
dirLight.shadow.camera.far = 50;
dirLight.shadow.camera.left = -22;
dirLight.shadow.camera.right = 22;
dirLight.shadow.camera.top = 22;
dirLight.shadow.camera.bottom = -22;
dirLight.shadow.radius = 4;
dirLight.shadow.bias = -0.00018;
scene.add(dirLight);

const textureLoader = new THREE.TextureLoader();
const paredTexturas = crearTexturasParedProcedurales();

const gltfLoader = new THREE.GLTFLoader();

function crearTexturasParedProcedurales() {
    const size = 256;
    const albedoCanvas = document.createElement("canvas");
    const normalCanvas = document.createElement("canvas");
    albedoCanvas.width = normalCanvas.width = size;
    albedoCanvas.height = normalCanvas.height = size;

    const albedoCtx = albedoCanvas.getContext("2d");
    const normalCtx = normalCanvas.getContext("2d");
    const albedoData = albedoCtx.createImageData(size, size);
    const normalData = normalCtx.createImageData(size, size);

    for (let y = 0; y < size; y++) {
        for (let x = 0; x < size; x++) {
            const i = (y * size + x) * 4;
            const fine = Math.random() * 18;
            const wave = Math.sin(x * 0.17) * 4 + Math.cos(y * 0.13) * 4;
            const shade = Math.max(208, Math.min(244, 226 + fine + wave));

            albedoData.data[i] = shade;
            albedoData.data[i + 1] = shade + 2;
            albedoData.data[i + 2] = shade + 1;
            albedoData.data[i + 3] = 255;

            normalData.data[i] = 128 + Math.sin((x + fine) * 0.35) * 18;
            normalData.data[i + 1] = 128 + Math.cos((y + fine) * 0.31) * 18;
            normalData.data[i + 2] = 235;
            normalData.data[i + 3] = 255;
        }
    }

    albedoCtx.putImageData(albedoData, 0, 0);
    normalCtx.putImageData(normalData, 0, 0);

    const map = new THREE.CanvasTexture(albedoCanvas);
    const normalMap = new THREE.CanvasTexture(normalCanvas);
    [map, normalMap].forEach((texture) => {
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(2.4, 1.2);
    });
    map.encoding = THREE.sRGBEncoding;

    return { map, normalMap };
}

function crearMaterialPared(color) {
    return new THREE.MeshStandardMaterial({
        color: new THREE.Color(color),
        map: paredTexturas.map,
        normalMap: paredTexturas.normalMap,
        normalScale: new THREE.Vector2(0.08, 0.08),
        roughness: 0.86,
        metalness: 0.0
    });
}

function leerMedida(inputId, respaldo, minimo, maximo = Infinity) {
    const input = document.getElementById(inputId);
    const crudo = String(input?.value ?? "").trim().replace(",", ".");
    const numero = Number.parseFloat(crudo);
    const base = Number.isFinite(numero) ? numero : respaldo;
    return Math.min(maximo, Math.max(minimo, base));
}

function campoTieneMedidaValida(inputId) {
    const input = document.getElementById(inputId);
    const crudo = String(input?.value ?? "").trim();
    if (!crudo || crudo === "-" || crudo === "." || crudo === ",") return false;
    if (!/^\d+([,.]\d*)?$/.test(crudo)) return false;
    return Number.isFinite(Number.parseFloat(crudo.replace(",", ".")));
}

function formatearMedida(numero, decimales = 2) {
    return Number(numero).toFixed(decimales).replace(".", ",");
}

function numeroSeguro(valor, respaldo, minimo, maximo = Infinity) {
    const numero = Number.parseFloat(valor);
    const base = Number.isFinite(numero) ? numero : respaldo;
    return Math.min(maximo, Math.max(minimo, base));
}

// Piso y cuadricula base
const gridHelper = new THREE.GridHelper(30, 30, 0x5e72e4, 0x444444);
scene.add(gridHelper);

const floorGeometry = new THREE.PlaneGeometry(30, 30);
const floorMaterial = new THREE.MeshStandardMaterial({
    color: 0x26282c,
    roughness: 0.9,
    metalness: 0
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
            preview: "../assets/objetos/monigote-de-nieve.png",
            escala: 6,
            posX: 0,
            posY: 3
        },
        {
            id: "guirnalda",
            nombre: "Guirnalda",
            modelo: "../assets/objetos/elementos/guirnalda.glb",
            preview: "../assets/objetos/guirnalda.png",
            escala: 5,
            posX: 0,
            posY: 3
        }   
    ],

    halloween: [
        {
            id: "fantasma",
            nombre: "Fantasma",
            modelo: "../assets/objetos/elementos/fantasma.glb",
            preview: "../assets/objetos/fantasma.png",
            escala: 2,
            posX: -3,
            posY: 0
        },
        {
            id: "murcielago",
            nombre: "Murcielago",
            modelo: "../assets/objetos/elementos/murcielago.glb",
            preview: "../assets/objetos/murcielago.png",
            escala: 1.5,
            posX: 2,
            posY: 0
        },
        {
            id: "arana",
            nombre: "Araña",
            modelo: "../assets/objetos/elementos/araña.glb",
            preview: "../assets/objetos/araña.png",
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
            preview: "../assets/objetos/globos.png",
            escala: 6,
            posX: -3,
            posY: 0
        },
        {
            id: "decoracion-cumple",
            nombre: "Decoracion",
            modelo: "../assets/objetos/elementos/decoracion-cumpleaños.glb",
            preview: "../assets/objetos/decoracion-cumpleaños.png",
            escala: 4.5,
            posX: 2,
            posY: 0
        }
    ],

    sanvalentin: [
        {
            id: "corazon-flores",
            nombre: "Corazon de flores",
            modelo: "../assets/objetos/elementos/corazon-flores.glb",
            preview: "../assets/objetos/corazon-flores.png",
            escala: 4,
            posX: -2,
            posY: 0
        },
        {
            id: "rosa",
            nombre: "Rosa",
            modelo: "../assets/objetos/elementos/rosa.glb",
            preview: "../assets/objetos/rosa.png", 
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
            preview: "../assets/objetos/mesa.png",
            escala: 4.5,
            posX: -3,
            posY: 0
        },
        {
            id: "lampara",
            nombre: "Lampara",
            modelo: "../assets/objetos/elementos/lampara.glb",
            preview: "../assets/objetos/lampara.png",
            escala: 4,
            posX: 2,
            posY: 0
        },
        {
            id: "planta",
            nombre: "Planta",
            modelo: "../assets/objetos/elementos/planta.glb",
            preview: "../assets/objetos/planta.png",
            escala: 2,
            posX: 4,
            posY: 0
        },
        {
            id: "tapete",
            nombre: "Tapete",
            modelo: "../assets/objetos/elementos/tapete.glb",
            preview: "../assets/objetos/tapete.png",   
            escala: 4,
            posX: 0,
            posY: 3
        },
        {
            id: "reloj",
            nombre: "Reloj",
            modelo: "../assets/objetos/elementos/reloj.glb",
            preview: "../assets/objetos/reloj.png",
            escala: 1.5,
            posX: -4,
            posY: 3
        },
        {
            id: "cuadro",
            nombre: "Cuadro",
            modelo: "../assets/objetos/elementos/cuadro.glb",
            preview: "../assets/objetos/cuadro.jpg",
            escala: 3,
            posX: 4,
            posY: 3
        },
        {
            id: "adornos-escritorio",
            nombre: "Adornos",
            modelo: "../assets/objetos/elementos/adornos-escritorio.glb",
            preview: "../assets/objetos/adornos-escritorio.png",
            escala: 1.5,
            posX: 0,
            posY: -3
        }
    ]
};

const objetosInteractivos = [];
let dragControls;
let objetoSeleccionado = null;
const pasoVertical = 0.25;
let escenarioImportado = null;
let proyectoActualId = new URLSearchParams(window.location.search).get("id");
let nombreProyectoActual = "";
const paredesPlano = [];
const paredes3D = [];
let orientacionParedActiva = "horizontal";
const pixelesPorMetro = 24;
const grosorParedPorDefecto = 0.18;
let paredSeleccionada = null;
let paredArrastrada2D = null;
let offsetArrastre2D = { x: 0, y: 0 };
const raycaster = new THREE.Raycaster();
const mouse = new THREE.Vector2();
const transformControls = new THREE.TransformControls(camera, renderer.domElement);
transformControls.setMode("translate");
transformControls.setTranslationSnap(0.25);
transformControls.showY = false;
scene.add(transformControls);

transformControls.addEventListener("dragging-changed", (event) => {
    controls.enabled = !event.value;
});

transformControls.addEventListener("objectChange", () => {
    if (paredSeleccionada) {
        sincronizarParedDesdeGrupo(paredSeleccionada);
        actualizarSegmento2D(paredSeleccionada);
    }
});



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
        if (paredSeleccionada) {
            transformControls.detach();
            paredes3D.forEach((pared) => marcarParedSeleccionada(pared, false));
            paredSeleccionada = null;
            actualizarPanelParedSeleccionada();
        }
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


function colocarObjeto(obj, estadoGuardado = null) {
    gltfLoader.load(
        obj.modelo,
        (gltf) => {
            const modelo = gltf.scene;

            modelo.name = estadoGuardado?.name || `${obj.id}-${Date.now()}`;
            modelo.position.set(
                estadoGuardado?.position?.x ?? obj.posX ?? 0,
                estadoGuardado?.position?.y ?? 0,
                estadoGuardado?.position?.z ?? obj.posY ?? 0
            );

            prepararModelo(modelo);
            ajustarModeloAlTamano(modelo, obj.escala || 2);

            if (estadoGuardado?.position) {
                modelo.position.set(
                    estadoGuardado.position.x ?? modelo.position.x,
                    estadoGuardado.position.y ?? modelo.position.y,
                    estadoGuardado.position.z ?? modelo.position.z
                );
            }

            if (estadoGuardado?.rotation) {
                modelo.rotation.set(
                    estadoGuardado.rotation.x || 0,
                    estadoGuardado.rotation.y || 0,
                    estadoGuardado.rotation.z || 0
                );
            }

            modelo.userData.alturaBase = modelo.position.y;
            modelo.userData.alturaMinima = modelo.position.y;
            modelo.userData.objectId = obj.id;
            modelo.userData.nombre = obj.nombre;
            modelo.userData.escalaObjetivo = obj.escala || 2;


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

function normalizarPared(pared) {
    pared.thickness = numeroSeguro(pared.thickness, grosorParedPorDefecto, 0.12, 0.45);
    pared.height = numeroSeguro(pared.height, 2.7, 1.5, 10);
    pared.length = numeroSeguro(pared.length, 3, 0.5, 30);
    pared.color = pared.color || "#edf7f6";
    pared.openings = Array.isArray(pared.openings) ? pared.openings : [];
    pared.x = numeroSeguro(pared.x, 0, -100, 100);
    pared.z = numeroSeguro(pared.z, 0, -100, 100);
    return pared;
}

function crearBloquePared(pared, ancho, alto, profundidad, offset, centroY) {
    const geometry = new THREE.BoxGeometry(ancho, alto, profundidad);
    const mesh = new THREE.Mesh(geometry, crearMaterialPared(pared.color));
    mesh.position.set(
        pared.orientation === "horizontal" ? offset : 0,
        centroY,
        pared.orientation === "vertical" ? offset : 0
    );
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    mesh.userData.tipo = "pared";
    mesh.userData.wallId = pared.id;
    return mesh;
}

function crearRodapie(pared, offset, ancho) {
    const rodapieAlto = 0.11;
    const rodapieProfundidad = pared.thickness + 0.055;
    const geometry = new THREE.BoxGeometry(
        pared.orientation === "horizontal" ? ancho : rodapieProfundidad,
        rodapieAlto,
        pared.orientation === "vertical" ? ancho : rodapieProfundidad
    );
    const material = new THREE.MeshStandardMaterial({
        color: 0xf4f1e8,
        roughness: 0.74,
        metalness: 0
    });
    const mesh = new THREE.Mesh(geometry, material);
    mesh.position.set(
        pared.orientation === "horizontal" ? offset : 0,
        rodapieAlto / 2,
        pared.orientation === "vertical" ? offset : 0
    );
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    mesh.userData.tipo = "pared";
    mesh.userData.wallId = pared.id;
    return mesh;
}

function crearSombraContactoPared(pared) {
    const geometry = new THREE.PlaneGeometry(pared.length + pared.thickness, pared.thickness * 2.2);
    const material = new THREE.MeshBasicMaterial({
        color: 0x000000,
        transparent: true,
        opacity: 0.16,
        depthWrite: false
    });
    const shadow = new THREE.Mesh(geometry, material);
    shadow.rotation.x = -Math.PI / 2;
    if (pared.orientation === "vertical") {
        shadow.rotation.z = Math.PI / 2;
    }
    shadow.position.y = 0.006;
    shadow.renderOrder = 1;
    return shadow;
}

function crearMarcoAbertura(pared, opening) {
    const group = new THREE.Group();
    const frameColor = opening.type === "door" ? 0x8b5e3c : 0xf7f8fb;
    const material = new THREE.MeshStandardMaterial({
        color: frameColor,
        roughness: opening.type === "door" ? 0.58 : 0.42,
        metalness: 0.02
    });
    const glassMaterial = new THREE.MeshPhysicalMaterial({
        color: 0xbfd8ee,
        roughness: 0.08,
        metalness: 0,
        transmission: 0.35,
        transparent: true,
        opacity: 0.42
    });
    const frame = 0.07;
    const depth = pared.thickness + 0.06;
    const parts = [
        { x: -opening.width / 2 - frame / 2, y: opening.bottom + opening.height / 2, w: frame, h: opening.height + frame * 2 },
        { x: opening.width / 2 + frame / 2, y: opening.bottom + opening.height / 2, w: frame, h: opening.height + frame * 2 },
        { x: 0, y: opening.bottom + opening.height + frame / 2, w: opening.width + frame * 2, h: frame }
    ];
    if (opening.type === "window") {
        parts.push({ x: 0, y: opening.bottom - frame / 2, w: opening.width + frame * 2, h: frame });
    }

    parts.forEach((part) => {
        const geometry = new THREE.BoxGeometry(
            pared.orientation === "horizontal" ? part.w : depth,
            part.h,
            pared.orientation === "vertical" ? part.w : depth
        );
        const mesh = new THREE.Mesh(geometry, material);
        mesh.position.set(
            pared.orientation === "horizontal" ? opening.offset + part.x : 0,
            part.y,
            pared.orientation === "vertical" ? opening.offset + part.x : 0
        );
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        group.add(mesh);
    });

    if (opening.type === "door") {
        const geometry = new THREE.BoxGeometry(
            pared.orientation === "horizontal" ? opening.width * 0.88 : pared.thickness * 0.42,
            opening.height * 0.94,
            pared.orientation === "vertical" ? opening.width * 0.88 : pared.thickness * 0.42
        );
        const door = new THREE.Mesh(geometry, material);
        door.position.set(
            pared.orientation === "horizontal" ? opening.offset : 0,
            opening.bottom + opening.height * 0.47,
            pared.orientation === "vertical" ? opening.offset : 0
        );
        door.castShadow = true;
        group.add(door);
    } else {
        const geometry = new THREE.BoxGeometry(
            pared.orientation === "horizontal" ? opening.width * 0.82 : pared.thickness * 0.18,
            opening.height * 0.72,
            pared.orientation === "vertical" ? opening.width * 0.82 : pared.thickness * 0.18
        );
        const glass = new THREE.Mesh(geometry, glassMaterial);
        glass.position.set(
            pared.orientation === "horizontal" ? opening.offset : 0,
            opening.bottom + opening.height / 2,
            pared.orientation === "vertical" ? opening.offset : 0
        );
        group.add(glass);
    }

    group.userData.tipo = "pared";
    group.userData.wallId = pared.id;
    return group;
}

function normalizarAbertura(pared, opening) {
    const margen = Math.min(0.28, pared.length * 0.18);
    const anchoMaximo = Math.max(0.18, pared.length - margen * 2);
    const altoMaximo = Math.max(0.25, pared.height - 0.12);
    const bottomBase = opening.type === "door" ? 0 : Math.min(1.05, Math.max(0.45, pared.height * 0.32));
    const widthBase = opening.type === "door" ? 0.92 : 1.2;
    const heightBase = opening.type === "door" ? 2.12 : 1.05;
    const width = numeroSeguro(opening.width, widthBase, Math.min(0.18, anchoMaximo), anchoMaximo);
    const bottom = numeroSeguro(opening.bottom, bottomBase, 0, Math.max(0, pared.height - 0.25));
    const height = numeroSeguro(opening.height, heightBase, 0.25, Math.max(0.25, altoMaximo - bottom));
    const offsetMin = -pared.length / 2 + width / 2 + margen;
    const offsetMax = pared.length / 2 - width / 2 - margen;

    return {
        type: opening.type === "window" ? "window" : "door",
        width,
        height,
        bottom,
        offset: numeroSeguro(opening.offset, 0, Math.min(offsetMin, offsetMax), Math.max(offsetMin, offsetMax))
    };
}

function crearMallaPared(pared, animarEntrada = false) {
    normalizarPared(pared);

    const grupo = new THREE.Group();
    grupo.name = `pared-${pared.id}`;
    grupo.position.set(pared.x, 0, pared.z);
    grupo.userData.tipo = "pared";
    grupo.userData.wallId = pared.id;
    grupo.userData.pared = pared;

    const profundidad = pared.thickness;
    grupo.add(crearSombraContactoPared(pared));
    const openings = pared.openings
        .map((opening) => normalizarAbertura(pared, opening))
        .sort((a, b) => (a.offset - a.width / 2) - (b.offset - b.width / 2));
    pared.openings = openings;

    let cursor = -pared.length / 2 - pared.thickness / 2;
    openings.forEach((opening) => {
        const start = opening.offset - opening.width / 2;
        const end = opening.offset + opening.width / 2;
        const leftWidth = Math.max(0, start - cursor);
        if (leftWidth > 0.04) {
            const offset = cursor + leftWidth / 2;
            grupo.add(crearBloquePared(
                pared,
                pared.orientation === "horizontal" ? leftWidth : profundidad,
                pared.height,
                pared.orientation === "vertical" ? leftWidth : profundidad,
                offset,
                pared.height / 2
            ));
            grupo.add(crearRodapie(pared, offset, leftWidth));
        }

        if (opening.bottom > 0.04) {
            grupo.add(crearBloquePared(
                pared,
                pared.orientation === "horizontal" ? opening.width : profundidad,
                opening.bottom,
                pared.orientation === "vertical" ? opening.width : profundidad,
                opening.offset,
                opening.bottom / 2
            ));
        }

        const topHeight = pared.height - opening.bottom - opening.height;
        if (topHeight > 0.04) {
            grupo.add(crearBloquePared(
                pared,
                pared.orientation === "horizontal" ? opening.width : profundidad,
                topHeight,
                pared.orientation === "vertical" ? opening.width : profundidad,
                opening.offset,
                opening.bottom + opening.height + topHeight / 2
            ));
        }

        grupo.add(crearMarcoAbertura(pared, opening));
        cursor = end;
    });

    const rightWidth = Math.max(0, pared.length / 2 + pared.thickness / 2 - cursor);
    if (rightWidth > 0.04) {
        const offset = cursor + rightWidth / 2;
        grupo.add(crearBloquePared(
            pared,
            pared.orientation === "horizontal" ? rightWidth : profundidad,
            pared.height,
            pared.orientation === "vertical" ? rightWidth : profundidad,
            offset,
            pared.height / 2
        ));
        grupo.add(crearRodapie(pared, offset, rightWidth));
    }

    if (animarEntrada) {
        grupo.scale.y = 0.001;
        grupo.userData.animacionEntrada = {
            inicio: performance.now(),
            duracion: 650
        };
    }

    scene.add(grupo);
    paredes3D.push(grupo);
    return grupo;
}

function renderPared2D(pared) {
    const canvas = document.getElementById("wallCanvas");
    if (!canvas) return;
    normalizarPared(pared);

    const hint = canvas.querySelector(".wall-canvas__hint");
    if (hint) hint.style.display = "none";

    const segment = document.createElement("div");
    segment.className = "wall-segment-2d";
    segment.dataset.wallId = pared.id;
    segment.style.left = `${pared.canvasX}px`;
    segment.style.top = `${pared.canvasY}px`;
    segment.style.width = `${pared.length * pixelesPorMetro}px`;
    segment.style.background = pared.color;
    segment.style.transform = pared.orientation === "vertical" ? "rotate(90deg)" : "rotate(0deg)";
    segment.title = "Haz clic para seleccionar esta pared";
    segment.innerHTML = `<span>${pared.length.toFixed(1)} m x ${pared.thickness.toFixed(2)} m</span>`;
    segment.addEventListener("pointerdown", iniciarArrastrePared2D);
    segment.addEventListener("click", (event) => {
        event.stopPropagation();
        seleccionarPared(pared.id);
    });
    canvas.appendChild(segment);
}

function actualizarSegmento2D(pared) {
    const canvas = document.getElementById("wallCanvas");
    const segment = document.querySelector(`.wall-segment-2d[data-wall-id="${pared.id}"]`);
    if (!canvas || !segment) return;

    const rect = canvas.getBoundingClientRect();
    if (!rect.width || !rect.height) return;
    const startX = pared.orientation === "horizontal"
        ? pared.x * pixelesPorMetro + rect.width / 2 - (pared.length * pixelesPorMetro) / 2
        : pared.x * pixelesPorMetro + rect.width / 2;
    const startY = pared.orientation === "vertical"
        ? pared.z * pixelesPorMetro + rect.height / 2 - (pared.length * pixelesPorMetro) / 2
        : pared.z * pixelesPorMetro + rect.height / 2;

    pared.canvasX = Math.round(startX / pixelesPorMetro) * pixelesPorMetro;
    pared.canvasY = Math.round(startY / pixelesPorMetro) * pixelesPorMetro;
    segment.style.left = `${pared.canvasX}px`;
    segment.style.top = `${pared.canvasY}px`;
    segment.style.width = `${pared.length * pixelesPorMetro}px`;
    segment.style.background = pared.color;
    segment.style.transform = pared.orientation === "vertical" ? "rotate(90deg)" : "rotate(0deg)";
    segment.innerHTML = `<span>${pared.length.toFixed(1)} m x ${pared.thickness.toFixed(2)} m</span>`;
}

function actualizarParedDesdeCanvas(pared) {
    const canvas = document.getElementById("wallCanvas");
    if (!canvas) return;

    const rect = canvas.getBoundingClientRect();
    pared.x = (pared.canvasX - rect.width / 2) / pixelesPorMetro + (pared.orientation === "horizontal" ? pared.length / 2 : 0);
    pared.z = (pared.canvasY - rect.height / 2) / pixelesPorMetro + (pared.orientation === "vertical" ? pared.length / 2 : 0);
}

function iniciarArrastrePared2D(event) {
    const pared = obtenerParedPlano(event.currentTarget.dataset.wallId);
    if (!pared) return;

    event.preventDefault();
    event.stopPropagation();
    seleccionarPared(pared.id);
    paredArrastrada2D = pared;

    const canvas = document.getElementById("wallCanvas");
    const rect = canvas.getBoundingClientRect();
    offsetArrastre2D.x = event.clientX - rect.left - pared.canvasX;
    offsetArrastre2D.y = event.clientY - rect.top - pared.canvasY;

    window.addEventListener("pointermove", moverPared2D);
    window.addEventListener("pointerup", terminarArrastrePared2D, { once: true });
}

function moverPared2D(event) {
    if (!paredArrastrada2D) return;

    const canvas = document.getElementById("wallCanvas");
    if (!canvas) return;

    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left - offsetArrastre2D.x;
    const y = event.clientY - rect.top - offsetArrastre2D.y;
    paredArrastrada2D.canvasX = Math.round(x / pixelesPorMetro) * pixelesPorMetro;
    paredArrastrada2D.canvasY = Math.round(y / pixelesPorMetro) * pixelesPorMetro;
    actualizarParedDesdeCanvas(paredArrastrada2D);

    const grupo = obtenerGrupoPared(paredArrastrada2D.id);
    if (grupo) {
        grupo.position.set(paredArrastrada2D.x, 0, paredArrastrada2D.z);
    }
    actualizarSegmento2D(paredArrastrada2D);
}

function terminarArrastrePared2D() {
    window.removeEventListener("pointermove", moverPared2D);
    paredArrastrada2D = null;
}

function agregarParedDesdePlano(event) {
    const canvas = document.getElementById("wallCanvas");
    if (!canvas || event.target.closest(".wall-segment-2d")) return;

    const rect = canvas.getBoundingClientRect();
    const canvasX = Math.round((event.clientX - rect.left) / pixelesPorMetro) * pixelesPorMetro;
    const canvasY = Math.round((event.clientY - rect.top) / pixelesPorMetro) * pixelesPorMetro;
    const length = leerMedida("wallLength", 3, 0.5, 30);
    const height = leerMedida("wallHeight", 2.7, 1.5, 10);
    const color = document.getElementById("wallColor")?.value || "#edf7f6";
    const thickness = leerMedida("wallThickness", grosorParedPorDefecto, 0.12, 0.45);
    const x = (canvasX - rect.width / 2) / pixelesPorMetro + (orientacionParedActiva === "horizontal" ? length / 2 : 0);
    const z = (canvasY - rect.height / 2) / pixelesPorMetro + (orientacionParedActiva === "vertical" ? length / 2 : 0);

    const pared = {
        id: Date.now(),
        orientation: orientacionParedActiva,
        length,
        height,
        thickness,
        color,
        openings: [],
        canvasX,
        canvasY,
        x,
        z
    };

    paredesPlano.push(pared);
    renderPared2D(pared);
    crearMallaPared(pared, true);
    seleccionarPared(pared.id);

    floor.visible = true;
    gridHelper.visible = true;
    camera.position.set(0, 8, 12);
    controls.target.set(0, 1.4, 0);
    controls.update();
}

function crearParedArquitectonica(datos) {
    const pared = normalizarPared({
        id: Date.now() + Math.floor(Math.random() * 1000),
        openings: [],
        ...datos
    });

    paredesPlano.push(pared);
    renderPared2D(pared);
    crearMallaPared(pared, true);
    return pared;
}

function crearCasaAutomatica() {
    const canvas = document.getElementById("wallCanvas");
    if (!canvas) return;

    limpiarParedes();

    const rect = canvas.getBoundingClientRect();
    const width = leerMedida("wallLength", 4, 1, 30);
    const depth = Math.max(1, Math.min(12, width * 0.75));
    const height = leerMedida("wallHeight", 2.7, 1.5, 10);
    const thickness = leerMedida("wallThickness", grosorParedPorDefecto, 0.12, 0.45);
    const color = document.getElementById("wallColor")?.value || "#edf7f6";
    const originX = Math.round((rect.width / 2 - (width * pixelesPorMetro) / 2) / pixelesPorMetro) * pixelesPorMetro;
    const originY = Math.round((rect.height / 2 - (depth * pixelesPorMetro) / 2) / pixelesPorMetro) * pixelesPorMetro;

    const paredes = [
        { orientation: "horizontal", length: width, canvasX: originX, canvasY: originY },
        { orientation: "horizontal", length: width, canvasX: originX, canvasY: originY + depth * pixelesPorMetro },
        { orientation: "vertical", length: depth, canvasX: originX, canvasY: originY },
        { orientation: "vertical", length: depth, canvasX: originX + width * pixelesPorMetro, canvasY: originY }
    ].map((base) => {
        const pared = {
            ...base,
            height,
            thickness,
            color
        };
        actualizarParedDesdeCanvas(pared);
        return crearParedArquitectonica(pared);
    });

    seleccionarPared(paredes[0].id);
    floor.visible = true;
    gridHelper.visible = true;
    camera.position.set(0, 8, 12);
    controls.target.set(0, 1.4, 0);
    controls.update();
}

function limpiarParedes() {
    transformControls.detach();
    paredSeleccionada = null;
    paredes3D.forEach((pared) => {
        scene.remove(pared);
        pared.traverse((child) => {
            if (child.geometry) child.geometry.dispose();
            if (child.material) child.material.dispose();
        });
    });
    paredes3D.length = 0;
    paredesPlano.length = 0;

    document.querySelectorAll(".wall-segment-2d").forEach((node) => node.remove());
    const hint = document.querySelector(".wall-canvas__hint");
    if (hint) hint.style.display = "grid";
    actualizarPanelParedSeleccionada();
}

function obtenerGrupoPared(id) {
    return paredes3D.find((grupo) => grupo.userData.wallId == id);
}

function obtenerParedPlano(id) {
    return paredesPlano.find((pared) => pared.id == id);
}

function sincronizarParedDesdeGrupo(pared) {
    const grupo = obtenerGrupoPared(pared.id);
    if (!grupo) return;
    pared.x = grupo.position.x;
    pared.z = grupo.position.z;
}

function actualizarPanelParedSeleccionada() {
    const editor = document.getElementById("wallEditor");
    if (!editor) return;

    editor.classList.toggle("is-visible", Boolean(paredSeleccionada));
}

function marcarParedSeleccionada(grupo, activa) {
    grupo.traverse((child) => {
        if (child.isMesh && child.material) {
            child.material.emissive = child.material.emissive || new THREE.Color(0x000000);
            child.material.emissive.set(activa ? 0x1d4f49 : 0x000000);
            child.material.emissiveIntensity = activa ? 0.18 : 0;
        }
    });
}

function seleccionarPared(id, sincronizarFormulario = true) {
    const pared = obtenerParedPlano(id);
    const grupo = obtenerGrupoPared(id);
    if (!pared || !grupo) return;

    paredes3D.forEach((otra) => marcarParedSeleccionada(otra, false));
    document.querySelectorAll(".wall-segment-2d").forEach((segment) => {
        segment.classList.toggle("is-selected", segment.dataset.wallId == id);
    });
    paredSeleccionada = pared;
    marcarParedSeleccionada(grupo, true);
    transformControls.attach(grupo);
    panelPlanoPared?.classList.add("is-open");
    if (sincronizarFormulario) {
        sincronizarFormularioConPared(pared);
    }
    actualizarPanelParedSeleccionada();
}

function reconstruirPared(pared, mantenerFormulario = false) {
    const grupoAnterior = obtenerGrupoPared(pared.id);
    if (grupoAnterior) {
        pared.x = grupoAnterior.position.x;
        pared.z = grupoAnterior.position.z;
        transformControls.detach();
        scene.remove(grupoAnterior);
    }

    let nuevoGrupo;
    try {
        nuevoGrupo = crearMallaPared(pared);
    } catch (error) {
        console.error("No se pudo reconstruir la pared:", error);
        if (grupoAnterior) scene.add(grupoAnterior);
        mostrarToast("Revisa las medidas de la pared", true);
        return;
    }

    if (grupoAnterior) {
        const index = paredes3D.indexOf(grupoAnterior);
        if (index !== -1) paredes3D.splice(index, 1);
        grupoAnterior.traverse((child) => {
            if (child.geometry) child.geometry.dispose();
            if (child.material && child.material.dispose) child.material.dispose();
        });
    }

    seleccionarPared(pared.id, !mantenerFormulario);
    transformControls.attach(nuevoGrupo);
    actualizarSegmento2D(pared);
}

function sincronizarFormularioConPared(pared) {
    document.getElementById("wallLength").value = formatearMedida(pared.length, 1);
    document.getElementById("wallHeight").value = formatearMedida(pared.height, 2);
    document.getElementById("wallThickness").value = formatearMedida(pared.thickness, 2);
    document.getElementById("wallColor").value = pared.color;
}

function aplicarFormularioAParedSeleccionada() {
    if (!paredSeleccionada) return;
    if (
        !campoTieneMedidaValida("wallLength") ||
        !campoTieneMedidaValida("wallHeight") ||
        !campoTieneMedidaValida("wallThickness")
    ) {
        return;
    }

    paredSeleccionada.length = leerMedida("wallLength", paredSeleccionada.length, 0.5, 30);
    paredSeleccionada.height = leerMedida("wallHeight", paredSeleccionada.height, 1.5, 10);
    paredSeleccionada.thickness = leerMedida("wallThickness", paredSeleccionada.thickness, 0.12, 0.45);
    paredSeleccionada.color = document.getElementById("wallColor")?.value || paredSeleccionada.color;
    reconstruirPared(paredSeleccionada, true);
}

function agregarAberturaParedSeleccionada(type) {
    if (!paredSeleccionada) {
        mostrarToast("Selecciona una pared primero", true);
        return;
    }

    const opening = type === "door"
        ? { type, width: 0.92, height: 2.12, bottom: 0, offset: 0 }
        : { type, width: 1.2, height: 1.05, bottom: 1.05, offset: 0 };
    paredSeleccionada.openings.push(opening);
    reconstruirPared(paredSeleccionada);
}

function eliminarParedSeleccionada() {
    if (!paredSeleccionada) return;

    const grupo = obtenerGrupoPared(paredSeleccionada.id);
    if (grupo) {
        transformControls.detach();
        scene.remove(grupo);
        grupo.traverse((child) => {
            if (child.geometry) child.geometry.dispose();
            if (child.material && child.material.dispose) child.material.dispose();
        });
        const meshIndex = paredes3D.indexOf(grupo);
        if (meshIndex !== -1) paredes3D.splice(meshIndex, 1);
    }

    const dataIndex = paredesPlano.indexOf(paredSeleccionada);
    if (dataIndex !== -1) paredesPlano.splice(dataIndex, 1);
    document.querySelector(`.wall-segment-2d[data-wall-id="${paredSeleccionada.id}"]`)?.remove();
    paredSeleccionada = null;
    actualizarPanelParedSeleccionada();
}

function buscarDefinicionObjeto(id) {
    for (const categoria in objetos) {
        const encontrado = objetos[categoria].find((obj) => obj.id === id);
        if (encontrado) return encontrado;
    }
    return null;
}

function serializarProyecto3D() {
    renderer.render(scene, camera);

    return {
        tipo: "3D",
        version: 2,
        escenario: escenarioActual ? document.getElementById("escenarioSelect")?.value || null : null,
        paredes: paredesPlano,
        objetos: objetosInteractivos.map((objeto) => ({
            id: objeto.userData.objectId,
            nombre: objeto.userData.nombre,
            name: objeto.name,
            position: {
                x: objeto.position.x,
                y: objeto.position.y,
                z: objeto.position.z
            },
            rotation: {
                x: objeto.rotation.x,
                y: objeto.rotation.y,
                z: objeto.rotation.z
            }
        })).filter((objeto) => objeto.id),
        thumbnail: renderer.domElement.toDataURL("image/jpeg", 0.72),
        actualizado: new Date().toISOString()
    };
}

function mostrarToast(mensaje, error = false) {
    const toast = document.getElementById("saveToast3D");
    if (!toast) return;
    toast.textContent = mensaje;
    toast.style.background = error ? "#c0392b" : "#136F63";
    toast.classList.add("is-visible");
    setTimeout(() => toast.classList.remove("is-visible"), 2600);
}

function guardarProyecto3D() {
    const nombre = nombreProyectoActual || prompt("Nombre del proyecto 3D:", "Proyecto 3D interior");
    if (!nombre) return;

    nombreProyectoActual = nombre;
    const payload = {
        id: proyectoActualId,
        nombre,
        tipo: "3D",
        data: serializarProyecto3D()
    };

    fetch("../phpFunciones/guardar_proyecto.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
        .then((response) => response.json())
        .then((result) => {
            if (!result.ok) throw new Error(result.message || "No se pudo guardar el proyecto.");
            proyectoActualId = result.id;
            mostrarToast("Proyecto 3D guardado");
            setTimeout(() => {
                window.location.href = "../phpPaginas/modelos_3d.php";
            }, 700);
        })
        .catch((error) => {
            console.error(error);
            mostrarToast(error.message || "Error al guardar", true);
        });
}

function cargarProyecto3DGuardado() {
    if (!proyectoActualId) return;

    fetch(`../phpFunciones/cargar_proyectos.php?id=${encodeURIComponent(proyectoActualId)}`)
        .then((response) => response.json())
        .then((proyecto) => {
            if (!proyecto || !proyecto.data) return;

            nombreProyectoActual = proyecto.nombre || "";
            const data = typeof proyecto.data === "string" ? JSON.parse(proyecto.data) : proyecto.data;
            if (!data || data.tipo !== "3D") return;

            limpiarParedes();
            (data.paredes || []).forEach((pared) => {
                paredesPlano.push(pared);
                renderPared2D(pared);
                crearMallaPared(pared);
            });

            if (data.escenario && escenarios[data.escenario]) {
                const select = document.getElementById("escenarioSelect");
                if (select) select.value = data.escenario;
                cargarEscenario(data.escenario);
            }

            (data.objetos || []).forEach((objetoGuardado) => {
                const definicion = buscarDefinicionObjeto(objetoGuardado.id);
                if (definicion) colocarObjeto(definicion, objetoGuardado);
            });
        })
        .catch((error) => {
            console.error("No se pudo cargar el proyecto 3D:", error);
            mostrarToast("No se pudo cargar el proyecto", true);
        });
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
function limpiarEscenarioImportado() {
    if (!escenarioImportado) return;

    scene.remove(escenarioImportado);

    escenarioImportado.traverse((child) => {
        if (child.geometry) child.geometry.dispose();

        if (child.material) {
            if (Array.isArray(child.material)) {
                child.material.forEach((mat) => {
                    if (mat.map) mat.map.dispose();
                    mat.dispose();
                });
            } else {
                if (child.material.map) child.material.map.dispose();
                child.material.dispose();
            }
        }
    });

    escenarioImportado = null;
}

function crearPlanoConImagen(file, ancho, alto) {
    const url = URL.createObjectURL(file);

    const texture = new THREE.TextureLoader().load(
        url,
        () => {
            texture.needsUpdate = true;
            URL.revokeObjectURL(url);
        },
        undefined,
        (error) => {
            console.error("Error cargando textura:", file.name, error);
        }
    );

    texture.encoding = THREE.sRGBEncoding;

    const material = new THREE.MeshStandardMaterial({
        map: texture,
        side: THREE.DoubleSide,
        roughness: 0.65
    });

    const geometry = new THREE.BoxGeometry(ancho, alto, grosorParedPorDefecto);
    return new THREE.Mesh(geometry, material);
}



function importarEscenarioDesdeImagenes(files) {
    if (!files || files.length !== 5) {
        alert("Debes seleccionar exactamente 5 imagenes: 4 paredes y 1 techo.");
        return;
    }

    limpiarEscenarioImportado();

    if (escenarioActual) {
        scene.remove(escenarioActual);
        escenarioActual = null;
    }

    floor.visible = true;
    gridHelper.visible = false;

    const urls = Array.from(files).map((file) => URL.createObjectURL(file));

    const grupo = new THREE.Group();
    grupo.name = "escenario-importado";

    const ancho = 14;
    const fondo = 14;
    const alto = 6;

    const paredFrontal = crearPlanoConImagen(files[0], ancho, alto);
    paredFrontal.position.set(0, alto / 2, -fondo / 2);
    grupo.add(paredFrontal);

    const paredTrasera = crearPlanoConImagen(files[1], ancho, alto);
    paredTrasera.position.set(0, alto / 2, fondo / 2);
    paredTrasera.rotation.y = Math.PI;
    grupo.add(paredTrasera);

    const paredIzquierda = crearPlanoConImagen(files[2], fondo, alto);
    paredIzquierda.position.set(-ancho / 2, alto / 2, 0);
    paredIzquierda.rotation.y = Math.PI / 2;
    grupo.add(paredIzquierda);

    const paredDerecha = crearPlanoConImagen(files[3], fondo, alto);
    paredDerecha.position.set(ancho / 2, alto / 2, 0);
    paredDerecha.rotation.y = -Math.PI / 2;
    grupo.add(paredDerecha);

    const techo = crearPlanoConImagen(files[4], ancho, fondo);
    techo.position.set(0, alto, 0);
    techo.rotation.x = Math.PI / 2;
    grupo.add(techo);

    grupo.traverse((child) => {
        if (child.isMesh) {
            child.receiveShadow = true;
        }
    });

    escenarioImportado = grupo;
    scene.add(escenarioImportado);

    camera.position.set(0, 7, 14);
    controls.target.set(0, 2.5, 0);
    controls.update();
}

function resetearEscenario() {
    if (escenarioActual) {
        scene.remove(escenarioActual);

        escenarioActual.traverse((child) => {
            if (child.geometry) child.geometry.dispose();

            if (child.material) {
                if (Array.isArray(child.material)) {
                    child.material.forEach((mat) => {
                        if (mat.map) mat.map.dispose();
                        mat.dispose();
                    });
                } else {
                    if (child.material.map) child.material.map.dispose();
                    child.material.dispose();
                }
            }
        });

        escenarioActual = null;
    }

    if (escenarioImportado) {
        limpiarEscenarioImportado();
    }

    floor.visible = true;
    gridHelper.visible = true;

    camera.position.set(0, 10, 15);
    controls.target.set(0, 0, 0);
    controls.update();
}

function actualizarAnimacionesParedes() {
    const ahora = performance.now();

    paredes3D.forEach((pared) => {
        const animacion = pared.userData.animacionEntrada;
        if (!animacion) return;

        const progreso = Math.min(1, (ahora - animacion.inicio) / animacion.duracion);
        const suavizado = 1 - Math.pow(1 - progreso, 3);
        pared.scale.y = Math.max(0.001, suavizado);

        if (progreso >= 1) {
            pared.scale.y = 1;
            delete pared.userData.animacionEntrada;
        }
    });
}


cargarObjetos();
cargarProyecto3DGuardado();

const btnAbrirPlanoPared = document.getElementById("btnAbrirPlanoPared");
const btnCerrarPlanoPared = document.getElementById("btnCerrarPlanoPared");
const panelPlanoPared = document.getElementById("panelPlanoPared");
const wallCanvas = document.getElementById("wallCanvas");
const btnWallHorizontal = document.getElementById("btnWallHorizontal");
const btnWallVertical = document.getElementById("btnWallVertical");
const btnAutoRoom = document.getElementById("btnAutoRoom");
const btnClearWalls = document.getElementById("btnClearWalls");
const btnGuardarProyecto3D = document.getElementById("btnGuardarProyecto3D");
const wallLengthInput = document.getElementById("wallLength");
const wallHeightInput = document.getElementById("wallHeight");
const wallColorInput = document.getElementById("wallColor");
const wallThicknessInput = document.getElementById("wallThickness");
const btnMoveWall = document.getElementById("btnMoveWall");
const btnAddDoor = document.getElementById("btnAddDoor");
const btnAddWindow = document.getElementById("btnAddWindow");
const btnDeleteWall = document.getElementById("btnDeleteWall");

if (btnAbrirPlanoPared && panelPlanoPared) {
    btnAbrirPlanoPared.addEventListener("click", () => {
        panelPlanoPared.classList.add("is-open");
    });
}

if (btnCerrarPlanoPared && panelPlanoPared) {
    btnCerrarPlanoPared.addEventListener("click", () => {
        panelPlanoPared.classList.remove("is-open");
    });
}

if (btnWallHorizontal) {
    btnWallHorizontal.addEventListener("click", () => {
        orientacionParedActiva = "horizontal";
        btnWallHorizontal.style.background = "#136F63";
        btnWallHorizontal.style.color = "#fff";
        if (btnWallVertical) {
            btnWallVertical.style.background = "#FF9F1C";
            btnWallVertical.style.color = "#11152a";
        }
    });
}

if (btnWallVertical) {
    btnWallVertical.addEventListener("click", () => {
        orientacionParedActiva = "vertical";
        btnWallVertical.style.background = "#136F63";
        btnWallVertical.style.color = "#fff";
        if (btnWallHorizontal) {
            btnWallHorizontal.style.background = "#FF9F1C";
            btnWallHorizontal.style.color = "#11152a";
        }
    });
}

if (btnClearWalls) {
    btnClearWalls.addEventListener("click", limpiarParedes);
}

if (wallCanvas) {
    wallCanvas.addEventListener("click", agregarParedDesdePlano);
}

if (btnAutoRoom) {
    btnAutoRoom.addEventListener("click", crearCasaAutomatica);
}

[wallLengthInput, wallHeightInput, wallColorInput, wallThicknessInput].forEach((input) => {
    if (input) input.addEventListener("input", aplicarFormularioAParedSeleccionada);
});

if (btnMoveWall) {
    btnMoveWall.addEventListener("click", () => {
        transformControls.setMode("translate");
        if (paredSeleccionada) transformControls.attach(obtenerGrupoPared(paredSeleccionada.id));
    });
}

if (btnAddDoor) {
    btnAddDoor.addEventListener("click", () => agregarAberturaParedSeleccionada("door"));
}

if (btnAddWindow) {
    btnAddWindow.addEventListener("click", () => agregarAberturaParedSeleccionada("window"));
}

if (btnDeleteWall) {
    btnDeleteWall.addEventListener("click", eliminarParedSeleccionada);
}

renderer.domElement.addEventListener("pointerdown", (event) => {
    if (transformControls.dragging) return;

    const rect = renderer.domElement.getBoundingClientRect();
    mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);

    const intersections = raycaster.intersectObjects(paredes3D, true);
    const hit = intersections.find((item) => item.object.userData?.wallId || item.object.parent?.userData?.wallId);
    if (!hit) return;

    const wallId = hit.object.userData.wallId || hit.object.parent.userData.wallId;
    seleccionarPared(wallId);
});

if (btnGuardarProyecto3D) {
    btnGuardarProyecto3D.addEventListener("click", guardarProyecto3D);
}

const escenarioSelect = document.getElementById("escenarioSelect");
if (escenarioSelect) {
    escenarioSelect.addEventListener("change", (e) => {
        cargarEscenario(e.target.value);
    });
}

function animate() {
    requestAnimationFrame(animate);
    actualizarAnimacionesParedes();
    controls.update();
    renderer.render(scene, camera);
}

animate();

window.addEventListener("resize", () => {
    camera.aspect = obtenerAnchoRender() / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(obtenerAnchoRender(), window.innerHeight);
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
    if ((event.key === "Delete" || event.key === "Backspace") && paredSeleccionada) {
        eliminarParedSeleccionada();
        return;
    }

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

    if (event.key === "Delete" || event.key === "Backspace") {
        eliminarObjetoSeleccionado();
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
}

const inputEscenarioImagenes = document.getElementById("inputEscenarioImagenes");

if (inputEscenarioImagenes) {
    inputEscenarioImagenes.addEventListener("change", (event) => {
        const files = Array.from(event.target.files);

        console.log("Imagenes seleccionadas:", files.length, files);

        if (files.length !== 5) {
            alert("Debes seleccionar exactamente 5 imagenes: 4 paredes y 1 techo.");
            return;
        }

        importarEscenarioDesdeImagenes(files);
        inputEscenarioImagenes.value = "";
    });
} else {
    console.error("No se encontro el input #inputEscenarioImagenes");
}

const btnResetEscenario = document.getElementById("btnResetEscenario");

if (btnResetEscenario) {
    btnResetEscenario.addEventListener("click", resetearEscenario);
}
