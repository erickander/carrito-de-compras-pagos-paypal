<?php
/**
 * Script de Instalación y Verificación
 * Verifica que todo esté correctamente configurado
 */

session_start();

$checks = [
    'php_version' => version_compare(PHP_VERSION, '7.4', '>='),
    'mysqli' => extension_loaded('mysqli'),
    'curl' => extension_loaded('curl'),
    'json' => extension_loaded('json'),
];

$db_check = false;
$db_error = '';

// Intentar conectar a la BD
try {
    $conexion = @new mysqli('localhost', 'root', '', 'tienda_tecnologia');

    if ($conexion->connect_error) {
        $db_error = $conexion->connect_error;
        $db_check = false;
    } else {
        $db_check = true;
        $conexion->close();
    }
} catch (Exception $e) {
    $db_error = $e->getMessage();
    $db_check = false;
}

$checks['database'] = $db_check;

// Verificar archivos críticos
$archivos_criticos = [
    'config.php' => file_exists(__DIR__ . '/config.php'),
    'includes/funciones.php' => file_exists(__DIR__ . '/includes/funciones.php'),
    'includes/header.php' => file_exists(__DIR__ . '/includes/header.php'),
    'includes/footer.php' => file_exists(__DIR__ . '/includes/footer.php'),
    'css/style.css' => file_exists(__DIR__ . '/css/style.css'),
    'js/main.js' => file_exists(__DIR__ . '/js/main.js'),
];

$checks['archivos'] = !in_array(false, $archivos_criticos);

// Verificar credenciales de PayPal
require_once 'config.php';

$paypal_check = (PAYPAL_CLIENT_ID !== 'YOUR_CLIENT_ID' && PAYPAL_SECRET !== 'YOUR_SECRET_KEY');

