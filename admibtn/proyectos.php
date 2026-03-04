<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Proyectos - Decoración</title>
  <link rel="stylesheet" href="../CSS/Proyectos.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #edf7f6;
      color: #1b1f3b;
      margin: 0;
      padding: 20px;
    }
    h1 {
      color: #136f36;
    }
    .proyecto-form {
      margin-bottom: 20px;
      background: #9067c6;
      padding: 15px;
      border-radius: 8px;
      color: #fff;
    }
    input, button {
      padding: 8px;
      margin: 5px;
      border: none;
      border-radius: 5px;
    }
    input {
      width: 200px;
    }
    button {
      background-color: #ff9f1c;
      color: #fff;
      cursor: pointer;
    }
    button:hover {
      background-color: #136f36;
    }
    .lista-proyectos {
      list-style: none;
      padding: 0;
    }
    .proyecto {
      background: #fff;
      border: 2px solid #1b1f3b;
      margin-bottom: 10px;
      padding: 10px;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .proyecto.completado {
      background: #136f36;
      color: #fff;
      text-decoration: line-through;
    }
  </style>
</head>
<body>

  <h1>Panel de Proyectos - Administrador</h1>

  <div class="proyecto-form">
    <h2>Agregar nuevo proyecto</h2>
    <input type="text" id="nombreProyecto" placeholder="Nombre del proyecto">
    <button onclick="agregarProyecto()">Agregar</button>
  </div>

  <h2>Lista de proyectos</h2>
  <ul id="listaProyectos" class="lista-proyectos"></ul>

  <script>
    const lista = document.getElementById("listaProyectos");

    // Al cargar la página, recuperar proyectos guardados
    window.onload = () => {
      const proyectosGuardados = JSON.parse(localStorage.getItem("proyectos")) || [];
      proyectosGuardados.forEach(p => renderProyecto(p.nombre, p.completado));
    };

    function agregarProyecto() {
      const nombre = document.getElementById("nombreProyecto").value.trim();
      if (nombre === "") return alert("Escribe un nombre para el proyecto");

      renderProyecto(nombre, false);
      guardarProyectos();
      document.getElementById("nombreProyecto").value = "";
    }

    function renderProyecto(nombre, completado) {
      const li = document.createElement("li");
      li.classList.add("proyecto");
      if (completado) li.classList.add("completado");

      li.innerHTML = `
        <span>${nombre}</span>
        <div>
          <button onclick="completarProyecto(this)">✔</button>
          <button onclick="eliminarProyecto(this)">✖</button>
        </div>
      `;
      lista.appendChild(li);
    }

    function completarProyecto(btn) {
      const proyecto = btn.closest(".proyecto");
      proyecto.classList.toggle("completado");
      guardarProyectos();
    }

    function eliminarProyecto(btn) {
      const proyecto = btn.closest(".proyecto");
      proyecto.remove();
      guardarProyectos();
    }

    function guardarProyectos() {
      const proyectos = [];
      document.querySelectorAll(".proyecto").forEach(p => {
        proyectos.push({
          nombre: p.querySelector("span").textContent,
          completado: p.classList.contains("completado")
        });
      });
      localStorage.setItem("proyectos", JSON.stringify(proyectos));
    }
  </script>

</body>
</html>
