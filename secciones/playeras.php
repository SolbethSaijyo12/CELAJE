<?php
session_start();
include '../includes/db.php';
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['SCRIPT_NAME']) . "/../";

// Function to get color code
function getColorCode($color) {
    $colors = [
        'Negro' => '#000000',
        'Blanco' => '#FFFFFF',
        'Morado' => '#674e82',
        'Azul' => '#3b82f6',
        'Gris' => '#6b7280',
        'Rosa' => '#ec4899',
        'Verde' => '#10b981',
        'Rojo' => '#ef4444'
    ];
    return $colors[$color] ?? '#cccccc';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CELAJE - Playeras Exclusivas</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/productos.css">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer">
    <meta name="description" content="Descubre nuestra colección de playeras exclusivas CELAJE. Diseños únicos, alta calidad y estilo urbano.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <section class="category-hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1><i class="fas fa-tshirt"></i> Playeras CELAJE</h1>
                <p>Diseños únicos que expresan tu personalidad. Calidad premium en cada prenda.</p>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM products WHERE category = 'playeras'")->fetch_row()[0] ?></span>
                    <span class="stat-label">Diseños</span>
                </div>
                <div class="stat">
                    <span class="stat-number">6</span>
                    <span class="stat-label">Colores</span>
                </div>
                <div class="stat">
                    <span class="stat-number">7</span>
                    <span class="stat-label">Tallas</span>
                </div>
            </div>
        </div>
    </section>

    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2>Nuestra Colección de Playeras</h2>
                <p>Cada playera es una obra de arte diseñada para destacar tu estilo único</p>
            </div>

            <div class="products-grid" id="productsGrid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'playeras' AND is_active = 1 ORDER BY name");
                while($product = $result->fetch_assoc()):
                    $colors = explode(',', $product['colors'] ?: 'Negro,Blanco');
                    $materials = explode(',', $product['materials'] ?: 'Algodón 100%');
                    $sizes = explode(',', $product['sizes'] ?: 'S,M,L');
                    $isFavorite = isset($_SESSION['user_wishlist_ids']) ? in_array($product['id'], $_SESSION['user_wishlist_ids']) : false;
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?= $base_url . $product['image_path'] ?>"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             loading="lazy">

                        <div class="product-overlay">
                            <button class="quick-view-btn" data-product-id="<?= $product['id'] ?>">
                                <i class="fas fa-eye"></i> Vista Rápida
                            </button>
                        </div>

                        <div class="product-badges">
                            <?php if ($product['stock'] < 20): // Changed threshold for "Últimas piezas" ?>
                                <span class="badge badge-warning">¡Últimas piezas!</span>
                            <?php endif; ?>
                            <?php if ($product['price'] < 270): // Changed threshold for "Oferta" ?>
                                <span class="badge badge-success">Oferta</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</p>

                        <div class="product-colors">
                            <span class="colors-label">Colores:</span>
                            <div class="color-swatches">
                                <?php foreach (array_slice($colors, 0, 4) as $color): ?>
                                    <span class="color-swatch"
                                          style="background-color: <?= getColorCode($color) ?>"
                                          title="<?= $color ?>"></span>
                                <?php endforeach; ?>
                                <?php if (count($colors) > 4): ?>
                                    <span class="more-colors">+<?= count($colors) - 4 ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-price">
                            <span class="price">$<?= number_format($product['price'], 0) ?></span>
                            <span class="price-note">MXN</span>
                        </div>

                        <div class="product-actions">
                            <button class="btn-add-cart" data-product-id="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Agregar al Carrito</span>
                            </button>
                            <button class="btn-wishlist <?= $isFavorite ? 'active' : '' ?>" 
                                    data-product-id="<?= $product['id'] ?>">
                                <i class="<?= $isFavorite ? 'fas' : 'far' ?> fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <div id="quickViewModal" class="modal-overlay">
        <div class="modal-content quick-view-info">
            <div class="modal-header">
                <h3 id="quickViewTitle"></h3>
                <button class="modal-close" id="quickViewClose">&times;</button>
            </div>
            <div class="modal-body">
                <div class="quick-view-product-detail">
                    <div class="quick-view-image">
                        <img id="quickViewImage" src="" alt="Product Image">
                    </div>
                    <div class="quick-view-details">
                        <p id="quickViewDescription"></p>
                        <div class="product-colors" id="quickViewColors">
                            <span class="colors-label">Colores:</span>
                            <div class="color-swatches" id="quickViewColorSwatches"></div>
                        </div>
                        <div class="product-price">
                            <span class="price" id="quickViewPrice"></span>
                            <span class="price-note">MXN</span>
                        </div>
                        <div class="product-actions">
                            <button class="btn-add-cart" id="quickViewAddCartBtn">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Agregar al Carrito</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script>
        // Función para mostrar notificaciones
        function showNotification(message, type = 'info', duration = 3000) {
            const notificationContainer = document.querySelector('body');
            let notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-times-circle' : 'fa-info-circle')}"></i>
                    <span>${message}</span>
                </div>
                <button class="notification-close">&times;</button>
            `;
            notificationContainer.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            setTimeout(() => {
                notification.classList.remove('show');
                notification.addEventListener('transitionend', () => {
                    notification.remove();
                });
            }, duration);

            notification.querySelector('.notification-close').addEventListener('click', () => {
                notification.classList.remove('show');
                notification.addEventListener('transitionend', () => {
                    notification.remove();
                });
            });
        }

        // Función para obtener código de color
        function getColorCode(color) {
            const colors = {
                'Negro': '#000000',
                'Blanco': '#FFFFFF',
                'Morado': '#674e82',
                'Azul': '#3b82f6',
                'Gris': '#6b7280',
                'Rosa': '#ec4899',
                'Verde': '#10b981',
                'Rojo': '#ef4444'
            };
            return colors[color] || '#cccccc';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const quickViewModal = document.getElementById('quickViewModal');
            const quickViewCloseBtn = document.getElementById('quickViewClose');
            const quickViewTitle = document.getElementById('quickViewTitle');
            const quickViewImage = document.getElementById('quickViewImage');
            const quickViewDescription = document.getElementById('quickViewDescription');
            const quickViewColorSwatches = document.getElementById('quickViewColorSwatches');
            const quickViewPrice = document.getElementById('quickViewPrice');
            const quickViewAddCartBtn = document.getElementById('quickViewAddCartBtn');

            // Vista rápida
            document.querySelectorAll('.quick-view-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    fetch(`<?= $base_url ?>api/get_product.php?id=${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const product = data.product;
                                quickViewTitle.textContent = product.name;
                                quickViewImage.src = `<?= $base_url ?>${product.image_path}`;
                                quickViewImage.alt = product.name;
                                quickViewDescription.textContent = product.description;
                                quickViewPrice.textContent = `$${parseFloat(product.price).toFixed(2)}`;

                                quickViewColorSwatches.innerHTML = '';
                                if (product.colors && product.colors.length > 0) {
                                    product.colors.forEach(color => {
                                        const colorSwatch = document.createElement('span');
                                        colorSwatch.className = 'color-swatch';
                                        colorSwatch.style.backgroundColor = getColorCode(color);
                                        colorSwatch.title = color;
                                        quickViewColorSwatches.appendChild(colorSwatch);
                                    });
                                }

                                quickViewAddCartBtn.onclick = () => {
                                    addToCart(product.id);
                                };

                                quickViewModal.style.display = 'flex';
                                document.body.classList.add('modal-open');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('Error al cargar el producto', 'error');
                        });
                });
            });

            // Cerrar modal
            quickViewCloseBtn.addEventListener('click', () => {
                quickViewModal.style.display = 'none';
                document.body.classList.remove('modal-open');
            });

            quickViewModal.addEventListener('click', (e) => {
                if (e.target === quickViewModal) {
                    quickViewModal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                }
            });

            // Agregar al carrito
            document.querySelectorAll('.btn-add-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    addToCart(productId);
                });
            });

            // Función para agregar al carrito
            function addToCart(productId) {
                if (document.body.dataset.userLoggedIn === 'false') {
                    showNotification('Debes iniciar sesión para agregar productos al carrito', 'info');
                    return;
                }

                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', 1);

                fetch('<?= $base_url ?>ajax/add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Producto agregado al carrito', 'success');
                        // Actualizar contador del carrito
                        updateCartCount();
                    } else {
                        showNotification(data.error || 'Error al agregar al carrito', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error de conexión', 'error');
                });
            }

            // Actualizar contador del carrito
            function updateCartCount() {
                fetch('<?= $base_url ?>ajax/get_cart_count.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('cartCount').textContent = data.count;
                        }
                    });
            }

            // Favoritos
            document.querySelectorAll('.btn-wishlist').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    toggleWishlist(productId, this);
                });
            });

            // Función para favoritos
            function toggleWishlist(productId, button) {
                if (document.body.dataset.userLoggedIn === 'false') {
                    showNotification('Debes iniciar sesión para agregar productos a favoritos', 'info');
                    return;
                }

                const formData = new FormData();
                formData.append('product_id', productId);

                fetch('<?= $base_url ?>ajax/add_to_wishlist.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const icon = button.querySelector('i');
                        if (data.action === 'added') {
                            button.classList.add('active');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        } else {
                            button.classList.remove('active');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        }
                        showNotification(data.message, data.action === 'added' ? 'success' : 'info');
                        // Actualizar contador de favoritos
                        updateWishlistCount();
                    } else {
                        showNotification(data.message || 'Error al actualizar favoritos', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error de conexión', 'error');
                });
            }

            // Actualizar contador de favoritos
            function updateWishlistCount() {
                fetch('<?= $base_url ?>ajax/get_wishlist_count.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('wishlistCount').textContent = data.count;
                        }
                    });
            }
        });
    </script>
</body>
</html>