$all_good = !in_array(false, $checks) && $paypal_check;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Instalación - TechShop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .status-icon {
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }

        .check-list {
            list-style: none;
            margin-top: 30px;
        }

        .check-item {
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 15px;
            background-color: #f5f5f5;
            border-left: 4px solid #ddd;
        }

        .check-item.ok {
            background-color: #e8f5e9;
            border-left-color: #4caf50;
        }

        .check-item.error {
            background-color: #ffebee;
            border-left-color: #f44336;
        }

        .check-icon {
            font-size: 20px;
            min-width: 30px;
        }

        .check-label {
            flex: 1;
        }

        .check-label strong {
            display: block;
            color: #333;
            margin-bottom: 3px;
        }

        .check-label small {
            color: #666;
            font-size: 12px;
        }

        .section-title {
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .action-buttons {
            text-align: center;
            margin-top: 40px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5568d3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #ddd;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #ccc;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }

        code {
            background-color: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-icon">
            <?php echo $all_good ? '✅' : '⚠️'; ?>
        </div>

        <h1>
            <?php echo $all_good
                ? '¡Instalación Completa!'
                : 'Verificación de Instalación'; ?>
        </h1>

        <?php if (!$all_good): ?>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">
                Por favor revisa los puntos que están marcados como error.
            </p>
        <?php else: ?>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">
                Tu tienda está lista para operar. ¡Comienza a vender!
            </p>
        <?php endif; ?>

        <div class="section-title">Requisitos del Servidor</div>
        <ul class="check-list">
            <li class="check-item <?php echo $checks['php_version'] ? 'ok' : 'error'; ?>">
                <span class="check-icon"><?php echo $checks['php_version'] ? '✓' : '✗'; ?></span>
                <span class="check-label">
                    <strong>PHP 7.4+</strong>
                    <small>Versión instalada: <?php echo PHP_VERSION; ?></small>
                </span>
            </li>

            <li class="check-item <?php echo $checks['mysqli'] ? 'ok' : 'error'; ?>">
                <span class="check-icon"><?php echo $checks['mysqli'] ? '✓' : '✗'; ?></span>
                <span class="check-label">
                    <strong>MySQLi Extension</strong>
                    <small>Extensión para conexión a BD MySQL</small>
                </span>
            </li>

            <li class="check-item <?php echo $checks['curl'] ? 'ok' : 'error'; ?>">
                <span class="check-icon"><?php echo $checks['curl'] ? '✓' : '✗'; ?></span>
                <span class="check-label">
                    <strong>cURL Extension</strong>
                    <small>Necesario para API de PayPal</small>
                </span>
            </li>

            <li class="check-item <?php echo $checks['json'] ? 'ok' : 'error'; ?>">
                <span class="check-icon"><?php echo $checks['json'] ? '✓' : '✗'; ?></span>
                <span class="check-label">
                    <strong>JSON Extension</strong>
                    <small>Para procesar datos de PayPal</small>
                </span>
            </li>
        </ul>

        <div class="section-title">Base de Datos</div>
        <ul class="check-list">
            <li class="check-item <?php echo $checks['database'] ? 'ok' : 'error'; ?>">
                <span class="check-icon"><?php echo $checks['database'] ? '✓' : '✗'; ?></span>
                <span class="check-label">
                    <strong>Conexión a BD</strong>
                    <small>
                        <?php
                        if ($checks['database']) {
                            echo 'Conectado a <code>tienda_tecnologia</code>';
                        } else {
                            echo 'Error: ' . htmlspecialchars($db_error);
                        }
                        ?>
                    </small>
                </span>
            </li>
        </ul>

        <div class="section-title">Archivos</div>
        <ul class="check-list">
            <?php foreach ($archivos_criticos as $archivo => $existe): ?>
                <li class="check-item <?php echo $existe ? 'ok' : 'error'; ?>">
                    <span class="check-icon"><?php echo $existe ? '✓' : '✗'; ?></span>
                    <span class="check-label">
                        <strong><?php echo htmlspecialchars($archivo); ?></strong>
                        <small><?php echo $existe ? 'Encontrado' : 'No encontrado'; ?></small>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="section-title">Configuración</div>
        <ul class="check-list">
            <li class="check-item <?php echo $paypal_check ? 'ok' : 'error'; ?>">
                <span class="check-icon"><?php echo $paypal_check ? '✓' : '✗'; ?></span>
                <span class="check-label">
                    <strong>Credenciales de PayPal</strong>
                    <small>
                        <?php
                        if ($paypal_check) {
                            echo 'Configuradas correctamente';
                        } else {
                            echo 'Reemplaza las credenciales en <code>config.php</code>';
                        }
                        ?>
                    </small>
                </span>
            </li>
        </ul>

        <?php if (!$all_good): ?>
            <div class="alert alert-danger" style="margin-top: 30px;">
                <strong>⚠️ Problemas Detectados</strong><br>
                Por favor revisa los puntos marcados con ✗ arriba.
                <br><br>

                <?php if (!$checks['database']): ?>
                    <strong>Para importar la BD:</strong>
                    <ol style="margin-left: 20px; margin-top: 10px;">
                        <li>Abre <code>http://localhost/phpmyadmin</code></li>
                        <li>Crea una BD llamada <code>tienda_tecnologia</code></li>
                        <li>Selecciona "Importar" y carga <code>tienda_tecnologia.sql</code></li>
                    </ol>
                <?php endif; ?>

                <?php if (!$paypal_check): ?>
                    <strong>Para configurar PayPal:</strong>
                    <ol style="margin-left: 20px; margin-top: 10px;">
                        <li>Ve a https://developer.paypal.com</li>
                        <li>Obtén tus credenciales en "Apps & Credentials"</li>
                        <li>Edita <code>config.php</code> y reemplaza <code>PAYPAL_CLIENT_ID</code> y <code>PAYPAL_SECRET</code></li>
                    </ol>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <?php if ($all_good): ?>
                <a href="<?php echo $checks['database'] ? 'index.php' : '#'; ?>"
                   class="btn btn-primary"
                   <?php echo $checks['database'] ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'; ?>>
                    📱 Ir a la Tienda
                </a>
            <?php else: ?>
                <button class="btn btn-secondary" onclick="location.reload();">
                    🔄 Verificar Nuevamente
                </button>
            <?php endif; ?>
        </div>

        <div class="footer-text">
            Para ayuda, consulta el archivo <code>README.md</code>
        </div>
    </div>
</body>
</html>
