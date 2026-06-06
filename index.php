<?php
require_once 'config.php';
require_once 'includes/funciones.php';

$titulo = 'Catalogo de Productos - TechShop';
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;

$productos = obtener_productos($conexion, $categoria_id);
$categorias = obtener_categorias($conexion);

require_once 'includes/header.php';
?>

<section class="catalogo">
    <div class="tienda-hero">
        <div class="hero-copy">
            <p class="section-kicker">Setup, gaming y productividad</p>
            <h2>Equipos tecnologicos listos para renovar tu escritorio.</h2>
            <p>Encuentra laptops, smartphones, audio y accesorios con precios claros, stock visible y pago seguro con PayPal.</p>

            <div class="hero-stats">
                <div class="hero-stat">
                    <strong><?php echo count($productos); ?>+</strong>
                    <span>Productos</span>
                </div>
                <div class="hero-stat">
                    <strong>24h</strong>
                    <span>Despacho</span>
                </div>
                <div class="hero-stat">
                    <strong>PayPal</strong>
                    <span>Pago seguro</span>
                </div>
            </div>
        </div>

        <div class="hero-panel">
            <div>
                <span class="hero-chip">Oferta destacada</span>
                <strong>Combina laptop, audio y accesorios en una sola compra.</strong>
            </div>
            <span>Catalogo actualizado con referencias reales para una tienda de tecnologia moderna.</span>
        </div>
    </div>

    <div class="catalogo-header">
        <div>
            <p class="section-kicker">Tecnologia seleccionada</p>
            <h2>
                <?php
                if ($categoria_id) {
                    $cat = array_filter($categorias, fn($c) => $c['id'] == $categoria_id);
                    echo $cat ? sanitizar(reset($cat)['nombre']) : 'Productos';
                } else {
                    echo 'Todos los Productos';
                }
                ?>
            </h2>
        </div>

        <div class="categoria-filtros">
            <a href="<?php echo BASE_URL; ?>/index.php"
               class="btn <?php echo $categoria_id === null ? 'btn-primary' : 'btn-secondary'; ?>">
                Ver todos
            </a>

            <?php foreach ($categorias as $cat): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?categoria=<?php echo $cat['id']; ?>"
                   class="btn <?php echo $categoria_id === (int)$cat['id'] ? 'btn-primary' : 'btn-secondary'; ?>">
                    <?php echo sanitizar($cat['nombre']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (count($productos) > 0): ?>
        <div class="productos-grid">
            <?php foreach ($productos as $producto): ?>
                <article class="producto-card">
                    <?php if ((int)$producto['destacado'] === 1): ?>
                        <span class="producto-badge">Destacado</span>
                    <?php endif; ?>

                    <div class="producto-imagen">
                        <?php if ($producto['imagen']): ?>
                            <img src="<?php echo sanitizar($producto['imagen']); ?>"
                                 alt="<?php echo sanitizar($producto['nombre']); ?>">
                        <?php else: ?>
                            <span>Sin imagen</span>
                        <?php endif; ?>
                    </div>

                    <div class="producto-info">
                        <div class="producto-categoria">
                            <?php echo sanitizar($producto['categoria'] ?? 'Sin categoria'); ?>
                        </div>

                        <h3 class="producto-nombre">
                            <?php echo sanitizar($producto['nombre']); ?>
                        </h3>

                        <p class="producto-descripcion">
                            <?php echo sanitizar(substr($producto['descripcion'] ?? '', 0, 90)); ?>
                            <?php if (strlen($producto['descripcion'] ?? '') > 90): ?>...<?php endif; ?>
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
                            Stock: <?php echo (int)$producto['stock']; ?> unidad<?php echo (int)$producto['stock'] !== 1 ? 'es' : ''; ?>
                        </div>

                        <?php if ($producto['stock'] > 0): ?>
                            <form class="producto-acciones" onsubmit="agregarAlCarrito(<?php echo $producto['id']; ?>, event)">
                                <input type="number" class="cantidad-input" value="1" min="1"
                                       max="<?php echo (int)$producto['stock']; ?>" required>
                                <button type="submit" class="btn btn-primary">Agregar</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Sin Stock</button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No hay productos en esta categoria.
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
