const editor = document.getElementById("editor");
const btnRect = document.getElementById("btnRect");
const btnCirc = document.getElementById("btnCirc");
const btnDelete = document.getElementById("btnDelete");
const btnCopy = document.getElementById("btnCopy");
const btnPaste = document.getElementById("btnPaste");
const btnCut = document.getElementById("btnCut");

let mode = null;
let drawing = false;
let startX = 0, startY = 0;
let currentShape = null;
let selectedShape = null;
let copiedShape = null;

let history = [];
let redoStack = [];

function saveState() {
  history.push(editor.innerHTML);
  redoStack = [];
}

function restoreState(state) {
  editor.innerHTML = state;
  selectedShape = null;
}

// Cambiar modo
btnRect.onclick = () => mode = "rect";
btnCirc.onclick = () => mode = "circ";

// Dibujar figuras
editor.addEventListener("mousedown", (e) => {
  // Evitar dibujar si se hace clic sobre una figura existente
  if ((mode === "rect" || mode === "circ") && !e.target.classList.contains("shape")) {
    drawing = true;
    startX = e.offsetX;
    startY = e.offsetY;

    currentShape = document.createElement("section");
    currentShape.classList.add("shape");
    currentShape.style.left = `${startX}px`;
    currentShape.style.top = `${startY}px`;
    currentShape.style.width = "0px";
    currentShape.style.height = "0px";
    currentShape.style.position = "absolute";
    currentShape.style.backgroundColor = mode === "rect" ? "rgba(0,128,255,0.5)" : "rgba(255,100,100,0.5)";
    if (mode === "circ") currentShape.style.borderRadius = "50%";

    editor.appendChild(currentShape);
    saveState();
  }
});

editor.addEventListener("mousemove", (e) => {
  if (drawing && currentShape) {
    const width = e.offsetX - startX;
    const height = e.offsetY - startY;
    currentShape.style.width = `${width}px`;
    currentShape.style.height = `${height}px`;
  }
});

editor.addEventListener("mouseup", () => {
  drawing = false;
  currentShape = null;
});

// Selección directa y movimiento
editor.addEventListener("mousedown", (e) => {
  if (e.target.classList.contains("shape")) {
    selectedShape?.classList.remove("selected");
    selectedShape = e.target;
    selectedShape.classList.add("selected");

    const offsetX = e.offsetX - selectedShape.offsetLeft;
    const offsetY = e.offsetY - selectedShape.offsetTop;

    function mover(ev) {
      selectedShape.style.left = `${ev.offsetX - offsetX}px`;
      selectedShape.style.top = `${ev.offsetY - offsetY}px`;
    }

    function soltar() {
      editor.removeEventListener("mousemove", mover);
      editor.removeEventListener("mouseup", soltar);
      saveState();
    }

    editor.addEventListener("mousemove", mover);
    editor.addEventListener("mouseup", soltar);
  }
});

// Acciones
btnDelete.onclick = () => {
  if (selectedShape) {
    selectedShape.remove();
    selectedShape = null;
    saveState();
  }
};

btnCopy.onclick = () => {
  if (selectedShape) {
    copiedShape = selectedShape.cloneNode(true);
  }
};

btnPaste.onclick = () => {
  if (copiedShape) {
    const nueva = copiedShape.cloneNode(true);
    nueva.style.left = `${parseInt(copiedShape.style.left) + 20}px`;
    nueva.style.top = `${parseInt(copiedShape.style.top) + 20}px`;
    editor.appendChild(nueva);
    saveState();
  }
};

btnCut.onclick = () => {
  if (selectedShape) {
    copiedShape = selectedShape.cloneNode(true);
    selectedShape.remove();
    selectedShape = null;
    saveState();
  }
};

// Atajos de teclado
document.addEventListener("keydown", (e) => {
  if (e.ctrlKey && e.key === "z") {
    e.preventDefault();
    if (history.length > 1) {
      redoStack.push(history.pop());
      restoreState(history[history.length - 1]);
    }
  }

if (e.ctrlKey && e.key.toLowerCase() === "y") {
    e.preventDefault();
    if (redoStack.length > 0) {
      const state = redoStack.pop();
      history.push(state);
      restoreState(state);
    }
  }

  if (e.ctrlKey && e.key === "c") {
    e.preventDefault();
    btnCopy.click();
  }

  if (e.ctrlKey && e.key === "x") {
    e.preventDefault();
    btnCut.click();
  }

  if (e.ctrlKey && e.key === "v") {
    e.preventDefault();
    btnPaste.click();
  }

  if (e.key === "Delete") {
    e.preventDefault();
    btnDelete.click();
  }
});
window.addEventListener("DOMContentLoaded", () => {
  saveState(); // Guarda el estado inicial vacío
});