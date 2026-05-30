<?php
require_once 'config.php';
require_once 'includes/funciones.php';

$session_id = $_SESSION['session_id'];
$carrito = obtener_carrito($conexion, $session_id);
$total = calcular_total($conexion, $session_id);

$titulo = 'Carrito de Compras - TechShop';
require_once 'includes/header.php';
?>

<section class="carrito-contenedor">
    <h2 style="margin-bottom: 30px;">🛒 Carrito de Compras</h2>

    <?php if (count($carrito) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="carrito-tabla">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrito as $item): ?>
                        <?php
                        $precio = $item['precio_oferta'] ?? $item['precio'];
                        $subtotal = $precio * $item['cantidad'];
                        ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="producto-imagen" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <?php if ($item['imagen']): ?>
                                            <img src="<?php echo sanitizar($item['imagen']); ?>"
                                                 alt="<?php echo sanitizar($item['nombre']); ?>">
                                        <?php else: ?>
                                            📱
                                        <?php endif; ?>
                                    </div>
                                    <span><?php echo sanitizar($item['nombre']); ?></span>
                                </div>
                            </td>
                            <td>$<?php echo number_format($precio, 2); ?></td>
                            <td>
                                <input type="number"
                                       class="cantidad-input"
                                       value="<?php echo $item['cantidad']; ?>"
                                       min="1"
                                       max="<?php echo $item['stock']; ?>"
                                       onchange="actualizarCantidad(<?php echo $item['id']; ?>, this.value)">
                            </td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <button class="btn btn-danger"
                                        style="padding: 8px 12px; font-size: 12px;"
                                        onclick="eliminarDelCarrito(<?php echo $item['id']; ?>)">
                                    🗑️ Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="carrito-resumen">
            <div class="carrito-total">
                <span class="carrito-total-label">Total a Pagar:</span>
                <span class="carrito-total-valor">$<?php echo number_format($total, 2); ?></span>
            </div>

            <div class="carrito-botones">
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-secondary">
                    ← Continuar Comprando
                </a>
                <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-success">
                    Ir a Checkout →
                </a>
            </div>
        </div>

    <?php else: ?>
        <div class="carrito-vacio">
            <p style="font-size: 48px; margin-bottom: 10px;">🛒</p>
            <p>Tu carrito está vacío</p>
            <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary"
               style="display: inline-block; margin-top: 20px; text-decoration: none;">
                Ir al Catálogo
            </a>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
