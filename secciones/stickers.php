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
    <title>CELAJE - Stickers Exclusivos</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/productos.css">
 <link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Stickers CELAJE con diseños únicos. Perfectos para personalizar tus objetos favoritos.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="category-hero" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
        <div class="hero-content">
            <div class="hero-text">
                <h1><i class="fas fa-star"></i> Stickers CELAJE</h1>
                <p>Diseños únicos para personalizar tu mundo. Expresa tu creatividad.</p>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM products WHERE category = 'stickers'")->fetch_row()[0] ?></span>
                    <span class="stat-label">Diseños</span>
                </div>
                <div class="stat">
                    <span class="stat-number">Resistente</span>
                    <span class="stat-label">Al agua</span>
                </div>
                <div class="stat">
                    <span class="stat-number">3-5</span>
                    <span class="stat-label">Años duración</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2>Características de nuestros stickers</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h3>Resistente al Agua</h3>
                    <p>Material vinílico que resiste lluvia, lavado y humedad</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-sun"></i>
                    </div>
                    <h3>Anti UV</h3>
                    <p>Los colores no se desvanecen con la exposición solar</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                    <h3>Fácil Aplicación</h3>
                    <p>Se adhiere perfectamente sin burbujas de aire</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h3>Removible</h3>
                    <p>Se puede quitar sin dejar residuos pegajosos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-container">
                <div class="filter-group">
                    <label for="categoryFilter">Categoría:</label>
                    <select id="categoryFilter" onchange="filterProducts()">
                        <option value="">Todas las categorías</option>
                        <option value="logo">Logo CELAJE</option>
                        <option value="frases">Frases</option>
                        <option value="ilustraciones">Ilustraciones</option>
                        <option value="pack">Pack Variado</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sizeFilter">Tamaño:</label>
                    <select id="sizeFilter" onchange="filterProducts()">
                        <option value="">Todos los tamaños</option>
                        <option value="pequeño">Pequeño (5cm)</option>
                        <option value="mediano">Mediano (10cm)</option>
                        <option value="grande">Grande (15cm)</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="priceFilter">Precio:</label>
                    <select id="priceFilter" onchange="filterProducts()">
                        <option value="">Todos los precios</option>
                        <option value="20-50">$20 - $50</option>
                        <option value="50-80">$50 - $80</option>
                        <option value="80-120">$80 - $120</option>
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
                <h2>Colección de Stickers</h2>
                <p>Diseños únicos para personalizar laptops, celulares, cuadernos y más</p>
            </div>

            <div class="products-grid" id="productsGrid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'stickers' AND is_active = 1 ORDER BY name");
                while($product = $result->fetch_assoc()):
                    $colors = json_decode($product['colors'], true) ?: ['Multicolor'];
                    $materials = json_decode($product['materials'], true) ?: ['Vinyl resistente'];
                ?>
                <div class="product-card" 
                     data-price="<?= $product['price'] ?>" 
                     data-colors="<?= implode(',', $colors) ?>"
                     data-name="<?= strtolower($product['name']) ?>"
                     data-category="pack"
                     data-size="mediano">
                    
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
                            <span class="badge badge-info">Resistente al agua</span>
                            <?php if ($product['stock'] < 5): ?>
                                <span class="badge badge-warning">¡Últimas piezas!</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</p>
                        
                        <div class="product-features">
                            <span class="feature-tag">
                                <i class="fas fa-tint"></i> Resistente al agua
                            </span>
                            <span class="feature-tag">
                                <i class="fas fa-sun"></i> Anti UV
                            </span>
                        </div>
                        
                        <div class="product-specs">
                            <div class="spec-item">
                                <span class="spec-label">Material:</span>
                                <span class="spec-value">Vinyl Premium</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Duración:</span>
                                <span class="spec-value">3-5 años</span>
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
                    <h3>No se encontraron stickers</h3>
                    <p>Intenta ajustar los filtros para ver más resultados</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Guide -->
    <section class="application-guide">
        <div class="container">
            <h2>¿Cómo aplicar tus stickers?</h2>
            <div class="guide-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Limpia la superficie</h3>
                        <p>Asegúrate de que esté libre de polvo, grasa y humedad</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Retira el papel protector</h3>
                        <p>Despega cuidadosamente desde una esquina</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Aplica gradualmente</h3>
                        <p>Presiona desde el centro hacia los bordes para evitar burbujas</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Presiona firmemente</h3>
                        <p>Usa una tarjeta para asegurar una adhesión perfecta</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/productos.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>

    
    <style>
    .application-guide {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .application-guide h2 {
        text-align: center;
        margin-bottom: 50px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .guide-steps {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .step {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .step:hover {
        transform: translateY(-5px);
    }
    
    .step-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0 auto 20px;
    }
    
    .step-content h3 {
        margin-bottom: 15px;
        color: var(--text-dark);
    }
    
    .step-content p {
        color: var(--text-light);
        line-height: 1.6;
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
        .guide-steps {
            grid-template-columns: 1fr;
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
</body>
</html>