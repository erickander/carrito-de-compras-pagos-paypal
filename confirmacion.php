<?php
require_once 'config.php';

$order_id = $_GET['order_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Completado</title>

    <style>

        body{
            font-family:Arial;
            text-align:center;
            padding:50px;
        }

        .ok{
            color:green;
            font-size:25px;
        }

    </style>

</head>
<body>

<div class="ok">
    ✅ Pago realizado correctamente
</div>

<br>

Orden:

<strong>
    <?php echo htmlspecialchars($order_id); ?>
</strong>

</body>
</html>