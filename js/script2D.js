const editor = document.getElementById("editor");
const btnRect = document.getElementById("btnRect");
const btnCirc = document.getElementById("btnCirc");
const btnSelect = document.getElementById("btnSelect");
const btnDelete = document.getElementById("btnDelete");
const btnCopy = document.getElementById("btnCopy");
const btnPaste = document.getElementById("btnPaste");
const btnCut = document.getElementById("btnCut");

let mode = null;
let startX, startY;
let currentShape = null;
let selectedShape = null;
let copiedShape = null;

// Historial para deshacer/rehacer
let history = [];
let redoStack = [];

function saveState() {
  history.push(editor.innerHTML);
  redoStack = []; // limpiar pila de rehacer
}

// Cambiar modo
btnRect.onclick = () => mode = "rect";
btnCirc.onclick = () => mode = "circ";
btnSelect.onclick = () => mode = "select";

// Dibujar figuras con el mouse
editor.addEventListener("mousedown", e => {
  if (mode === "rect" || mode === "circ") {
    startX = e.offsetX;
    startY = e.offsetY;

    currentShape = document.createElement("div");
    currentShape.classList.add("shape");

    if (mode === "rect") {
      currentShape.style.backgroundColor = "rgba(0,128,255,0.5)";
    } else {
      currentShape.style.backgroundColor = "rgba(255,100,100,0.5)";
      currentShape.style.borderRadius = "50%";
    }

    currentShape.style.left = startX + "px";
    currentShape.style.top = startY + "px";
    currentShape.style.width = "0px";
    currentShape.style.height = "0px";

    editor.appendChild(currentShape);
    saveState();
  }
});

editor.addEventListener("mousemove", e => {
  if (currentShape) {
    const width = e.offsetX - startX;
    const height = e.offsetY - startY;
    currentShape.style.width = width + "px";
    currentShape.style.height = height + "px";
  }
});

editor.addEventListener("mouseup", () => {
  currentShape = null;
});

// Seleccionar figura
editor.addEventListener("click", e => {
  if (mode === "select" && e.target.classList.contains("shape")) {
    if (selectedShape) selectedShape.style.outline = "";
    selectedShape = e.target;
    selectedShape.style.outline = "2px dashed black";
  }
});

// Mover figura seleccionada
let offsetX, offsetY;
editor.addEventListener("mousedown", e => {
  if (mode === "select" && e.target === selectedShape) {
    offsetX = e.offsetX - selectedShape.offsetLeft;
    offsetY = e.offsetY - selectedShape.offsetTop;
    editor.addEventListener("mousemove", moveShape);
  }
});

editor.addEventListener("mouseup", () => {
  editor.removeEventListener("mousemove", moveShape);
});

function moveShape(e) {
  selectedShape.style.left = (e.offsetX - offsetX) + "px";
  selectedShape.style.top = (e.offsetY - offsetY) + "px";
}

// Borrar
btnDelete.onclick = () => {
  if (selectedShape) {
    editor.removeChild(selectedShape);
    selectedShape = null;
    saveState();
  }
};

// Copiar y pegar
btnCopy.onclick = () => {
  if (selectedShape) {
    copiedShape = selectedShape.cloneNode(true);
  }
};

btnPaste.onclick = () => {
  if (copiedShape) {
    const newShape = copiedShape.cloneNode(true);
    newShape.style.left = (parseInt(copiedShape.style.left) + 20) + "px";
    newShape.style.top = (parseInt(copiedShape.style.top) + 20) + "px";
    editor.appendChild(newShape);
    saveState();
  }
};

// Cortar
btnCut.onclick = () => {
  if (selectedShape) {
    copiedShape = selectedShape.cloneNode(true);
    editor.removeChild(selectedShape);
    selectedShape = null;
    saveState();
  }
};

// Deshacer y rehacer con Ctrl+Z / Ctrl+Y
document.addEventListener("keydown", e => {
  if (e.ctrlKey && e.key === "z") {
    if (history.length > 1) {
      redoStack.push(history.pop());
      editor.innerHTML = history[history.length - 1];
    }
  }
  if (e.ctrlKey && e.key === "y") {
    if (redoStack.length > 0) {
      const state = redoStack.pop();
      history.push(state);
      editor.innerHTML = state;
    }
  }
});