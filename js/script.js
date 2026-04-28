<script>
// =========================
// FUNCIONES GENERALES (DISEÑO)
// =========================

// Ir a diseño 2D o 3D
function irDiseno(tipo) {

    // Verificar si hay sesión (lo guardas al iniciar sesión)
    const usuario = sessionStorage.getItem("usuario");

    if (!usuario) {
        alert("Debes iniciar sesión primero");
        window.location.href = "phpPaginas/inSesion.php";
        return;
    }

    if (tipo === "3d") {
        window.location.href = "diseño/pagina3d.php";
    } else {
        window.location.href = "html/DISEÑO2D.html";
    }
}

// Redirigir a login
function redirigirLogin() {
    alert("Debes iniciar sesión primero");
    window.location.href = "phpPaginas/inSesion.php";
}

// Menú de perfil
function toggleMenu() {
    const menu = document.getElementById("menuPopup");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}


// =========================
// SCRIPT PARA ADMINISTRADORES
// =========================

window.addEventListener("load", function() {
    const rol = sessionStorage.getItem("rol"); // Rol guardado al iniciar sesión
    
    if(rol === "admin") {
        // Plan Personal
        mostrarAdmin("plan1B", "Personal", "$30 USD");

        // Plan Familiar
        mostrarAdmin("plan2B", "Familiar", "$90 USD");

        // Plan Institucional
        mostrarAdmin("plan3B", "Institucional", "$250 USD");
    }
});

// Función para crear inputs de admin dinámicamente
function mostrarAdmin(botonId, planNombre, precioActual) {
    const boton = document.getElementById(botonId);

    if(!boton) return;

    // Ocultar botón COMPRAR normal
    boton.style.display = "none";

    // Crear contenedor admin
    const adminDiv = document.createElement("div");
    adminDiv.style.border = "1px solid #ccc";
    adminDiv.style.padding = "10px";
    adminDiv.style.marginTop = "10px";
    adminDiv.style.backgroundColor = "#f9f9f9";

    // Mostrar precio actual
    const pPrecio = document.createElement("p");
    pPrecio.textContent = "Precio actual: " + precioActual;
    adminDiv.appendChild(pPrecio);

    // Input para nuevo precio
    const input = document.createElement("input");
    input.type = "number";
    input.placeholder = "Nuevo precio USD";
    input.required = true;
    input.style.marginRight = "10px";
    adminDiv.appendChild(input);

    // Botón para actualizar
    const btnActualizar = document.createElement("button");
    btnActualizar.textContent = "Actualizar precio";
    btnActualizar.onclick = function() {

        if(!input.value || input.value <= 0){
            alert("Ingresa un precio válido");
            return;
        }

        // Formulario dinámico
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "../php/cambiar_plan.php";

        const inputPlan = document.createElement("input");
        inputPlan.type = "hidden";
        inputPlan.name = "plan";
        inputPlan.value = planNombre;
        form.appendChild(inputPlan);

        const inputPrecio = document.createElement("input");
        inputPrecio.type = "hidden";
        inputPrecio.name = "precio";
        inputPrecio.value = input.value;
        form.appendChild(inputPrecio);

        document.body.appendChild(form);
        form.submit();
    };

    adminDiv.appendChild(btnActualizar);

    // Insertar después del botón original
    boton.parentNode.appendChild(adminDiv);
}
</script>