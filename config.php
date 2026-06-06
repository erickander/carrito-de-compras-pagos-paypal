<?php
session_start();

// =========================
// BASE DE DATOS
// =========================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tienda_tecnologia');

$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

// =========================
// SESIÓN CARRITO
// =========================

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid('sess_', true);
}

// =========================
// PAYPAL
// =========================

define('PAYPAL_CLIENT_ID', 'AS4orua2tvrA9Cx9K8i7ypUqeZJizmsn89ZxJTonGGxWMGZ-bs_IGKpHHMQDHrS--mfW6Hsv-XbEAlTt');
define('PAYPAL_SECRET', 'ECwJQsG8W_kzR6zy73k9ehyVcDlLLvVMlrYbzrDcG1Ga55jO8-AD_iPs5rHVkp9MFgH7r-A_RIG22-KH');

define('PAYPAL_MODE', 'sandbox');

define(
    'PAYPAL_API_BASE',
    PAYPAL_MODE === 'sandbox'
        ? 'https://api-m.sandbox.paypal.com'
        : 'https://api-m.paypal.com'
);

// =========================
// URL BASE
// =========================

define('BASE_URL', 'http://localhost/tienda');

// =========================
// ZONA HORARIA
// =========================

date_default_timezone_set('America/Guayaquil');
?>
