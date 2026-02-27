const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");

const btnRect = document.getElementById("btnRect");
const btnCirc = document.getElementById("btnCirc");
const btnCut = document.getElementById("btnCut");
const btnCopy = document.getElementById("btnCopy");
const btnPaste = document.getElementById("btnPaste");
const btnDelete = document.getElementById("btnDelete");

let mode = null;
let shapes = [];
let selected = null;
let copied = null;
let history = [];
let redoStack = [];

function saveState() {
  history.push(JSON.stringify(shapes));
  if (history.length > 50) history.shift();
  redoStack = [];
}

function restoreState(state) {
  shapes = JSON.parse(state);
  selected = null;
  draw();
}

//funcion para dibujar
function draw() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  shapes.forEach(shape => {
    ctx.beginPath();
    ctx.fillStyle = shape.color;
    if (shape.type === "rect") {
      ctx.fillRect(shape.x, shape.y, shape.w, shape.h);
    } else if (shape.type === "circ") {
      ctx.ellipse(shape.x + shape.w / 2, shape.y + shape.h / 2, shape.w / 2, shape.h / 2, 0, 0, Math.PI * 2);
      ctx.fill();
    }
    if (shape === selected) {
      ctx.strokeStyle = "black";
      ctx.lineWidth = 2;
      ctx.stroke();
    }
  });
}

btnRect.onclick = () => mode = "rect";
btnCirc.onclick = () => mode = "circ";

let isDrawing = false;
let startX, startY;
let isDragging = false;
let dragOffsetX = 0;
let dragOffsetY = 0;

//funciones para crear figura y moverla
canvas.addEventListener("mousedown", (e) => {
  const { offsetX, offsetY } = e;
  const clicked = getShapeAt(offsetX, offsetY);

  if (mode && !clicked) {
    // Iniciar dibujo
    isDrawing = true;
    startX = offsetX;
    startY = offsetY;
    selected = null;
  } else if (!mode && clicked) {
    // Iniciar movimiento
    selected = clicked;
    dragOffsetX = offsetX - selected.x;
    dragOffsetY = offsetY - selected.y;
    isDragging = true;
  }

  draw();
});


canvas.addEventListener("mousemove", (e) => {
  if (isDrawing) {
    const w = e.offsetX - startX;
    const h = e.offsetY - startY;
    draw();
    ctx.beginPath();
    ctx.fillStyle = mode === "rect" ? "rgba(0,128,255,0.5)" : "rgba(255,100,100,0.5)";
    if (mode === "rect") {
      ctx.fillRect(startX, startY, w, h);
    } else {
    ctx.ellipse(
      startX + w / 2,
      startY + h / 2,
     Math.abs(w / 2),
      Math.abs(h / 2),
      0,
      0,
     Math.PI * 2
    );
    ctx.fill();
    }
  }
});



canvas.addEventListener("mouseup", (e) => {
  const { offsetX, offsetY } = e;

  if (isDrawing) {
    const w = offsetX - startX;
    const h = offsetY - startY;
    shapes.push({
      type: mode,
      x: startX,
      y: startY,
      w,
      h,
      color: mode === "rect" ? "rgba(0,128,255,0.5)" : "rgba(255,100,100,0.5)"
    });
    isDrawing = false;
    saveState();
    draw();
  }

  if (isDragging) {
    isDragging = false;
    saveState();
  }
});


function getShapeAt(x, y) {
  for (let i = shapes.length - 1; i >= 0; i--) {
    const s = shapes[i];
    if (s.type === "rect" &&
        x >= s.x && x <= s.x + s.w &&
        y >= s.y && y <= s.y + s.h) return s;
    if (s.type === "circ") {
      const dx = x - (s.x + s.w / 2);
      const dy = y - (s.y + s.h / 2);
      const rx = s.w / 2;
      const ry = s.h / 2;
      if ((dx * dx) / (rx * rx) + (dy * dy) / (ry * ry) <= 1) return s;
    }
  }
  return null;
}

// Acciones
btnDelete.onclick = () => {
  if (selected) {
    shapes = shapes.filter(s => s !== selected);
    selected = null;
    saveState();
    draw();
  }
};

btnCopy.onclick = () => {
  if (selected) copied = { ...selected };
};

btnPaste.onclick = () => {
  if (copied) {
    const nueva = { ...copied, x: copied.x + 20, y: copied.y + 20 };
    shapes.push(nueva);
    selected = nueva;
    saveState();
    draw();
  }
};

btnCut.onclick = () => {
  if (selected) {
    copied = { ...selected };
    shapes = shapes.filter(s => s !== selected);
    selected = null;
    saveState();
    draw();
  }
};

// Atajos de teclado
document.addEventListener("keydown", (e) => {
  if (e.ctrlKey && e.key.toLowerCase() === "z") {
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

// Inicializar
window.addEventListener("DOMContentLoaded", () => {
  saveState();
  draw();
});