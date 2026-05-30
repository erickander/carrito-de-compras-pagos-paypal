<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? sanitizar($titulo) : 'Tienda de Tecnología'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    <a href="<?php echo BASE_URL; ?>/index.php">
                        <h1>🛍️ TechShop</h1>
                    </a>
                </div>

                <ul class="nav-menu">
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Inicio</a></li>
                    <li>
                        <a href="#">Categorías ▼</a>
                        <ul class="submenu">
                            <?php
                            require_once 'funciones.php';
                            global $conexion;
                            $categorias = obtener_categorias($conexion);
                            foreach ($categorias as $cat): ?>
                                <li><a href="<?php echo BASE_URL; ?>/index.php?categoria=<?php echo $cat['id']; ?>">
                                    <?php echo sanitizar($cat['nombre']); ?>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>

                <div class="navbar-right">
                    <a href="<?php echo BASE_URL; ?>/carrito.php" class="carrito-btn">
                        🛒 Carrito
                        <span id="carrito-count" class="carrito-count">0</span>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
