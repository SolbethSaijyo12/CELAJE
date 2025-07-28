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
    <title>CELAJE - Tatuajes Temporales</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/productos.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Tatuajes temporales CELAJE con diseños únicos. Duran hasta 7 días y son seguros para la piel.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="category-hero" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
        <div class="hero-content">
            <div class="hero-text">
                <h1><i class="fas fa-paint-brush"></i> Tatuajes Temporales CELAJE</h1>
                <p>Expresa tu arte corporal sin compromiso. Diseños únicos que duran hasta 7 días.</p>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM products WHERE category = 'tatuajes'")->fetch_row()[0] ?></span>
                    <span class="stat-label">Diseños</span>
                </div>
                <div class="stat">
                    <span class="stat-number">7</span>
                    <span class="stat-label">Días duración</span>
                </div>
                <div class="stat">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Seguro</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Safety Section -->
    <section class="safety-section">
        <div class="container">
            <h2>Seguridad y Calidad</h2>
            <div class="safety-grid">
                <div class="safety-card">
                    <div class="safety-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Tinta Segura</h3>
                    <p>Certificada FDA, no tóxica y libre de metales pesados</p>
                </div>
                
                <div class="safety-card">
                    <div class="safety-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Ingredientes Naturales</h3>
                    <p>Base de agua con extractos vegetales hipoalergénicos</p>
                </div>
                
                <div class="safety-card">
                    <div class="safety-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Duración Perfecta</h3>
                    <p>Entre 3 a 7 días dependiendo del cuidado y ubicación</p>
                </div>
                
                <div class="safety-card">
                    <div class="safety-icon">
                        <i class="fas fa-hand-sparkles"></i>
                    </div>
                    <h3>Fácil Remoción</h3>
                    <p>Se quita fácilmente con aceite o alcohol sin irritar</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-container">
                <div class="filter-group">
                    <label for="styleFilter">Estilo:</label>
                    <select id="styleFilter" onchange="filterProducts()">
                        <option value="">Todos los estilos</option>
                        <option value="minimalista">Minimalista</option>
                        <option value="tribal">Tribal</option>
                        <option value="floral">Floral</option>
                        <option value="geométrico">Geométrico</option>
                        <option value="texto">Texto/Frases</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sizeFilter">Tamaño:</label>
                    <select id="sizeFilter" onchange="filterProducts()">
                        <option value="">Todos los tamaños</option>
                        <option value="pequeño">Pequeño (2-5cm)</option>
                        <option value="mediano">Mediano (5-10cm)</option>
                        <option value="grande">Grande (10-15cm)</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="colorFilter">Color:</label>
                    <select id="colorFilter" onchange="filterProducts()">
                        <option value="">Todos los colores</option>
                        <option value="Negro">Negro</option>
                        <option value="Multicolor">Multicolor</option>
                        <option value="Dorado">Dorado</option>
                        <option value="Plateado">Plateado</option>
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
                <h2>Colección de Tatuajes Temporales</h2>
                <p>Diseños únicos para expresar tu personalidad sin compromiso permanente</p>
            </div>

            <div class="products-grid" id="productsGrid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'tatuajes' AND is_active = 1 ORDER BY name");
                while($product = $result->fetch_assoc()):
                    $colors = json_decode($product['colors'], true) ?: ['Negro', 'Multicolor'];
                    $materials = json_decode($product['materials'], true) ?: ['Tinta segura para piel'];
                ?>
                <div class="product-card" 
                     data-price="<?= $product['price'] ?>" 
                     data-colors="<?= implode(',', $colors) ?>"
                     data-name="<?= strtolower($product['name']) ?>"
                     data-style="minimalista"
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
                            <span class="badge badge-success">Seguro FDA</span>
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
                                <i class="fas fa-shield-alt"></i> Seguro
                            </span>
                            <span class="feature-tag">
                                <i class="fas fa-clock"></i> 3-7 días
                            </span>
                            <span class="feature-tag">
                                <i class="fas fa-leaf"></i> Natural
                            </span>
                        </div>
                        
                        <div class="product-specs">
                            <div class="spec-item">
                                <span class="spec-label">Duración:</span>
                                <span class="spec-value">3-7 días</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Aplicación:</span>
                                <span class="spec-value">Con agua</span>
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
                    <h3>No se encontraron tatuajes</h3>
                    <p>Intenta ajustar los filtros para ver más resultados</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Guide -->
    <section class="application-guide">
        <div class="container">
            <h2>¿Cómo aplicar tu tatuaje temporal?</h2>
            <div class="guide-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Limpia la piel</h3>
                        <p>Asegúrate de que esté libre de aceites, cremas y vello</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Retira el plástico</h3>
                        <p>Despega cuidadosamente la lámina protectora</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Aplica con agua</h3>
                        <p>Coloca el tatuaje y presiona con una esponja húmeda por 30 segundos</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Retira el papel</h3>
                        <p>Despega lentamente el papel y deja secar completamente</p>
                    </div>
                </div>
            </div>
            
            <div class="care-tips">
                <h3>💡 Consejos para mayor duración:</h3>
                <ul>
                    <li>Evita frotar la zona durante las primeras 2 horas</li>
                    <li>No apliques cremas o aceites sobre el tatuaje</li>
                    <li>Sécalo suavemente después de ducharte</li>
                    <li>Evita piscinas con cloro las primeras 24 horas</li>
                </ul>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/productos.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>

    
    <style>
    .safety-section {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .safety-section h2 {
        text-align: center;
        margin-bottom: 50px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .safety-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .safety-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .safety-card:hover {
        transform: translateY(-5px);
    }
    
    .safety-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #8e44ad, #9b59b6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.8rem;
        color: white;
    }
    
    .application-guide {
        padding: 80px 0;
        background: white;
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
        margin: 0 auto 50px;
    }
    
    .step {
        background: var(--secondary-color);
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .step:hover {
        transform: translateY(-5px);
    }
    
    .step-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #8e44ad, #9b59b6);
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
    
    .care-tips {
        background: var(--secondary-color);
        padding: 30px;
        border-radius: 12px;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .care-tips h3 {
        margin-bottom: 20px;
        color: var(--text-dark);
        text-align: center;
    }
    
    .care-tips ul {
        list-style: none;
        padding: 0;
    }
    
    .care-tips li {
        padding: 8px 0;
        color: var(--text-dark);
        position: relative;
        padding-left: 25px;
    }
    
    .care-tips li:before {
        content: "✓";
        position: absolute;
        left: 0;
        color: #8e44ad;
        font-weight: bold;
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