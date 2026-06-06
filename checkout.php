<?php
require_once 'config.php';
require_once 'includes/funciones.php';

$session_id = $_SESSION['session_id'];
$carrito = obtener_carrito($conexion, $session_id);
$total = calcular_total($conexion, $session_id);

$titulo = 'Checkout - TechShop';
require_once 'includes/header.php';
?>

<section class="checkout-page">
    <div class="checkout-heading">
        <p class="section-kicker">Pago seguro</p>
        <h2>Finaliza tu compra</h2>
        <p>Completa tus datos, revisa el resumen y paga con PayPal.</p>
    </div>

    <?php if (count($carrito) === 0): ?>
        <div class="carrito-vacio checkout-empty">
            <p class="empty-icon">Carrito vacio</p>
            <p>No tienes productos agregados.</p>
            <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary">Ir al catalogo</a>
        </div>
    <?php else: ?>
        <div class="checkout-contenedor">
            <form id="checkoutForm" class="checkout-form" novalidate>
                <div class="form-header">
                    <span class="step-badge">1</span>
                    <div>
                        <h3>Datos de entrega</h3>
                        <p>La informacion se envia como XML y se valida antes de crear la orden.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" minlength="3" maxlength="120" autocomplete="name" required>
                    <small class="field-error" id="nombre-error"></small>
                </div>

                <div class="form-group">
                    <label for="email">Correo electronico</label>
                    <input type="email" id="email" name="email" maxlength="160" autocomplete="email" required>
                    <small class="field-error" id="email-error"></small>
                </div>

                <div class="form-group">
                    <label for="direccion">Direccion</label>
                    <textarea id="direccion" name="direccion" minlength="6" maxlength="220" autocomplete="street-address" required></textarea>
                    <small class="field-error" id="direccion-error"></small>
                </div>

                <div class="form-group">
                    <label for="telefono">Telefono</label>
                    <input type="tel" id="telefono" name="telefono" minlength="7" maxlength="25" autocomplete="tel" required>
                    <small class="field-error" id="telefono-error"></small>
                </div>
            </form>

            <aside class="checkout-resumen">
                <div class="form-header">
                    <span class="step-badge">2</span>
                    <div>
                        <h3>Resumen</h3>
                        <p>Pago conservado con PayPal.</p>
                    </div>
                </div>

                <div class="resumen-lista">
                    <?php foreach ($carrito as $item): ?>
                        <?php
                        $precio = $item['precio_oferta'] ?? $item['precio'];
                        $subtotal = $precio * $item['cantidad'];
                        ?>
                        <div class="resumen-item">
                            <span><?php echo sanitizar($item['nombre']); ?> x <?php echo (int)$item['cantidad']; ?></span>
                            <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="resumen-total">
                    <span>Total</span>
                    <strong>$<?php echo number_format($total, 2); ?></strong>
                </div>

                <div class="paypal-card">
                    <p>Metodo de pago</p>
                    <div id="paypal-button-container"></div>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</section>

<?php if (count($carrito) > 0): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=USD"></script>
    <script>
        const checkoutForm = document.getElementById('checkoutForm');
        const xmlEscape = (value) => String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&apos;');

        function setFieldError(field, message) {
            const error = document.getElementById(`${field}-error`);
            const input = document.getElementById(field);
            if (error) error.textContent = message || '';
            if (input) input.classList.toggle('input-error', Boolean(message));
        }

        function validateCheckoutFields() {
            const fields = {
                nombre: document.getElementById('nombre').value.trim(),
                email: document.getElementById('email').value.trim(),
                direccion: document.getElementById('direccion').value.trim(),
                telefono: document.getElementById('telefono').value.trim()
            };

            Object.keys(fields).forEach((field) => setFieldError(field, ''));

            let valid = true;
            if (fields.nombre.length < 3) {
                setFieldError('nombre', 'Ingresa al menos 3 caracteres.');
                valid = false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(fields.email)) {
                setFieldError('email', 'Ingresa un correo valido.');
                valid = false;
            }
            if (fields.direccion.length < 6) {
                setFieldError('direccion', 'Ingresa una direccion mas completa.');
                valid = false;
            }
            if (!/^[0-9+\-\s()]{7,25}$/.test(fields.telefono)) {
                setFieldError('telefono', 'Usa solo numeros, espacios, +, - o parentesis.');
                valid = false;
            }

            return valid ? fields : null;
        }

        function buildCheckoutXml(fields) {
            return `<?xml version="1.0" encoding="UTF-8"?>
<checkout>
    <cliente>
        <nombre>${xmlEscape(fields.nombre)}</nombre>
        <email>${xmlEscape(fields.email)}</email>
        <direccion>${xmlEscape(fields.direccion)}</direccion>
        <telefono>${xmlEscape(fields.telefono)}</telefono>
    </cliente>
</checkout>`;
        }

        paypal.Buttons({
            createOrder: async function() {
                const fields = validateCheckoutFields();
                if (!fields) {
                    mostrarToast('Revisa los datos del formulario', 'error');
                    return;
                }

                const response = await fetch('procesar_paypal.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/xml',
                        'Accept': 'application/xml'
                    },
                    body: buildCheckoutXml(fields)
                });

                const xmlText = await response.text();
                const xml = new DOMParser().parseFromString(xmlText, 'application/xml');
                const parserError = xml.querySelector('parsererror');
                const success = xml.querySelector('success')?.textContent === 'true';
                const orderId = xml.querySelector('orderId')?.textContent;
                const error = xml.querySelector('error')?.textContent;

                if (parserError || !response.ok || !success || !orderId) {
                    mostrarToast(error || 'No se pudo crear la orden PayPal', 'error');
                    return;
                }

                return orderId;
            },

            onApprove: async function(data, actions) {
                await actions.order.capture();
                window.location.href = 'confirmacion.php?order_id=' + encodeURIComponent(data.orderID);
            },

            onError: function(err) {
                console.error(err);
                mostrarToast('Error con PayPal', 'error');
            }
        }).render('#paypal-button-container');
    </script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
