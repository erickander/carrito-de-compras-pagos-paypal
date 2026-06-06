function actualizarContadorCarrito() {
    const carritoItems = JSON.parse(localStorage.getItem('carrito_items') || '[]');
    const contador = carritoItems.reduce((total, item) => total + item.cantidad, 0);
    const carritoCount = document.getElementById('carrito-count');

    if (carritoCount) {
        carritoCount.textContent = contador;
    }
}

function agregarAlCarrito(productoId, event) {
    event.preventDefault();

    const cantidadInput = event.target.closest('.producto-acciones')?.querySelector('.cantidad-input');
    const cantidad = cantidadInput ? parseInt(cantidadInput.value, 10) : 1;

    if (cantidad < 1) {
        mostrarToast('Cantidad invalida', 'error');
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
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                mostrarToast('Producto agregado al carrito', 'success');

                const carritoItems = JSON.parse(localStorage.getItem('carrito_items') || '[]');
                const itemExistente = carritoItems.find((item) => item.id === productoId);

                if (itemExistente) {
                    itemExistente.cantidad += cantidad;
                } else {
                    carritoItems.push({ id: productoId, cantidad });
                }

                localStorage.setItem('carrito_items', JSON.stringify(carritoItems));
                actualizarContadorCarrito();
            } else {
                mostrarToast('Error: ' + data.error, 'error');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            mostrarToast('Error al agregar al carrito', 'error');
        });
}

function actualizarCantidad(carritoId, nuevaCantidad) {
    if (nuevaCantidad < 1) {
        if (confirm('Deseas eliminar este articulo?')) {
            eliminarDelCarrito(carritoId);
        }
        return;
    }

    const formData = new FormData();
    formData.append('carrito_id', carritoId);
    formData.append('cantidad', nuevaCantidad);
    formData.append('action', 'actualizar');

    fetch('/tienda/agregar_carrito.php', {
        method: 'POST',
        body: formData
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                location.reload();
            } else {
                mostrarToast('Error al actualizar cantidad', 'error');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            mostrarToast('Error al actualizar cantidad', 'error');
        });
}

function eliminarDelCarrito(carritoId) {
    const formData = new FormData();
    formData.append('carrito_id', carritoId);
    formData.append('action', 'eliminar');

    fetch('/tienda/agregar_carrito.php', {
        method: 'POST',
        body: formData
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                mostrarToast('Articulo eliminado del carrito', 'success');
                location.reload();
            } else {
                mostrarToast('Error al eliminar del carrito', 'error');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            mostrarToast('Error al eliminar del carrito', 'error');
        });
}

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

function validarEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validarTelefono(telefono) {
    return /^\d{7,20}$/.test(telefono.replace(/[\s\-\+\(\)]/g, ''));
}

document.addEventListener('DOMContentLoaded', function() {
    actualizarContadorCarrito();

    document.querySelectorAll('.cantidad-input').forEach((input) => {
        input.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > 999) this.value = 999;
        });
    });
});

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
