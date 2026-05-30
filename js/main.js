// Actualizar el contador del carrito en la página
function actualizarContadorCarrito() {
    const carritoItems = JSON.parse(localStorage.getItem('carrito_items') || '[]');
    const contador = carritoItems.reduce((total, item) => total + item.cantidad, 0);
    const carritoCount = document.getElementById('carrito-count');
    if (carritoCount) {
        carritoCount.textContent = contador;
    }
}

// Agregar producto al carrito mediante AJAX
function agregarAlCarrito(productoId, event) {
    event.preventDefault();

    const cantidadInput = event.target.closest('.producto-acciones')?.querySelector('.cantidad-input');
    const cantidad = cantidadInput ? parseInt(cantidadInput.value) : 1;

    if (cantidad < 1) {
        mostrarToast('Cantidad inválida', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('producto_id', productoId);
    formData.append('cantidad', cantidad);
    formData.append('action', 'agregar');

    fetch('/tienda/agregar_carrito.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarToast('✓ Producto agregado al carrito', 'success');
            actualizarContadorCarrito();

            // Actualizar almacenamiento local
            const carritoItems = JSON.parse(localStorage.getItem('carrito_items') || '[]');
            const itemExistente = carritoItems.find(item => item.id === productoId);
            if (itemExistente) {
                itemExistente.cantidad += cantidad;
            } else {
                carritoItems.push({
                    id: productoId,
                    cantidad: cantidad
                });
            }
            localStorage.setItem('carrito_items', JSON.stringify(carritoItems));
        } else {
            mostrarToast('Error: ' + data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error al agregar al carrito', 'error');
    });
}

// Actualizar cantidad en el carrito
function actualizarCantidad(carritoId, nueva_cantidad) {
    if (nueva_cantidad < 1) {
        if (confirm('¿Deseas eliminar este artículo?')) {
            eliminarDelCarrito(carritoId);
        }
        return;
    }

    const formData = new FormData();
    formData.append('carrito_id', carritoId);
    formData.append('cantidad', nueva_cantidad);
    formData.append('action', 'actualizar');

    fetch('/tienda/agregar_carrito.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            mostrarToast('Error al actualizar cantidad', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error al actualizar cantidad', 'error');
    });
}

// Eliminar producto del carrito
function eliminarDelCarrito(carritoId) {
    const formData = new FormData();
    formData.append('carrito_id', carritoId);
    formData.append('action', 'eliminar');

    fetch('/tienda/agregar_carrito.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarToast('Artículo eliminado del carrito', 'success');
            location.reload();
        } else {
            mostrarToast('Error al eliminar del carrito', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error al eliminar del carrito', 'error');
    });
}

// Mostrar notificación (toast)
function mostrarToast(mensaje, tipo = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.textContent = mensaje;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Validar formulario de checkout
function validarCheckout(event) {
    event.preventDefault();

    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const direccion = document.getElementById('direccion').value.trim();
    const telefono = document.getElementById('telefono').value.trim();

    if (!nombre || !email || !direccion || !telefono) {
        mostrarToast('Todos los campos son obligatorios', 'error');
        return false;
    }

    if (!validarEmail(email)) {
        mostrarToast('Email inválido', 'error');
        return false;
    }

    if (!validarTelefono(telefono)) {
        mostrarToast('Teléfono inválido', 'error');
        return false;
    }

    return true;
}

// Validar email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Validar teléfono (10-20 dígitos)
function validarTelefono(telefono) {
    const regex = /^\d{10,20}$/;
    return regex.test(telefono.replace(/[\s\-\+\(\)]/g, ''));
}

// Inicializar cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    actualizarContadorCarrito();

    // Validar cantidad mínima de entrada
    const cantidadInputs = document.querySelectorAll('.cantidad-input');
    cantidadInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > 999) this.value = 999;
        });
    });

    // Validar formulario de checkout
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', validarCheckout);
    }
});

// Animación de salida para toasts
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
