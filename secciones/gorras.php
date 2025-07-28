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
    <title>CELAJE - Gorras Exclusivas</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/productos.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Gorras CELAJE de alta calidad. Estilo urbano y moderno para complementar tu outfit.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="category-hero" style="background: linear-gradient(135deg, #e67e22, #d35400);">
        <div class="hero-content">
            <div class="hero-text">
                <h1><i class="fas fa-hat-cowboy"></i> Gorras CELAJE</h1>
                <p>Estilo urbano y moderno. Perfectas para complementar cualquier outfit.</p>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM products WHERE category = 'gorras'")->fetch_row()[0] ?></span>
                    <span class="stat-label">Modelos</span>
                </div>
                <div class="stat">
                    <span class="stat-number">Ajustable</span>
                    <span class="stat-label">Talla única</span>
                </div>
                <div class="stat">
                    <span class="stat-number">Premium</span>
                    <span class="stat-label">Calidad</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2>Características de nuestras gorras</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-adjust"></i>
                    </div>
                    <h3>Ajustable</h3>
                    <p>Sistema de ajuste trasero para adaptarse a cualquier talla</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-sun"></i>
                    </div>
                    <h3>Protección UV</h3>
                    <p>Visera amplia que protege del sol y los rayos UV</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h3>Transpirable</h3>
                    <p>Materiales que permiten la ventilación y evitan la sudoración</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3>Diseños Únicos</h3>
                    <p>Bordados y estampados exclusivos de la marca CELAJE</p>
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
                        <option value="snapback">Snapback</option>
                        <option value="trucker">Trucker</option>
                        <option value="dad hat">Dad Hat</option>
                        <option value="fitted">Fitted</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="colorFilter">Color:</label>
                    <select id="colorFilter" onchange="filterProducts()">
                        <option value="">Todos los colores</option>
                        <option value="Negro">Negro</option>
                        <option value="Blanco">Blanco</option>
                        <option value="Morado">Morado</option>
                        <option value="Azul">Azul</option>
                        <option value="Gris">Gris</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="priceFilter">Precio:</label>
                    <select id="priceFilter" onchange="filterProducts()">
                        <option value="">Todos los precios</option>
                        <option value="100-200">$100 - $200</option>
                        <option value="200-300">$200 - $300</option>
                        <option value="300-400">$300 - $400</option>
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
                <h2>Colección de Gorras</h2>
                <p>Encuentra la gorra perfecta para tu estilo personal</p>
            </div>

            <div class="products-grid" id="productsGrid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'gorras' AND is_active = 1 ORDER BY name");
                while($product = $result->fetch_assoc()):
                    $colors = json_decode($product['colors'], true) ?: ['Negro', 'Blanco'];
                    $materials = json_decode($product['materials'], true) ?: ['Algodón', 'Poliéster'];
                ?>
                <div class="product-card" 
                     data-price="<?= $product['price'] ?>" 
                     data-colors="<?= implode(',', $colors) ?>"
                     data-name="<?= strtolower($product['name']) ?>"
                     data-style="snapback">
                    
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
                            <?php if ($product['stock'] < 5): ?>
                                <span class="badge badge-warning">¡Últimas piezas!</span>
                            <?php endif; ?>
                            <span class="badge badge-info">Talla única</span>
                        </div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</p>
                        
                        <div class="product-features">
                            <span class="feature-tag">
                                <i class="fas fa-adjust"></i> Ajustable
                            </span>
                            <span class="feature-tag">
                                <i class="fas fa-sun"></i> Protección UV
                            </span>
                        </div>
                        
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
                    <h3>No se encontraron gorras</h3>
                    <p>Intenta ajustar los filtros para ver más resultados</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Care Instructions -->
    <section class="care-section">
        <div class="container">
            <h2>Cuidado de tu gorra</h2>
            <div class="care-grid">
                <div class="care-item">
                    <div class="care-icon">
                        <i class="fas fa-hand-sparkles"></i>
                    </div>
                    <h3>Limpieza</h3>
                    <p>Lava a mano con agua fría y jabón suave. Evita sumergir completamente.</p>
                </div>
                
                <div class="care-item">
                    <div class="care-icon">
                        <i class="fas fa-wind"></i>
                    </div>
                    <h3>Secado</h3>
                    <p>Deja secar al aire libre, manteniendo la forma original. No uses secadora.</p>
                </div>
                
                <div class="care-item">
                    <div class="care-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <h3>Almacenamiento</h3>
                    <p>Guarda en lugar seco y ventilado. Usa un soporte para mantener la forma.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/productos.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>
    
    <style>
    .care-section {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .care-section h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .care-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .care-item {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .care-item:hover {
        transform: translateY(-5px);
    }
    
    .care-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #e67e22, #d35400);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.8rem;
        color: white;
    }
    
    .care-item h3 {
        margin-bottom: 15px;
        color: var(--text-dark);
    }
    
    .care-item p {
        color: var(--text-light);
        line-height: 1.6;
    }
    </style>
</body>
</html>

<?php
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