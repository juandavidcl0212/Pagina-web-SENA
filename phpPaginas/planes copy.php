<!DOCTYPE html> 
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Planes</title>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #d3d9da;
    }

    h1 {
        text-align: center;
        color: orange;
        margin-top: 20px;
    }

    .controls {
        text-align: center;
        margin-top: 20px;
    }

    .controls input, .controls select, .controls button {
        padding: 8px;
        margin: 5px;
        border-radius: 5px;
        border: none;
    }

    .container {
        background: #1f2747;
        margin: 40px auto;
        padding: 40px;
        border-radius: 10px;
        width: 80%;
        display: flex;
        justify-content: space-around;
    }

    .card {
        background: #1f7a6b;
        width: 250px;
        padding: 20px;
        border-radius: 10px;
        color: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .card:nth-child(2) {
        background: #dfe5e6;
        color: #333;
    }

    .price {
        background: rgba(0,0,0,0.2);
        padding: 15px;
        margin: 20px 0;
        border-radius: 10px;
        text-align: center;
        color: #c084fc;
        font-weight: bold;
    }

    .amount {
        font-size: 22px;
    }

    .period {
        font-size: 14px;
    }
</style>
</head>

<body>

<h1>PLANES</h1>

<div class="controls">
    <select id="planSelect">
        <option value="0">Personal</option>
        <option value="1">Familiar</option>
        <option value="2">Institucional</option>
    </select>

    <input type="number" id="newPrice" placeholder="Nuevo precio">

    <button onclick="cambiarPrecio()">Cambiar Precio</button>
</div>

<div class="container">

    <div class="card">
        <h3>Personal</h3>
        <div class="price">
            <span class="amount">$30</span>
            <span class="period">/mes</span>
        </div>
    </div>

    <div class="card">
        <h3>Familiar</h3>
        <div class="price">
            <span class="amount">$90</span>
            <span class="period">/mes</span>
        </div>
    </div>

    <div class="card">
        <h3>Institucional</h3>
        <div class="price">
            <span class="amount">$250</span>
            <span class="period">/mes</span>
        </div>
    </div>

</div>

<script>
function cambiarPrecio() {
    let planIndex = document.getElementById("planSelect").value;
    let nuevoPrecio = document.getElementById("newPrice").value;

    if(nuevoPrecio === "" || nuevoPrecio <= 0){
        alert("Ingresa un precio válido");
        return;
    }

    let cards = document.querySelectorAll(".card");
    let precioElemento = cards[planIndex].querySelector(".amount");

    precioElemento.textContent = "$" + nuevoPrecio;
}
</script>

</body>
</html>






 



