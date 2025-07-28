<?php 
session_start();
include 'includes/db.php';

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar datos
$test = $conn->query("SELECT * FROM products");
if (!$test) {
    die("Error en consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CELAJE - Ropa Única y Exclusiva</title>
    <link rel="stylesheet" href="css/celaje.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="CELAJE - Ropa única con diseños exclusivos. Playeras, sudaderas, gorras y más.">
</head>
<body data-user-logged-in="<?php echo $userLoggedIn; ?>">
    <?php include 'includes/header.php'; ?>

    <section class="hero-banner">
        <div class="banner-container">
            <div class="banner-slide active">
                <img src="img/banner1.jpg" alt="Nueva Colección CELAJE">
                <div class="banner-content">
                    <h1>Nueva Colección CELAJE</h1>
                    <p>Diseños únicos y exclusivos</p>
                    <a href="secciones/playeras.php" class="btn-cta">Ver Colección</a>
                </div>
            </div>
            <div class="banner-slide">
                <img src="img/banner2.jpg" alt="Sudaderas Premium">
                <div class="banner-content">
                    <h1>Sudaderas Premium</h1>
                    <p>Comodidad y estilo en cada prenda</p>
                    <a href="secciones/sudaderas.php" class="btn-cta">Explorar</a>
                </div>
            </div>
            <div class="banner-slide">
                <img src="img/banner3.jpg" alt="Accesorios Únicos">
                <div class="banner-content">
                    <h1>Accesorios Únicos</h1>
                    <p>Completa tu look con nuestros accesorios</p>
                    <a href="secciones/totebags.php" class="btn-cta">Descubrir</a>
                </div>
            </div>
        </div>
        <button class="banner-nav prev" onclick="changeBanner(-1)">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="banner-nav next" onclick="changeBanner(1)">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="banner-dots">
            <span class="dot active" onclick="currentBanner(1)"></span>
            <span class="dot" onclick="currentBanner(2)"></span>
            <span class="dot" onclick="currentBanner(3)"></span>
        </div>
    </section>

    <section class="featured-products">
        <div class="container">
            <div class="section-header">
                <h2>Productos Destacados</h2>
                <p>Descubre nuestra selección de productos más populares</p>
            </div>

            <div class="categories-grid">
                <div class="category-card" data-category="playeras">
                    <div class="category-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3>Playeras</h3>
                    <p>Diseños únicos y cómodos</p>
                    <div class="category-products">
                        <?php
                      $playeras = $conn->query("SELECT * FROM products WHERE category = 'Playeras' LIMIT 2");
                        while($product = $playeras->fetch_assoc()):
                        ?>
                        <div class="mini-product">
                            <img src="<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>">
                            <span>$<?= number_format($product['price'], 0) ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <a href="secciones/playeras.php" class="category-link">Ver todos</a>
                </div>

                <div class="category-card" data-category="sudaderas">
                    <div class="category-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Sudaderas</h3>
                    <p>Perfectas para cualquier ocasión</p>
                    <div class="category-products">
                        <?php
                        $sudaderas = $conn->query("SELECT * FROM products WHERE category = 'Sudaderas' LIMIT 2");
                        while($product = $sudaderas->fetch_assoc()):
                        ?>
                        <div class="mini-product">
                            <img src="<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>">
                            <span>$<?= number_format($product['price'], 0) ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <a href="secciones/sudaderas.php" class="category-link">Ver todos</a>
                </div>

                <div class="category-card" data-category="gorras">
                    <div class="category-icon">
                        <i class="fas fa-hat-cowboy"></i>
                    </div>
                    <h3>Gorras</h3>
                    <p>Estilo urbano y moderno</p>
                    <div class="category-products">
                        <?php
                        $gorras = $conn->query("SELECT * FROM products WHERE category = 'Gorras' LIMIT 2");
                        while($product = $gorras->fetch_assoc()):
                        ?>
                        <div class="mini-product">
                            <img src="<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>">
                            <span>$<?= number_format($product['price'], 0) ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <a href="secciones/gorras.php" class="category-link">Ver todos</a>
                </div>

                <div class="category-card" data-category="totebags">
                    <div class="category-icon">
                        <i class="fas fa-shopping-bag"></i> </div>
                    <h3>Totebags</h3>
                    <p>Estilo y funcionalidad</p>
                    <div class="category-products">
                        <?php
                        $totebags = $conn->query("SELECT * FROM products WHERE category = 'Totebags' LIMIT 2");
                        while($product = $totebags->fetch_assoc()):
                        ?>
                        <div class="mini-product">
                            <img src="<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>">
                            <span>$<?= number_format($product['price'], 0) ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <a href="secciones/totebags.php" class="category-link">Ver todos</a>
                </div>

                <div class="category-card" data-category="stickers">
                    <div class="category-icon">
                        <i class="fas fa-sticky-note"></i> </div>
                    <h3>Stickers</h3>
                    <p>Personaliza tus objetos</p>
                    <div class="category-products">
                        <?php
                       $stickers = $conn->query("SELECT * FROM products WHERE category = 'Stickers' LIMIT 2");
                        while($product = $stickers->fetch_assoc()):
                        ?>
                        <div class="mini-product">
                            <img src="<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>">
                            <span>$<?= number_format($product['price'], 0) ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <a href="secciones/stickers.php" class="category-link">Ver todos</a>
                </div>

            <div class="category-card" data-category="tatuajes">
                <div class="category-icon">
                 <i class="fas fa-paint-brush"></i> </div>
                        <h3>Tatuajes Temporales</h3>
                        <p>Diseños atrevidos sin compromiso</p>
                         <div class="category-products">
                        <?php
                       $tatuajes = $conn->query("SELECT * FROM products WHERE category = 'Tatuajes' LIMIT 2");
                        while($product = $tatuajes->fetch_assoc()):
                        ?>
                        <div class="mini-product">
                            <img src="<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>">
                            <span>$<?= number_format($product['price'], 0) ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <a href="secciones/tatuajes.php" class="category-link">Ver todos</a>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>¿No encuentras lo que buscas?</h2>
                <p>Explora todas nuestras categorías o ponte en contacto para productos personalizados</p>
                <div class="cta-buttons">
                    <a href="secciones/playeras.php" class="btn-primary">Ver todos los productos</a>
                    <a href="secciones/contactos.php" class="btn-secondary">Contactar</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
    <script src="js/banner.js"></script>
</body>
</html>