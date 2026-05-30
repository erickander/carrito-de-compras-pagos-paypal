<?php
require_once 'config.php';
require_once 'includes/funciones.php';

$titulo = 'Catálogo de Productos - TechShop';
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;

$productos = obtener_productos($conexion, $categoria_id);
$categorias = obtener_categorias($conexion);

require_once 'includes/header.php';
?>

<section class="catalogo">
    <div class="catalogo-header" style="margin-bottom: 40px;">
        <h2 style="font-size: 32px; margin-bottom: 20px;">
            <?php
            if ($categoria_id) {
                $cat = array_filter($categorias, fn($c) => $c['id'] == $categoria_id);
                if ($cat) {
                    echo sanitizar(reset($cat)['nombre']);
                }
            } else {
                echo 'Todos los Productos';
            }
            ?>
        </h2>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="<?php echo BASE_URL; ?>/index.php"
               class="btn <?php echo $categoria_id === null ? 'btn-primary' : 'btn-secondary'; ?>"
               style="text-decoration: none; display: inline-block;">
                Ver Todos
            </a>

            <?php foreach ($categorias as $cat): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?categoria=<?php echo $cat['id']; ?>"
                   class="btn <?php echo $categoria_id === $cat['id'] ? 'btn-primary' : 'btn-secondary'; ?>"
                   style="text-decoration: none; display: inline-block;">
                    <?php echo sanitizar($cat['nombre']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (count($productos) > 0): ?>
        <div class="productos-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
                    <div class="producto-imagen">
                        <?php if ($producto['imagen']): ?>
                            <img src="<?php echo sanitizar($producto['imagen']); ?>"
                                 alt="<?php echo sanitizar($producto['nombre']); ?>">
                        <?php else: ?>
                            📱
                        <?php endif; ?>
                    </div>

                    <div class="producto-info">
                        <div class="producto-categoria">
                            <?php echo sanitizar($producto['categoria'] ?? 'Sin categoría'); ?>
                        </div>

                        <h3 class="producto-nombre">
                            <?php echo sanitizar($producto['nombre']); ?>
                        </h3>

                        <p class="producto-descripcion">
                            <?php echo sanitizar(substr($producto['descripcion'] ?? '', 0, 80)); ?>
                            <?php if (strlen($producto['descripcion'] ?? '') > 80): ?>...<?php endif; ?>
                        </p>

                        <div class="producto-precio">
                            <span class="precio-actual">
                                $<?php echo number_format($producto['precio_oferta'] ?? $producto['precio'], 2); ?>
                            </span>

                            <?php if ($producto['precio_oferta']): ?>
                                <span class="precio-original">
                                    $<?php echo number_format($producto['precio'], 2); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="stock-info <?php echo $producto['stock'] < 10 ? 'bajo' : ''; ?>">
                            Stock: <?php echo $producto['stock']; ?> unidad<?php echo $producto['stock'] !== 1 ? 'es' : ''; ?>
                        </div>

                        <?php if ($producto['stock'] > 0): ?>
                            <form class="producto-acciones" onsubmit="agregarAlCarrito(<?php echo $producto['id']; ?>, event)">
                                <input type="number" class="cantidad-input" value="1" min="1"
                                       max="<?php echo $producto['stock']; ?>" required>
                                <button type="submit" class="btn btn-primary">
                                    🛒 Agregar
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled style="cursor: not-allowed; opacity: 0.6;">
                                Sin Stock
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No hay productos en esta categoría.
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
