# 🛍️ TechShop - Tienda de Tecnología

## Configuración e Instalación

### 1. Importar Base de Datos

Primero, necesitas importar la base de datos en MySQL:

**Opción A: Usando phpMyAdmin**
1. Abre `http://localhost/phpmyadmin`
2. Crea una nueva base de datos llamada `tienda_tecnologia`
3. Selecciona la BD y ve a "Importar"
4. Selecciona el archivo `tienda_tecnologia.sql` desde `/c/xampp/htdocs/tienda/`
5. Haz clic en "Importar"

**Opción B: Línea de comandos (en cmd o PowerShell)**
```bash
cd "C:\xampp\mysql\bin"
mysql -u root -p tienda_tecnologia < "C:\xampp\htdocs\tienda\tienda_tecnologia.sql"
```
(Presiona Enter cuando te pida contraseña si no tienes una configurada)

### 2. Configurar Credenciales de PayPal

Edita el archivo `config.php` y reemplaza:

```php
define('PAYPAL_CLIENT_ID', 'YOUR_CLIENT_ID');
define('PAYPAL_SECRET', 'YOUR_SECRET_KEY');
```

Con tus credenciales reales de PayPal.

**Para obtener credenciales:**
1. Ve a https://developer.paypal.com
2. Inicia sesión o crea una cuenta
3. Ve a "Apps & Credentials"
4. En "Sandbox", copia Client ID y Secret
5. Asegúrate de usar "Production" o "Sandbox" según sea necesario en `PAYPAL_MODE`

### 3. Acceder a la Tienda

Una vez que XAMPP esté ejecutándose:

```
http://localhost/tienda/index.php
```

## Estructura de la Aplicación

### Archivos Principales

- **index.php** - Catálogo de productos
- **carrito.php** - Carrito de compras
- **checkout.php** - Formulario de compra
- **procesar_paypal.php** - Procesamiento de pago con PayPal
- **confirmacion.php** - Confirmación de pedido
- **agregar_carrito.php** - Endpoint AJAX para gestionar carrito

### Carpetas

- **includes/** - Archivos compartidos (header, footer, funciones)
- **css/** - Hojas de estilo
- **js/** - JavaScript del cliente

### Base de Datos

Tablas incluidas:
- `usuarios` - Clientes (opcional, para futuras funcionalidades)
- `categorias` - Categorías de productos
- `productos` - Catálogo de productos
- `carrito` - Carrito de compras temporal
- `pedidos` - Órdenes completadas
- `detalles_pedido` - Items de cada pedido

## Flujo de Compra

1. **Catálogo** (index.php) - Usuario ve productos y agrega al carrito
2. **Carrito** (carrito.php) - Revisa los items, ajusta cantidades
3. **Checkout** (checkout.php) - Ingresa datos de envío
4. **PayPal** - Realiza el pago en el sitio de PayPal
5. **Confirmación** (confirmacion.php) - Recibe confirmación de pago

## Seguridad

✅ Validación de datos en servidor  
✅ Prepared Statements para prevenir SQL injection  
✅ Sanitización de entrada HTML  
✅ Tokens de sesión para carrito anónimo  
✅ Verificación de stock antes de pago  
✅ Encriptación SSL en PayPal  

## Características

- ✅ Catálogo con filtro por categorías
- ✅ Carrito de compras anónimo (basado en sesión)
- ✅ Gestión de cantidades
- ✅ Validación de stock
- ✅ Integración con API de PayPal v2
- ✅ Historial de pedidos con detalles
- ✅ Respuestas en JSON para AJAX
- ✅ Diseño responsivo (mobile-friendly)

## Soporte de Imágenes

Las imágenes de productos se configuran como URLs externas. Para agregar imágenes:

1. En phpMyAdmin, edita la tabla `productos`
2. En el campo `imagen`, agrega una URL externa:
   ```
   https://via.placeholder.com/300x300?text=iPhone+15
   ```

O usa URLs reales de CDN de imágenes.

## Troubleshooting

**Error: "Error de conexión a BD"**
- Verifica que XAMPP esté ejecutándose (Apache + MySQL)
- Asegúrate de que la BD `tienda_tecnologia` fue importada
- Comprueba credenciales en `config.php`

**Error: "Error al crear orden en PayPal"**
- Verifica que tus credenciales de PayPal sean correctas
- Comprueba que estés usando el modo correcto (Sandbox o Production)
- Revisa los logs de error en el servidor

**El carrito no se actualiza**
- Revisa que JavaScript esté habilitado
- Comprueba la consola del navegador para errores
- Verifica que `agregar_carrito.php` esté accesible

## Próximas Mejoras (Opcionales)

- [ ] Registro de usuarios y login
- [ ] Historial de órdenes por usuario
- [ ] Códigos de descuento
- [ ] Email de confirmación automático
- [ ] Sistema de reseñas
- [ ] Búsqueda de productos
- [ ] Admin panel para gestionar productos
- [ ] Múltiples métodos de pago

---

**Nota:** Esta aplicación usa PHP 8.0+ con MySQLi. Asegúrate de tener XAMPP actualizado.
