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
    <div class="catalogo-header">
        <div>
            <p class="section-kicker">Resumen de compra</p>
            <h2>Carrito de Compras</h2>
        </div>
    </div>

    <?php if (count($carrito) > 0): ?>
        <div class="tabla-scroll">
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
                                <div class="carrito-producto">
                                    <div class="producto-imagen carrito-miniatura">
                                        <?php if ($item['imagen']): ?>
                                            <img src="<?php echo sanitizar($item['imagen']); ?>"
                                                 alt="<?php echo sanitizar($item['nombre']); ?>">
                                        <?php else: ?>
                                            <span>Sin imagen</span>
                                        <?php endif; ?>
                                    </div>
                                    <span><?php echo sanitizar($item['nombre']); ?></span>
                                </div>
                            </td>
                            <td>$<?php echo number_format($precio, 2); ?></td>
                            <td>
                                <input type="number"
                                       class="cantidad-input"
                                       value="<?php echo (int)$item['cantidad']; ?>"
                                       min="1"
                                       max="<?php echo (int)$item['stock']; ?>"
                                       onchange="actualizarCantidad(<?php echo $item['id']; ?>, this.value)">
                            </td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <button class="btn btn-danger btn-small"
                                        onclick="eliminarDelCarrito(<?php echo $item['id']; ?>)">
                                    Eliminar
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
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-secondary">Continuar Comprando</a>
                <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-success">Ir a Checkout</a>
            </div>
        </div>

    <?php else: ?>
        <div class="carrito-vacio">
            <p class="empty-icon">Carrito vacio</p>
            <p>Tu carrito esta vacio</p>
            <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary">Ir al Catalogo</a>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
