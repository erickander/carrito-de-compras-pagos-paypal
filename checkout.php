<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>

    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=USD"></script>

    <style>
        body{
            font-family: Arial;
            padding:40px;
            background:#f5f5f5;
        }

        .contenedor{
            max-width:600px;
            margin:auto;
            background:white;
            padding:30px;
            border-radius:10px;
        }

        input{
            width:100%;
            padding:12px;
            margin-bottom:15px;
        }

        #paypal-button-container{
            margin-top:20px;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h2>Checkout PayPal</h2>

    <form id="formulario">

        <input type="text" id="nombre" placeholder="Nombre completo" required>

        <input type="email" id="email" placeholder="Correo electrónico" required>

        <input type="text" id="direccion" placeholder="Dirección" required>

        <input type="text" id="telefono" placeholder="Teléfono" required>

    </form>

    <h3>Total: $50.00</h3>

    <div id="paypal-button-container"></div>

</div>

<script>

paypal.Buttons({

    createOrder: async function() {

        const nombre = document.getElementById('nombre').value;
        const email = document.getElementById('email').value;
        const direccion = document.getElementById('direccion').value;
        const telefono = document.getElementById('telefono').value;

        if(!nombre || !email || !direccion || !telefono){

            alert("Completa todos los campos");
            return;
        }

        const response = await fetch('procesar_paypal.php', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({
                nombre,
                email,
                direccion,
                telefono
            })
        });

        const data = await response.json();

        if(data.id){
            return data.id;
        }else{
            alert(data.error);
        }
    },

    onApprove: async function(data, actions){

        const details = await actions.order.capture();

        window.location.href =
            "confirmacion.php?order_id=" + data.orderID;

    },

    onError: function(err){

        console.log(err);

        alert("Error con PayPal");
    }

}).render('#paypal-button-container');

</script>

</body>
</html>