function abrirVentana(seccion) {
    const modal = document.getElementById("ventana-modal");
    const titulo = document.getElementById("modal-titulo");
    const texto = document.getElementById("modal-texto");
  
    // Contenido dinámico según la sección
    switch(seccion) {
      case 'inicio':
        titulo.textContent = "Bienvenido a Nook";
        texto.textContent = "Descubre cómo transformar tus espacios con nuestras herramientas de diseño digital.";
        break;
      case 'servicios':
        titulo.textContent = "Nuestros Servicios";
        texto.textContent = "Ofrecemos asesoría en diseño, herramientas de modelado 3D y personalización para tus espacios.";
        break;
      case 'planes':
        titulo.textContent = "Planes Disponibles";
        texto.textContent = "Elige entre nuestros planes: Básico, Premium y Profesional, cada uno con beneficios únicos.";
        break;
      case 'contacto':
        titulo.textContent = "Contáctanos";
        texto.textContent = "¿Tienes dudas o necesitas ayuda? Escríbenos a soporte@nook.com o llámanos al +34 600 123 456.";
        break;
    }
  
    modal.style.display = "block";
  }
  
  function cerrarVentana() {
    document.getElementById("ventana-modal").style.display = "none";
  }
  
  // Cerrar al hacer clic fuera del contenido
  window.onclick = function(event) {
    const modal = document.getElementById("ventana-modal");
    if (event.target === modal) {
      modal.style.display = "none";
    }
  }
  function abrirDiseno3D() {
    document.getElementById("ventana-3d").style.display = "flex";
  }
  
  function cerrarDiseno3D() {
    document.getElementById("ventana-3d").style.display = "none";
  }
  
  // Cerrar si se hace clic fuera de la ventana
  window.onclick = function(e) {
    const modal = document.getElementById("ventana-3d");
    if (e.target === modal) {
      modal.style.display = "none";
    }
  };
  /* ============================
     ABRIR / CERRAR MODAL
============================ */
function abrirCuenta() {
  document.getElementById("modal-cuenta").style.display = "flex";
}

function cerrarCuenta() {
  document.getElementById("modal-cuenta").style.display = "none";
}

/* Alternar login/registro */
function mostrarRegistro() {
  document.getElementById("login").classList.remove("visible");
  document.getElementById("registro").classList.add("visible");
}

function mostrarLogin() {
  document.getElementById("registro").classList.remove("visible");
  document.getElementById("login").classList.add("visible");
}

/* ============================
     REGISTRAR USUARIO
============================ */
function registrarUsuario() {
  let nombre = document.getElementById("reg-nombre").value;
  let email = document.getElementById("reg-email").value;
  let pass = document.getElementById("reg-pass").value;

  if (!nombre || !email || !pass) {
    alert("Complete todos los campos.");
    return;
  }

  let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

  let existe = usuarios.find(u => u.email === email);

  if (existe) {
    alert("Este correo ya está registrado.");
    return;
  }

  usuarios.push({ nombre, email, pass });
  localStorage.setItem("usuarios", JSON.stringify(usuarios));

  alert("Cuenta creada con éxito. Ahora inicia sesión.");
  mostrarLogin();
}

/* ============================
         LOGIN
============================ */
function iniciarSesion() {
  let email = document.getElementById("login-email").value;
  let pass = document.getElementById("login-pass").value;

  let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

  let usuario = usuarios.find(u => u.email === email && u.pass === pass);

  if (usuario) {
    alert("Bienvenido " + usuario.nombre);
    cerrarCuenta();
  } else {
    alert("Correo o contraseña incorrectos.");
  }
}

function iniciarSesion() {
  let email = document.getElementById("login-email").value;
  let pass = document.getElementById("login-pass").value;

  let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

  let usuario = usuarios.find(u => u.email === email && u.pass === pass);

  if (usuario) {
    alert("Bienvenido " + usuario.nombre);

    cerrarCuenta();

    // 👉 ABRIR EL DASHBOARD EN UNA NUEVA VENTANA
    window.open("dashboard.html", "_blank");

  } else {
    alert("Correo o contraseña incorrectos.");
  }
}

  