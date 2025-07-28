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
    <title>CELAJE - Sudaderas Premium</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/productos.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Sudaderas CELAJE de alta calidad. Comodidad y estilo para cualquier ocasión.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="category-hero" style="background: linear-gradient(135deg, #2c3e50, #34495e);">
        <div class="hero-content">
            <div class="hero-text">
                <h1><i class="fas fa-user-tie"></i> Sudaderas CELAJE</h1>
                <p>Comodidad premium y estilo urbano. Perfectas para cualquier temporada.</p>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM products WHERE category = 'sudaderas'")->fetch_row()[0] ?></span>
                    <span class="stat-label">Modelos</span>
                </div>
                <div class="stat">
                    <span class="stat-number">5</span>
                    <span class="stat-label">Colores</span>
                </div>
                <div class="stat">
                    <span class="stat-number">Premium</span>
                    <span class="stat-label">Calidad</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Material Info -->
    <section class="material-info">
        <div class="container">
            <div class="material-grid">
                <div class="material-card">
                    <div class="material-icon">
                        <i class="fas fa-snowflake"></i>
                    </div>
                    <h3>Algodón-Poliéster</h3>
                    <p>Mezcla perfecta para máxima comodidad y durabilidad</p>
                </div>
                <div class="material-card">
                    <div class="material-icon">
                        <i class="fas fa-wind"></i>
                    </div>
                    <h3>Transpirable</h3>
                    <p>Tecnología que permite la circulación del aire</p>
                </div>
                <div class="material-card">
                    <div class="material-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Resistente</h3>
                    <p>Mantiene su forma y color lavado tras lavado</p>
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
                        <option value="con capucha">Con Capucha</option>
                        <option value="sin capucha">Sin Capucha</option>
                        <option value="crop">Crop</option>
                        <option value="oversized">Oversized</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="colorFilter">Color:</label>
                    <select id="colorFilter" onchange="filterProducts()">
                        <option value="">Todos los colores</option>
                        <option value="Negro">Negro</option>
                        <option value="Gris">Gris</option>
                        <option value="Morado">Morado</option>
                        <option value="Azul">Azul</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="priceFilter">Precio:</label>
                    <select id="priceFilter" onchange="filterProducts()">
                        <option value="">Todos los precios</option>
                        <option value="300-500">$300 - $500</option>
                        <option value="500-700">$500 - $700</option>
                        <option value="700-900">$700 - $900</option>
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
                <h2>Colección de Sudaderas</h2>
                <p>Diseñadas para brindarte el máximo confort sin sacrificar el estilo</p>
            </div>

            <div class="products-grid" id="productsGrid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'sudaderas' AND is_active = 1 ORDER BY name");
                while($product = $result->fetch_assoc()):
                    $colors = json_decode($product['colors'], true) ?: ['Negro', 'Gris'];
                    $materials = json_decode($product['materials'], true) ?: ['Algodón-Poliéster'];
                    $sizes = json_decode($product['sizes'], true) ?: ['S', 'M', 'L', 'XL'];
                ?>
                <div class="product-card" 
                     data-price="<?= $product['price'] ?>" 
                     data-colors="<?= implode(',', $colors) ?>"
                     data-name="<?= strtolower($product['name']) ?>"
                     data-style="<?= strpos(strtolower($product['name']), 'crop') !== false ? 'crop' : 'con capucha' ?>">
                    
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
                            <?php if (strpos(strtolower($product['name']), 'premium') !== false): ?>
                                <span class="badge badge-premium">Premium</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</p>
                        
                        <div class="product-features">
                            <span class="feature-tag">
                                <i class="fas fa-thermometer-half"></i> Ideal para clima frío
                            </span>
                            <span class="feature-tag">
                                <i class="fas fa-tshirt"></i> Unisex
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
                    <h3>No se encontraron sudaderas</h3>
                    <p>Intenta ajustar los filtros para ver más resultados</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Size Guide -->
    <section class="size-guide-section">
        <div class="container">
            <h2>Guía de Tallas</h2>
            <div class="size-guide-content">
                <div class="size-table-container">
                    <table class="size-table">
                        <thead>
                            <tr>
                                <th>Talla</th>
                                <th>Pecho (cm)</th>
                                <th>Largo (cm)</th>
                                <th>Manga (cm)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>S</td>
                                <td>50-52</td>
                                <td>65-67</td>
                                <td>58-60</td>
                            </tr>
                            <tr>
                                <td>M</td>
                                <td>54-56</td>
                                <td>67-69</td>
                                <td>60-62</td>
                            </tr>
                            <tr>
                                <td>L</td>
                                <td>58-60</td>
                                <td>69-71</td>
                                <td>62-64</td>
                            </tr>
                            <tr>
                                <td>XL</td>
                                <td>62-64</td>
                                <td>71-73</td>
                                <td>64-66</td>
                            </tr>
                            <tr>
                                <td>XXL</td>
                                <td>66-68</td>
                                <td>73-75</td>
                                <td>66-68</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="size-guide-tips">
                    <h3>Consejos para elegir tu talla:</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> Mide tu sudadera favorita y compara</li>
                        <li><i class="fas fa-check"></i> Si dudas entre dos tallas, elige la mayor</li>
                        <li><i class="fas fa-check"></i> Considera el ajuste que prefieres</li>
                        <li><i class="fas fa-check"></i> Contactanos si necesitas ayuda</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/productos.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>

    
    <style>
    .material-info {
        padding: 60px 0;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
    
    .material-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .material-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .material-card:hover {
        transform: translateY(-5px);
    }
    
    .material-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #2c3e50, #34495e);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.8rem;
        color: white;
    }
    
    .product-features {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .feature-tag {
        background: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-premium {
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        color: #1a1a1a;
    }
    
    .size-guide-section {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .size-guide-section h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .size-guide-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
        align-items: start;
    }
    
    .size-table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .size-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .size-table th {
        background: var(--primary-color);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }
    
    .size-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .size-table tr:hover {
        background: #f8f9fa;
    }
    
    .size-guide-tips {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .size-guide-tips h3 {
        margin-bottom: 20px;
        color: var(--text-dark);
    }
    
    .size-guide-tips ul {
        list-style: none;
        padding: 0;
    }
    
    .size-guide-tips li {
        padding: 8px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-light);
    }
    
    .size-guide-tips i {
        color: var(--primary-color);
    }
    
    @media (max-width: 768px) {
        .size-guide-content {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .size-table {
            font-size: 0.9rem;
        }
        
        .size-table th,
        .size-table td {
            padding: 10px 8px;
        }
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