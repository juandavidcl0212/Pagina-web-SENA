<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plano 3D Interactivo</title>
    <!-- Librería Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- Controles de cámara y arrastre -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/DragControls.js"></script>
    <style>
        body { margin: 0; display: flex; }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            overflow-y: auto;
            padding: 20px;
            z-index: 2;
        }
        .sidebar h2 { text-align: center; margin-bottom: 15px; }
        .categoria { margin-bottom: 20px; }
        .categoria h3 {
            border-bottom: 1px solid #fff;
            padding-bottom: 5px;
            cursor: pointer;
        }
        .objetos { display: none; margin-top: 10px; }
        .objeto {
            display: flex; align-items: center;
            margin-bottom: 8px; background-color: #34495e;
            padding: 5px; border-radius: 5px; cursor: pointer;
        }
        .objeto img {
            width: 40px; height: 40px; margin-right: 10px;
            border-radius: 5px; object-fit: cover;
        }
        .objeto span { font-size: 14px; }
        #espacio3D {
            flex: 1;
            height: 100vh;
            position: relative;
            background: #ccc;
        }
        #espacio3D canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }
        .btn-volver {
            position: absolute; top: 20px; left: 270px;
            padding: 10px 15px; background: #333; color: #fff;
            text-decoration: none; border-radius: 5px; z-index: 3;
        }
    </style>
</head>
<body>
    <!-- Barra lateral -->
    <div class="sidebar">
        <h2>Festividades</h2>
        <div class="categoria">
            <h3 onclick="toggleCategoria(this)">Navidad</h3>
            <div class="objetos" id="navidad"></div>
        </div>
        <div class="categoria">
            <h3 onclick="toggleCategoria(this)">Halloween</h3>
            <div class="objetos" id="halloween"></div>
        </div>
        <div class="categoria">
            <h3 onclick="toggleCategoria(this)">Cumpleaños</h3>
            <div class="objetos" id="cumple"></div>
        </div>
        <div class="categoria">
            <h3 onclick="toggleCategoria(this)">San Valentín</h3>
            <div class="objetos" id="sanvalentin"></div>
        </div>
        <div class="categoria">
            <h3 onclick="toggleCategoria(this)">Otros</h3>
            <div class="objetos" id="otros"></div>
        </div>
    </div>

    <!-- Área 3D -->
    <div id="espacio3D"></div>
    <a href="../phpPaginas/biblioteca.php" class="btn-volver">Volver</a>

    <script>
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, (window.innerWidth-250)/window.innerHeight, 0.1, 1000);
        camera.position.set(0, 5, 15);

        const renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth-250, window.innerHeight);
        document.getElementById("espacio3D").appendChild(renderer.domElement);

        // OrbitControls para modo visita 360°
        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.enablePan = true;
        controls.enableZoom = true;

        // Luz
        const light = new THREE.DirectionalLight(0xffffff, 1);
        light.position.set(5, 10, 7.5);
        scene.add(light);

        const textureLoader = new THREE.TextureLoader();
        const floorTexture = textureLoader.load('../assets/objetos/piso.jpg');
        const wallTexture = textureLoader.load('../assets/objetos/pared.jpg');

        const floor = new THREE.Mesh(new THREE.PlaneGeometry(20, 20), new THREE.MeshStandardMaterial({ map: floorTexture }));
        floor.rotation.x = -Math.PI / 2;
        scene.add(floor);

        const wall = new THREE.Mesh(new THREE.BoxGeometry(20, 5, 0.5), new THREE.MeshStandardMaterial({ map: wallTexture }));
        wall.position.set(0, 2.5, -10);
        scene.add(wall);

        // Objetos por temática
        const objetosPorTematica = {
            navidad: [
                { id:"arbol", nombre: "Árbol de Navidad", img: "../assets/objetos/arbol-navidad.png", ancho: 2, alto: 4, posX: -5, posY: 0 },
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
                { id:"corazon", nombre: "Globo Corazón", img: "../assets/objetos/globo-corazon.png", ancho: 1, alto: 2, posX: -3, posY: 0 }
            ],
            otros: [
                { id:"cama", nombre: "Cama", img: "../assets/objetos/cama.png", ancho: 3, alto: 1, posX: 0, posY: 3 },
                { id:"tocador", nombre: "Tocador", img: "../assets/objetos/tocador.png", ancho: 2, alto: 2, posX: 5, posY: -2 }
            ]
        };

        const objetosInteractivos = [];
        let dragControls;

        function colocarObjeto(obj) {
            let existente = scene.getObjectByName(obj.id);
            if (existente) return;

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
        }
        cargarObjetos();

        function animate() {
            requestAnimationFrame(animate);
            controls.update
            function animate() {
            requestAnimationFrame(animate);
            controls.update(); // actualizar cámara (modo visita)
            renderer.render(scene, camera);
        }
        }
          
        animate();

        // Ajustar al tamaño de la ventana
        window.addEventListener("resize", () => {
            camera.aspect = (window.innerWidth-250) / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth-250, window.innerHeight);
        });

        // Toggle categorías (abrir/cerrar menú)
        function toggleCategoria(elemento) {
            const lista = elemento.nextElementSibling;
            lista.style.display = (lista.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>
