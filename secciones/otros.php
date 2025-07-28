<?php 
session_start();
include '../includes/db.php';
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['SCRIPT_NAME']) . "/../";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CELAJE - Otros Productos</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/productos.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Otros productos CELAJE. Descubre accesorios únicos y artículos especiales de edición limitada.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="category-hero" style="background: linear-gradient(135deg, #34495e, #2c3e50);">
        <div class="hero-content">
            <div class="hero-text">
                <h1><i class="fas fa-ellipsis-h"></i> Otros Productos CELAJE</h1>
                <p>Descubre artículos únicos y ediciones especiales. Productos exclusivos que no encontrarás en otro lugar.</p>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM products WHERE category = 'otros'")->fetch_row()[0] ?></span>
                    <span class="stat-label">Productos</span>
                </div>
                <div class="stat">
                    <span class="stat-number">Limitada</span>
                    <span class="stat-label">Edición</span>
                </div>
                <div class="stat">
                    <span class="stat-number">Exclusivo</span>
                    <span class="stat-label">Diseño</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Special Categories -->
    <section class="special-categories">
        <div class="container">
            <h2>Categorías Especiales</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3>Edición Limitada</h3>
                    <p>Productos únicos disponibles por tiempo limitado</p>
                </div>
                
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Colaboraciones</h3>
                    <p>Productos resultado de colaboraciones especiales</p>
                </div>
                
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3>Arte Personalizado</h3>
                    <p>Piezas únicas creadas por artistas invitados</p>
                </div>
                
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Accesorios</h3>
                    <p>Complementos perfectos para tu estilo CELAJE</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-container">
                <div class="filter-group">
                    <label for="typeFilter">Tipo:</label>
                    <select id="typeFilter" onchange="filterProducts()">
                        <option value="">Todos los tipos</option>
                        <option value="accesorio">Accesorios</option>
                        <option value="edicion limitada">Edición Limitada</option>
                        <option value="colaboracion">Colaboración</option>
                        <option value="arte">Arte Personalizado</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="availabilityFilter">Disponibilidad:</label>
                    <select id="availabilityFilter" onchange="filterProducts()">
                        <option value="">Todos</option>
                        <option value="disponible">Disponible</option>
                        <option value="limitado">Stock Limitado</option>
                        <option value="preventa">Pre-venta</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="priceFilter">Precio:</label>
                    <select id="priceFilter" onchange="filterProducts()">
                        <option value="">Todos los precios</option>
                        <option value="100-300">$100 - $300</option>
                        <option value="300-500">$300 - $500</option>
                        <option value="500-1000">$500 - $1000</option>
                        <option value="1000-2000">$1000+</option>
                    </select>
                </div>

                <button class="clear-filters" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Limpiar Filtros
                </button>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2>Productos Especiales</h2>
                <p>Artículos únicos y exclusivos que complementan tu estilo CELAJE</p>
            </div>

            <div class="products-grid" id="productsGrid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'otros' AND is_active = 1 ORDER BY name");
                while($product = $result->fetch_assoc()):
                    $colors = json_decode($product['colors'], true) ?: ['Variado'];
                    $materials = json_decode($product['materials'], true) ?: ['Materiales premium'];
                ?>
                <div class="product-card" 
                     data-price="<?= $product['price'] ?>" 
                     data-colors="<?= implode(',', $colors) ?>"
                     data-name="<?= strtolower($product['name']) ?>"
                     data-type="accesorio"
                     data-availability="disponible">
                    
                    <div class="product-image">
                        <img src="<?= $base_url . $product['image_path'] ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             loading="lazy">
                        
                        <div class="product-overlay">
                            <button class="quick-view-btn" onclick="quickView(<?= $product['id'] ?>)">
                                <i class="fas fa-eye"></i> Vista Rápida
                            </button>
                        </div>

                        <div class="product-badges">
                            <span class="badge badge-premium">Exclusivo</span>
                            <?php if ($product['stock'] < 5): ?>
                                <span class="badge badge-warning">¡Últimas piezas!</span>
                            <?php endif; ?>
                            <?php if ($product['price'] > 500): ?>
                                <span class="badge badge-luxury">Premium</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</p>
                        
                        <div class="product-features">
                            <span class="feature-tag">
                                <i class="fas fa-star"></i> Exclusivo
                            </span>
                            <span class="feature-tag">
                                <i class="fas fa-certificate"></i> Edición limitada
                            </span>
                        </div>
                        
                        <div class="product-specs">
                            <div class="spec-item">
                                <span class="spec-label">Categoría:</span>
                                <span class="spec-value">Especial</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Stock:</span>
                                <span class="spec-value">Limitado</span>
                            </div>
                        </div>

                        <div class="product-price">
                            <span class="price">$<?= number_format($product['price'], 0) ?></span>
                            <span class="price-note">MXN</span>
                        </div>

                        <div class="product-actions">
                            <button class="btn-add-cart" onclick="addToCart(<?= $product['id'] ?>)">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Agregar al Carrito</span>
                            </button>
                            <button class="btn-wishlist" onclick="toggleWishlist(<?= $product['id'] ?>)">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="no-products" id="noProducts" style="display: none;">
                <div class="no-products-content">
                    <i class="fas fa-search"></i>
                    <h3>No se encontraron productos</h3>
                    <p>Intenta ajustar los filtros para ver más resultados</p>
                </div>
            </div>
        </div>
    </section>



    <?php include '../includes/footer.php'; ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/productos.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>

    
    <style>
    .special-categories {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .special-categories h2 {
        text-align: center;
        margin-bottom: 50px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .category-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
    }
    
    .category-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #34495e, #2c3e50);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.8rem;
        color: white;
    }
    
    .badge-premium {
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        color: #1a1a1a;
    }
    
    .badge-luxury {
        background: linear-gradient(135deg, #8e44ad, #9b59b6);
        color: white;
    }
    
    .newsletter-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #34495e, #2c3e50);
        color: white;
        text-align: center;
    }
    
    .newsletter-content h2 {
        font-size: 2.5rem;
        margin-bottom: 20px;
    }
    
    .newsletter-content p {
        font-size: 1.1rem;
        margin-bottom: 40px;
        opacity: 0.9;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .newsletter-form {
        max-width: 500px;
        margin: 0 auto 40px;
    }
    
    .newsletter-form .form-group {
        display: flex;
        gap: 0;
        margin-bottom: 10px;
    }
    
    .newsletter-form input {
        flex: 1;
        padding: 15px 20px;
        border: none;
        border-radius: 50px 0 0 50px;
        font-size: 1rem;
        outline: none;
    }
    
    .newsletter-form button {
        padding: 15px 30px;
        background: var(--accent-color);
        color: white;
        border: none;
        border-radius: 0 50px 50px 0;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .newsletter-form button:hover {
        background: #e55a2b;
    }
    
    .newsletter-form small {
        opacity: 0.8;
        font-size: 0.9rem;
    }
    
    .newsletter-benefits {
        display: flex;
        justify-content: center;
        gap: 40px;
        flex-wrap: wrap;
    }
    
    .benefit {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
    }
    
    .benefit i {
        color: var(--accent-color);
        font-size: 1.2rem;
    }
    
    .product-specs {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }
    
    .spec-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 8px;
        background: var(--secondary-color);
        border-radius: 6px;
        flex: 1;
    }
    
    .spec-label {
        color: var(--text-light);
        font-size: 0.8rem;
    }
    
    .spec-value {
        font-weight: 600;
        color: var(--text-dark);
    }
    
    @media (max-width: 768px) {
        .newsletter-form .form-group {
            flex-direction: column;
        }
        
        .newsletter-form input,
        .newsletter-form button {
            border-radius: 25px;
        }
        
        .newsletter-benefits {
            flex-direction: column;
            gap: 20px;
        }
        
        .product-specs {
            flex-direction: column;
            gap: 10px;
        }
        
        .spec-item {
            flex-direction: row;
            justify-content: space-between;
        }
    }
    </style>
    
    <script>
    document.getElementById('newsletterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input[type="email"]').value;
        
        // Simulate newsletter subscription
        alert('¡Gracias por suscribirte! Recibirás nuestras novedades en ' + email);
        this.reset();
    });
    </script>
</body>
</html>