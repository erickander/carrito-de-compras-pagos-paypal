<?php

require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'];
$email = $data['email'];
$direccion = $data['direccion'];
$telefono = $data['telefono'];

$total = 50.00;

// =======================
// OBTENER TOKEN
// =======================

$ch = curl_init();

curl_setopt_array($ch, [

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

$response = curl_exec($ch);

curl_close($ch);

$resultado = json_decode($response, true);

$access_token = $resultado['access_token'];

// =======================
// CREAR ORDEN
// =======================

$payload = [

    "intent" => "CAPTURE",

    "purchase_units" => [[

        "amount" => [

            "currency_code" => "USD",

            "value" => number_format($total, 2, '.', '')
        ]

    ]],

    "application_context" => [

        "return_url" => BASE_URL . "/confirmacion.php",

        "cancel_url" => BASE_URL . "/checkout.php"
    ]
];

$ch = curl_init();

curl_setopt_array($ch, [

    CURLOPT_URL => PAYPAL_API_BASE . '/v2/checkout/orders',

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_POST => true,

    CURLOPT_POSTFIELDS => json_encode($payload),

    CURLOPT_HTTPHEADER => [

        'Content-Type: application/json',

        'Authorization: Bearer ' . $access_token
    ]
]);

$response = curl_exec($ch);

curl_close($ch);

echo $response;