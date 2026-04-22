<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];

    $destino = "tucorreo@empresa.com"; // 🔴 cambia esto

    $contenido = "Nombre: $nombre\nEmail: $email\nMensaje: $mensaje";

    $headers = "From: $email";

    if(mail($destino, "Nuevo mensaje de contacto", $contenido, $headers)){
        echo "ok";
    } else {
        echo "error";
    }
}
?>