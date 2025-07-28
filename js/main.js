// File: js/main.js
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM cargado - Iniciando funcionalidades principales");

    // --- Definición de la URL Base ---
    // Esta variable 'window.CELAJE_BASE_URL' DEBE ser definida en el PHP antes de cargar este script (main.js).
    // Por ejemplo, en favoritos.php o playeras.php: <script>window.CELAJE_BASE_URL = '<?php echo $project_root_url; ?>';</script>
    // Si no está definida por PHP, se asume que el proyecto está en la raíz del dominio (/).
    const BASE_URL = window.CELAJE_BASE_URL || '/';
    console.log("BASE_URL en main.js:", BASE_URL); // Útil para depuración: VERIFICA QUE ESTA URL ES CORRECTA

    // --- UTILITIES ---
    // Función auxiliar para obtener el código de color hexadecimal (si es necesario para estilos)
    function getColorCode(color) {
        const colors = {
            'Negro': '#000000',
            'Blanco': '#FFFFFF',
            'Morado': '#674e82',
            'Azul': '#3b82f6',
            'Gris': '#6b7280',
            'Rosa': '#ec4899',
            'Verde': '#10b981',
            'Rojo': '#ef4444',
            'Naranja': '#FFA500'
        };
        return colors[color] || '#cccccc'; // Devuelve un gris por defecto si el color no está mapeado
    }

    // --- SISTEMA DE NOTIFICACIONES PERSONALIZADO ---
    const notificationContainer = document.createElement('div');
    notificationContainer.id = 'notificationContainer';
    notificationContainer.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        display: flex;
        flex-direction: column;
        gap: 10px;
    `;
    document.body.appendChild(notificationContainer);

    /**
     * Muestra una notificación temporal al usuario.
     * @param {string} message - El mensaje a mostrar.
     * @param {string} type - El tipo de notificación ('success', 'error', 'info').
     * @param {number} duration - Duración en milisegundos que la notificación estará visible.
     */
    function showNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            background: white;
            color: #333;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 250px;
            border-left: 4px solid;
            z-index: 2000;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        `;

        if (type === 'success') {
            notification.style.borderLeftColor = '#28a745';
        } else if (type === 'error') {
            notification.style.borderLeftColor = '#dc3545';
        } else { // info
            notification.style.borderLeftColor = '#17a2b8';
        }

        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-times-circle' : 'fa-info-circle')} me-2"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close" aria-label="Cerrar notificación">&times;</button>
        `;

        notificationContainer.appendChild(notification);

        // Animar entrada
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 10); // Pequeño retraso para que la transición se aplique

        // Cerrar notificación al hacer click en el botón de cerrar o automáticamente
        const closeButton = notification.querySelector('.notification-close');
        closeButton.addEventListener('click', () => {
            hideNotification(notification);
        });

        if (duration > 0) {
            setTimeout(() => {
                hideNotification(notification);
            }, duration);
        }
    }

    /**
     * Oculta y elimina una notificación del DOM.
     * @param {HTMLElement} notificationElement - El elemento de la notificación a ocultar.
     */
    function hideNotification(notificationElement) {
        notificationElement.style.opacity = '0';
        notificationElement.style.transform = 'translateY(-20px)';
        notificationElement.addEventListener('transitionend', () => {
            notificationElement.remove();
        }, { once: true });
    }


    // --- FUNCIÓN GLOBAL PARA GESTIONAR FAVORITOS ---
    /**
     * Añade o elimina un producto de la lista de favoritos.
     * @param {number} productId - El ID del producto.
     * @param {HTMLElement} buttonElement - El elemento del botón de favoritos que fue clickeado.
     */
    window.toggleWishlist = function(productId, buttonElement) {
        fetch(`${BASE_URL}ajax/add_to_wishlist.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => {
            if (!response.ok) {
                // Si la respuesta no es 200 OK, intenta leer el texto para depurar
                return response.text().then(text => {
                    throw new Error(`Server response for wishlist not ok: ${response.status} ${response.statusText} - ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message, data.action === 'added' ? 'success' : 'info');

                // Actualizar estado visual del botón
                if (buttonElement) {
                    const isActive = data.action === 'added';
                    buttonElement.classList.toggle('active', isActive);
                    const icon = buttonElement.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('far', !isActive); // Corazón vacío
                        icon.classList.toggle('fas', isActive);  // Corazón lleno
                    }
                }
                // Actualizar el contador de favoritos en el header
                updateWishlistCount();

                // Disparar un evento personalizado para que otras partes de la UI puedan reaccionar
                const event = new CustomEvent('wishlistUpdated', { detail: { productId: productId, action: data.action } });
                document.dispatchEvent(event);

            } else {
                showNotification(data.message || 'Error al actualizar favoritos', 'error');
            }
        })
        .catch(error => {
            showNotification('Error de conexión o datos inválidos del servidor. Revisa la consola.', 'error');
            console.error('Error en la operación de favoritos:', error);
        });
    };

    // --- UPDATE COUNTERS ---
    /**
     * Obtiene y actualiza el número de productos en el carrito.
     */
    function updateCartCount() {
        fetch(`${BASE_URL}ajax/get_cart_count.php`) // Petición al script del conteo del carrito
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCountElement = document.getElementById('cartCount');
                if (cartCountElement) {
                    cartCountElement.textContent = data.count; // Actualiza el texto del contador
                }
            }
        })
        .catch(error => console.error('Error al actualizar contador del carrito:', error));
    }

    /**
     * Obtiene y actualiza el número de productos en la lista de favoritos.
     */
    function updateWishlistCount() {
        // Usa BASE_URL para la ruta correcta del script AJAX
        fetch(`${BASE_URL}ajax/get_wishlist_count.php`) // Petición al script del conteo de favoritos
        .then(response => {
            if (!response.ok) {
                console.error('Network response for wishlist count not ok:', response.status, response.statusText);
                return response.text().then(text => {
                    throw new Error(`Server response not JSON for wishlist count: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const wishlistCountElement = document.getElementById('wishlistCount');
                if (wishlistCountElement) {
                    wishlistCountElement.textContent = data.count; // Actualiza el texto del contador
                }
            } else {
                console.error('Error en la respuesta del contador de favoritos:', data.message);
            }
        })
        .catch(error => console.error('Error al actualizar contador de favoritos:', error));
    }

    // Inicializa los contadores del carrito y favoritos al cargar la página
    updateCartCount();
    updateWishlistCount();
});