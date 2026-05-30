<?php
require_once 'config.php';
require_once 'includes/funciones.php';

header('Content-Type: application/json');

$session_id = $_SESSION['session_id'];
$action = $_POST['action'] ?? 'agregar';

try {
    switch ($action) {
        case 'agregar':
            $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
            $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

            if ($producto_id <= 0 || $cantidad <= 0) {
                throw new Exception('Datos inválidos');
            }

            $resultado = agregar_carrito($conexion, $producto_id, $cantidad, $session_id);
            echo json_encode($resultado);
            break;

        case 'actualizar':
            $carrito_id = isset($_POST['carrito_id']) ? (int)$_POST['carrito_id'] : 0;
            $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

            if ($carrito_id <= 0) {
                throw new Exception('ID de carrito inválido');
            }

            $resultado = actualizar_carrito($conexion, $carrito_id, $cantidad);
            echo json_encode([
                'success' => $resultado,
                'total' => calcular_total($conexion, $session_id)
            ]);
            break;

        case 'eliminar':
            $carrito_id = isset($_POST['carrito_id']) ? (int)$_POST['carrito_id'] : 0;

            if ($carrito_id <= 0) {
                throw new Exception('ID de carrito inválido');
            }

            $resultado = eliminar_carrito($conexion, $carrito_id);
            echo json_encode([
                'success' => $resultado,
                'total' => calcular_total($conexion, $session_id)
            ]);
            break;

        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
