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
    <title>CELAJE - Totebags Ecológicas</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/productos.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Totebags CELAJE ecológicas y reutilizables. Diseño minimalista y resistente.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="category-hero" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
        <div class="hero-content">
            <div class="hero-text">
                <h1><i class="fas fa-shopping-bag"></i> Totebags CELAJE</h1>
                <p>Bolsas ecológicas y reutilizables. Cuida el planeta con estilo.</p>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM products WHERE category = 'totebags'")->fetch_row()[0] ?></span>
                    <span class="stat-label">Diseños</span>
                </div>
                <div class="stat">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Ecológicas</span>
                </div>
                <div class="stat">
                    <span class="stat-number">Resistente</span>
                    <span class="stat-label">Durabilidad</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Eco Benefits Section -->
    <section class="eco-benefits">
        <div class="container">
            <h2>¿Por qué elegir nuestras totebags?</h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>100% Ecológicas</h3>
                    <p>Hechas con materiales orgánicos y procesos sostenibles</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h3>Reutilizables</h3>
                    <p>Reemplaza cientos de bolsas de plástico con una sola totebag</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h3>Resistentes</h3>
                    <p>Soportan hasta 15kg de peso sin deformarse</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3>Diseños Únicos</h3>
                    <p>Estampados exclusivos que reflejan tu personalidad</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-container">
                <div class="filter-group">
                    <label for="sizeFilter">Tamaño:</label>
                    <select id="sizeFilter" onchange="filterProducts()">
                        <option value="">Todos los tamaños</option>
                        <option value="pequeña">Pequeña</option>
                        <option value="mediana">Mediana</option>
                        <option value="grande">Grande</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="materialFilter">Material:</label>
                    <select id="materialFilter" onchange="filterProducts()">
                        <option value="">Todos los materiales</option>
                        <option value="algodón orgánico">Algodón Orgánico</option>
                        <option value="lona">Lona</option>
                        <option value="yute">Yute</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="priceFilter">Precio:</label>
                    <select id="priceFilter" onchange="filterProducts()">
                        <option value="">Todos los precios</option>
                        <option value="50-100">$50 - $100</option>
                        <option value="100-150">$100 - $150</option>
                        <option value="150-200">$150 - $200</option>
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
                <h2>Colección de Totebags</h2>
                <p>Bolsas ecológicas para un estilo de vida sostenible</p>
            </div>

            <div class="products-grid" id="productsGrid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'totebags' AND is_active = 1 ORDER BY name");
                while($product = $result->fetch_assoc()):
                    $colors = json_decode($product['colors'], true) ?: ['Natural', 'Negro'];
                    $materials = json_decode($product['materials'], true) ?: ['Algodón Orgánico'];
                ?>
                <div class="product-card" 
                     data-price="<?= $product['price'] ?>" 
                     data-colors="<?= implode(',', $colors) ?>"
                     data-name="<?= strtolower($product['name']) ?>"
                     data-size="mediana"
                     data-material="algodón orgánico">
                    
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
                            <span class="badge badge-success">Ecológica</span>
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
                                <i class="fas fa-leaf"></i> Ecológica
                            </span>
                            <span class="feature-tag">
                                <i class="fas fa-dumbbell"></i> Resistente
                            </span>
                            <span class="feature-tag">
                                <i class="fas fa-recycle"></i> Reutilizable
                            </span>
                        </div>
                        
                        <div class="product-specs">
                            <div class="spec-item">
                                <span class="spec-label">Capacidad:</span>
                                <span class="spec-value">15kg</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Dimensiones:</span>
                                <span class="spec-value">38x42cm</span>
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
                    <h3>No se encontraron totebags</h3>
                    <p>Intenta ajustar los filtros para ver más resultados</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Environmental Impact -->
    <section class="impact-section">
        <div class="container">
            <h2>Tu impacto ambiental</h2>
            <div class="impact-calculator">
                <div class="calculator-content">
                    <h3>Con una totebag CELAJE evitas:</h3>
                    <div class="impact-stats">
                        <div class="impact-stat">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Bolsas de plástico al año</div>
                        </div>
                        <div class="impact-stat">
                            <div class="stat-number">2.5kg</div>
                            <div class="stat-label">De residuos plásticos</div>
                        </div>
                        <div class="impact-stat">
                            <div class="stat-number">15L</div>
                            <div class="stat-label">De petróleo ahorrado</div>
                        </div>
                    </div>
                    <p class="impact-message">
                        <i class="fas fa-heart text-green-500"></i>
                        Cada compra contribuye a un planeta más limpio
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/productos.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>

    
    <style>
    .eco-benefits {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .eco-benefits h2 {
        text-align: center;
        margin-bottom: 50px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .benefit-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .benefit-card:hover {
        transform: translateY(-5px);
    }
    
    .benefit-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.8rem;
        color: white;
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
    
    .impact-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
    }
    
    .impact-section h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
    }
    
    .impact-calculator {
        max-width: 800px;
        margin: 0 auto;
        background: rgba(255,255,255,0.1);
        padding: 40px;
        border-radius: 12px;
        text-align: center;
    }
    
    .calculator-content h3 {
        font-size: 1.5rem;
        margin-bottom: 30px;
    }
    
    .impact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }
    
    .impact-stat {
        text-align: center;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        display: block;
        margin-bottom: 10px;
    }
    
    .stat-label {
        font-size: 1rem;
        opacity: 0.9;
    }
    
    .impact-message {
        font-size: 1.1rem;
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .text-green-500 {
        color: #10b981;
    }
    
    @media (max-width: 768px) {
        .impact-stats {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .stat-number {
            font-size: 2rem;
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