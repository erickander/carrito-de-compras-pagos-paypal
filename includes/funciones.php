<?php
// Obtener productos con filtro opcional de categoría
function obtener_productos($conexion, $categoria_id = null) {
    $sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.precio_oferta,
            p.stock, p.imagen, c.nombre AS categoria, p.destacado
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id";

    if ($categoria_id) {
        $sql .= " WHERE p.categoria_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $categoria_id);
    } else {
        $stmt = $conexion->prepare($sql);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Obtener todas las categorías
function obtener_categorias($conexion) {
    $sql = "SELECT id, nombre FROM categorias ORDER BY nombre";
    $result = $conexion->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener un producto específico
function obtener_producto($conexion, $producto_id) {
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obtener items del carrito
function obtener_carrito($conexion, $session_id) {
    $sql = "SELECT c.id, c.producto_id, c.cantidad, p.nombre, p.precio,
            p.precio_oferta, p.stock, p.imagen
            FROM carrito c
            JOIN productos p ON c.producto_id = p.id
            WHERE c.session_id = ?
            ORDER BY c.fecha_agregado DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Agregar producto al carrito (o actualizar si ya existe)
function agregar_carrito($conexion, $producto_id, $cantidad, $session_id) {
    $cantidad = (int)$cantidad;
    if ($cantidad < 1) $cantidad = 1;

    // Verificar si el producto existe y tiene stock
    $producto = obtener_producto($conexion, $producto_id);
    if (!$producto) {
        return ['success' => false, 'error' => 'Producto no existe'];
    }

    if ($cantidad > $producto['stock']) {
        return ['success' => false, 'error' => 'Stock insuficiente'];
    }

    // Verificar si ya está en el carrito
    $sql = "SELECT id FROM carrito WHERE producto_id = ? AND session_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("is", $producto_id, $session_id);
    $stmt->execute();
    $existe = $stmt->get_result()->fetch_assoc();

    if ($existe) {
        $sql = "UPDATE carrito SET cantidad = cantidad + ?
                WHERE producto_id = ? AND session_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iis", $cantidad, $producto_id, $session_id);
    } else {
        $sql = "INSERT INTO carrito (producto_id, cantidad, session_id) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iis", $producto_id, $cantidad, $session_id);
    }

    if ($stmt->execute()) {
        return ['success' => true, 'total' => calcular_total($conexion, $session_id)];
    }

    return ['success' => false, 'error' => 'Error al agregar al carrito'];
}

// Actualizar cantidad en carrito
function actualizar_carrito($conexion, $id, $cantidad) {
    $cantidad = (int)$cantidad;

    if ($cantidad <= 0) {
        return eliminar_carrito($conexion, $id);
    }

    $sql = "UPDATE carrito SET cantidad = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $cantidad, $id);

    return $stmt->execute();
}

// Eliminar item del carrito
function eliminar_carrito($conexion, $id) {
    $sql = "DELETE FROM carrito WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Calcular total del carrito
function calcular_total($conexion, $session_id) {
    $sql = "SELECT SUM((IFNULL(p.precio_oferta, p.precio)) * c.cantidad) AS total
            FROM carrito c
            JOIN productos p ON c.producto_id = p.id
            WHERE c.session_id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    return $resultado['total'] ?? 0;
}

// Crear pedido a partir del carrito
function crear_pedido($conexion, $session_id, $datos_pago, $id_transaccion = null) {
    $total = calcular_total($conexion, $session_id);
    $metodo_pago = 'paypal';
    $estado = 'pendiente';

    // Insertar pedido
    $sql = "INSERT INTO pedidos (total, metodo_pago, id_transaccion, estado)
            VALUES (?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("dsss", $total, $metodo_pago, $id_transaccion, $estado);

    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Error al crear pedido'];
    }

    $pedido_id = $conexion->insert_id;

    // Insertar detalles del pedido
    $carrito = obtener_carrito($conexion, $session_id);

    foreach ($carrito as $item) {
        $precio_actual = $item['precio_oferta'] ?? $item['precio'];
        $subtotal = $precio_actual * $item['cantidad'];

        $sql = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iiidd", $pedido_id, $item['producto_id'],
                         $item['cantidad'], $precio_actual, $subtotal);

        if (!$stmt->execute()) {
            return ['success' => false, 'error' => 'Error al crear detalles de pedido'];
        }
    }

    // Limpiar carrito
    $sql = "DELETE FROM carrito WHERE session_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    return ['success' => true, 'pedido_id' => $pedido_id];
}

// Obtener detalles de un pedido
function obtener_pedido($conexion, $pedido_id) {
    $sql = "SELECT * FROM pedidos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obtener detalles de un pedido (items)
function obtener_detalles_pedido($conexion, $pedido_id) {
    $sql = "SELECT dp.*, p.nombre, p.imagen FROM detalles_pedido dp
            JOIN productos p ON dp.producto_id = p.id
            WHERE dp.pedido_id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Actualizar estado del pedido
function actualizar_estado_pedido($conexion, $pedido_id, $estado, $id_transaccion = null) {
    $sql = "UPDATE pedidos SET estado = ?";
    $params = [$estado];
    $tipos = "s";

    if ($id_transaccion) {
        $sql .= ", id_transaccion = ?";
        $params[] = $id_transaccion;
        $tipos .= "s";
    }

    $sql .= " WHERE id = ?";
    $params[] = $pedido_id;
    $tipos .= "i";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($tipos, ...$params);

    return $stmt->execute();
}

// Validar email
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Sanitizar entrada
function sanitizar($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
?>
