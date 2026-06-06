<?php
require_once 'config.php';
require_once 'includes/funciones.php';

$order_id = $_GET['order_id'] ?? '';
$titulo = 'Pago Completado - TechShop';

require_once 'includes/header.php';
?>

<section class="confirmacion-contenedor">
    <p class="section-kicker">PayPal</p>
    <h2 class="confirmacion-titulo">Pago realizado correctamente</h2>
    <p class="confirmacion-numero">Orden: <strong><?php echo sanitizar($order_id); ?></strong></p>
    <div class="confirmacion-detalles">
        <p>Gracias por comprar en TechShop.</p>
        <p>Tu pago fue aprobado y registrado por PayPal.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary">Volver al catalogo</a>
</section>

<?php require_once 'includes/footer.php'; ?>
