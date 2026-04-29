function toggleMenu() {
  const menu = document.getElementById("menuPopup");
  menu.classList.toggle("show");
}
function irDiseno(tipo) {
  if (tipo === "3d") {
    window.location.href = "diseño/pagina3d.php";
  } else {
    window.location.href = "html/DISEÑO2D.html";
  }
}

function redirigirLogin() {
  alert("Debes iniciar sesión primero");
  window.location.href = "phpPaginas/inSesion.php";
}