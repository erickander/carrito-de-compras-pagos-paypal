SET NAMES utf8mb4;

INSERT INTO categorias (id, nombre, descripcion) VALUES
(1, 'Smartphones', 'Smartphones recientes para uso diario, fotografia y productividad'),
(2, 'Laptops', 'Computadoras portatiles para trabajo, estudio y gaming'),
(3, 'Tablets', 'Tablets para entretenimiento, lectura y productividad'),
(4, 'Accesorios', 'Accesorios para escritorios, carga y dispositivos electronicos'),
(5, 'Audio', 'Auriculares, parlantes y equipos de audio'),
(6, 'Gaming', 'Perifericos y equipos para jugadores'),
(7, 'Smart Home', 'Dispositivos inteligentes para el hogar')
ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    descripcion = VALUES(descripcion);

INSERT INTO productos
(id, nombre, descripcion, precio, precio_oferta, stock, imagen, categoria_id, destacado, especificaciones)
VALUES
(1, 'iPhone 15 Pro 128GB', 'Smartphone Apple con chip A17 Pro, pantalla OLED y camara avanzada.', 999.00, 949.00, 18, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=900&q=80', 1, 1, '128GB, titanio, USB-C'),
(2, 'Samsung Galaxy S24 256GB', 'Smartphone Samsung con pantalla AMOLED, IA integrada y alto rendimiento.', 899.00, 829.00, 22, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=900&q=80', 1, 1, '256GB, AMOLED, Android'),
(3, 'MacBook Pro 14 M3', 'Laptop profesional con chip M3, pantalla Liquid Retina y bateria de larga duracion.', 1999.00, 1849.00, 12, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=900&q=80', 2, 1, '14 pulgadas, 16GB RAM, 512GB SSD'),
(4, 'Lenovo Legion Slim 5', 'Laptop gaming ligera con graficos dedicados y pantalla de alta tasa de refresco.', 1399.00, 1199.00, 10, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=900&q=80', 2, 1, 'Ryzen 7, RTX, 16GB RAM'),
(5, 'iPad Air 11', 'Tablet versatil para estudio, dibujo, productividad y entretenimiento.', 599.00, 549.00, 26, 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=900&q=80', 3, 0, '11 pulgadas, Wi-Fi, 128GB'),
(6, 'Samsung Galaxy Tab S9 FE', 'Tablet Android con lapiz incluido, pantalla amplia y modo productividad.', 499.00, 429.00, 20, 'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&w=900&q=80', 3, 0, '10.9 pulgadas, S Pen, 128GB'),
(7, 'AirPods Pro 2', 'Auriculares inalambricos con cancelacion activa de ruido y audio espacial.', 249.00, 219.00, 34, 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?auto=format&fit=crop&w=900&q=80', 5, 1, 'ANC, USB-C, estuche MagSafe'),
(8, 'Sony WH-1000XM5', 'Audifonos premium con gran cancelacion de ruido y sonido de alta fidelidad.', 399.00, 349.00, 15, 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?auto=format&fit=crop&w=900&q=80', 5, 1, 'Bluetooth, ANC, 30h bateria'),
(9, 'Logitech MX Master 3S', 'Mouse ergonomico de precision para productividad, diseno y programacion.', 99.00, 89.00, 40, 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=900&q=80', 4, 0, '8000 DPI, Bluetooth, USB-C'),
(10, 'Teclado Mecanico Keychron K2', 'Teclado mecanico compacto con iluminacion y conexion inalambrica.', 109.00, 95.00, 28, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=80', 4, 0, 'Hot-swap, Bluetooth, RGB'),
(11, 'Monitor LG UltraGear 27', 'Monitor gaming QHD con alta tasa de refresco para juego competitivo.', 329.00, 299.00, 14, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=900&q=80', 6, 1, '27 pulgadas, QHD, 165Hz'),
(12, 'Consola PlayStation 5 Slim', 'Consola de nueva generacion con SSD veloz y catalogo de juegos actual.', 499.00, 469.00, 9, 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=900&q=80', 6, 1, '1TB, DualSense, 4K'),
(13, 'Echo Show 8', 'Pantalla inteligente para videollamadas, musica, calendario y hogar conectado.', 149.00, 129.00, 21, 'https://images.unsplash.com/photo-1543512214-318c7553f230?auto=format&fit=crop&w=900&q=80', 7, 0, 'Alexa, pantalla 8 pulgadas'),
(14, 'Camara Wi-Fi TP-Link Tapo', 'Camara de seguridad para interior con vision nocturna y deteccion de movimiento.', 49.00, 39.00, 45, 'https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=900&q=80', 7, 0, '1080p, Wi-Fi, app movil')
ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    descripcion = VALUES(descripcion),
    precio = VALUES(precio),
    precio_oferta = VALUES(precio_oferta),
    stock = VALUES(stock),
    imagen = VALUES(imagen),
    categoria_id = VALUES(categoria_id),
    destacado = VALUES(destacado),
    especificaciones = VALUES(especificaciones);
