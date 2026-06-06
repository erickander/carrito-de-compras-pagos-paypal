<?php
require_once 'config.php';
require_once 'includes/funciones.php';

header('Content-Type: application/xml; charset=UTF-8');

function responder_xml($success, $order_id = null, $total = null, $error = null, $status = 200) {
    http_response_code($status);

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;

    $root = $dom->createElement('paypalResponse');
    $dom->appendChild($root);

    $root->appendChild($dom->createElement('success', $success ? 'true' : 'false'));

    if ($order_id !== null) {
        $root->appendChild($dom->createElement('orderId', $order_id));
    }

    if ($total !== null) {
        $root->appendChild($dom->createElement('total', number_format((float)$total, 2, '.', '')));
    }

    if ($error !== null) {
        $root->appendChild($dom->createElement('error', $error));
    }

    $schema = __DIR__ . '/xml/paypal_response.xsd';
    if (!$dom->schemaValidate($schema)) {
        http_response_code(500);

        $fallback = new DOMDocument('1.0', 'UTF-8');
        $fallback_root = $fallback->createElement('paypalResponse');
        $fallback->appendChild($fallback_root);
        $fallback_root->appendChild($fallback->createElement('success', 'false'));
        $fallback_root->appendChild($fallback->createElement('error', 'La respuesta XML no cumple el esquema.'));
        echo $fallback->saveXML();
        exit;
    }

    echo $dom->saveXML();
    exit;
}

function texto_xml(DOMDocument $dom, $tag) {
    $nodes = $dom->getElementsByTagName($tag);
    return $nodes->length ? trim($nodes->item(0)->textContent) : '';
}

$raw_input = file_get_contents('php://input');

if (trim($raw_input) === '') {
    responder_xml(false, null, null, 'Solicitud XML vacia.', 400);
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);

if (!$dom->loadXML($raw_input, LIBXML_NONET)) {
    responder_xml(false, null, null, 'El XML enviado no es valido.', 400);
}

if (!$dom->schemaValidate(__DIR__ . '/xml/checkout_request.xsd')) {
    responder_xml(false, null, null, 'Los datos no cumplen la validacion XML.', 422);
}

$nombre = texto_xml($dom, 'nombre');
$email = texto_xml($dom, 'email');
$direccion = texto_xml($dom, 'direccion');
$telefono = texto_xml($dom, 'telefono');

if (!validar_email($email)) {
    responder_xml(false, null, null, 'Correo electronico invalido.', 422);
}

$session_id = $_SESSION['session_id'];
$total = calcular_total($conexion, $session_id);

if ($total <= 0) {
    responder_xml(false, null, null, 'El carrito esta vacio.', 422);
}

$token_ch = curl_init();
curl_setopt_array($token_ch, [
    CURLOPT_URL => PAYPAL_API_BASE . '/v1/oauth2/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
    CURLOPT_USERPWD => PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Accept-Language: en_US'
    ]
]);

$token_response = curl_exec($token_ch);
$token_error = curl_error($token_ch);
$token_status = curl_getinfo($token_ch, CURLINFO_HTTP_CODE);
curl_close($token_ch);

if ($token_response === false || $token_status >= 400) {
    responder_xml(false, null, null, 'No se pudo autenticar con PayPal.', 502);
}

$token_data = json_decode($token_response, true);
$access_token = $token_data['access_token'] ?? null;

if (!$access_token) {
    responder_xml(false, null, null, $token_error ?: 'PayPal no devolvio token de acceso.', 502);
}

$payload = [
    'intent' => 'CAPTURE',
    'purchase_units' => [[
        'amount' => [
            'currency_code' => 'USD',
            'value' => number_format($total, 2, '.', '')
        ],
        'description' => 'Compra TechShop'
    ]],
    'payer' => [
        'name' => [
            'given_name' => $nombre
        ],
        'email_address' => $email
    ],
    'application_context' => [
        'brand_name' => 'TechShop',
        'user_action' => 'PAY_NOW',
        'return_url' => BASE_URL . '/confirmacion.php',
        'cancel_url' => BASE_URL . '/checkout.php'
    ]
];

$order_ch = curl_init();
curl_setopt_array($order_ch, [
    CURLOPT_URL => PAYPAL_API_BASE . '/v2/checkout/orders',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]
]);

$order_response = curl_exec($order_ch);
$order_status = curl_getinfo($order_ch, CURLINFO_HTTP_CODE);
curl_close($order_ch);

if ($order_response === false || $order_status >= 400) {
    responder_xml(false, null, null, 'PayPal no pudo crear la orden.', 502);
}

$order_data = json_decode($order_response, true);
$order_id = $order_data['id'] ?? null;

if (!$order_id) {
    responder_xml(false, null, null, 'Respuesta inesperada de PayPal.', 502);
}

$_SESSION['checkout_cliente'] = [
    'nombre' => $nombre,
    'email' => $email,
    'direccion' => $direccion,
    'telefono' => $telefono
];

responder_xml(true, $order_id, $total);
