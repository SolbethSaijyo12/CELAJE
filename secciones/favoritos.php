<?php
// File: secciones/favoritos.php
session_start(); // Inicia la sesión
include '../includes/db.php'; // Incluye el archivo de conexión a la base de datos

// --- Configuración de reporte de errores (Solo para desarrollo, QUITAR EN PRODUCCIÓN) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Si la conexión a la base de datos falló en db.php, redirige o muestra un error crítico
if (!$conn) {
    // Podrías redirigir a una página de error o mostrar un mensaje amistoso
    // Para desarrollo, mostramos un error para depuración
    die('Error crítico: No se pudo conectar a la base de datos. Por favor, verifica db.php');
}

// --- CÁLCULO ROBUSTO DE LA URL BASE DEL PROYECTO ---
// Este cálculo intenta determinar la URL base de tu proyecto (ej. http://localhost:8080/CELAJE/)
// de forma dinámica, lo cual es vital para las rutas de CSS, JS y AJAX.

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST']; // Esto obtendrá 'localhost' o 'localhost:8080'

// Define el nombre de la carpeta raíz de tu proyecto en el servidor web.
// Es crucial que esto coincida EXACTAMENTE con el nombre de la carpeta donde tienes tu proyecto.
// Ejemplo: Si accedes a http://localhost/CELAJE/, entonces $project_folder_name = 'CELAJE';
// Si tu proyecto está directamente en la raíz de htdocs (ej. http://localhost:8080/index.php),
// entonces $project_folder_name = ''; (cadena vacía).
$project_folder_name = 'CELAJE'; // <--- AJUSTA ESTO AL NOMBRE DE TU CARPETA DE PROYECTO

$base_url_path = '';
if (!empty($project_folder_name)) {
    // Si el nombre de la carpeta del proyecto está en la URI, lo extraemos.
    // Esto maneja casos como 'http://localhost/CELAJE/secciones/favoritos.php'
    // y lo convierte en 'http://localhost/CELAJE/'
    $request_uri = $_SERVER['REQUEST_URI'];
    $pos = strpos($request_uri, '/' . $project_folder_name);
    if ($pos !== false) {
        $base_url_path = substr($request_uri, 0, $pos + strlen('/' . $project_folder_name));
    }
}
$project_root_url = $protocol . "://" . $host . $base_url_path . "/";

// Ejemplo de uso para depuración:
// echo "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Favoritos - Celaje</title>
    <link rel="stylesheet" href="<?php echo $project_root_url; ?>css/celaje.css">
    <link rel="stylesheet" href="<?php echo $project_root_url; ?>css/productos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos específicos para la página de favoritos */
        .favorite-product-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: transform var(--transition);
        }

        .favorite-product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .favorite-product-card img {
            width: 100%;
            height: 200px; /* Altura fija para las imágenes */
            object-fit: cover; /* Asegura que la imagen cubra el área sin distorsionarse */
            border-bottom: 1px solid var(--border-color);
        }

        .favorite-product-info {
            padding: 15px;
            flex-grow: 1; /* Permite que la información ocupe el espacio restante */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .favorite-product-info h3 {
            font-size: 1.25rem;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .favorite-product-info .product-description {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 10px;
            flex-grow: 1; /* Para que la descripción ocupe espacio si es larga */
        }

        .favorite-product-info .product-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--accent-color);
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: auto; /* Empuja los botones al final de la tarjeta */
        }

        .product-actions .btn-add-cart {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color var(--transition);
            flex-grow: 1; /* Permite que ocupe más espacio */
            justify-content: center;
        }

        .product-actions .btn-add-cart:hover {
            background-color: var(--primary-dark);
        }

        .product-actions .btn-wishlist {
            background: none;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            width: 40px; /* Tamaño fijo para el botón de corazón */
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition);
            color: var(--text-light); /* Color por defecto */
        }

        .product-actions .btn-wishlist.active {
            color: var(--accent-color); /* Color cuando está activo (rojo) */
            border-color: var(--accent-color);
        }

        .product-actions .btn-wishlist:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        /* Estilos para el contenedor principal y la cuadrícula de productos */
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .page-title {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent-color);
            border-radius: 2px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px 0;
            min-height: 300px; /* Para que no colapse si no hay productos */
        }

        .empty-wishlist-message {
            text-align: center;
            color: var(--text-light);
            font-size: 1.1rem;
            margin-top: 50px;
            padding: 20px;
            border: 1px dashed var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; // Incluye el header ?>

    <main class="container">
        <h1 class="page-title">Mis Productos Favoritos</h1>

        <div id="favorites-list" class="products-grid">
            </div>

        <p id="no-favorites-message" class="empty-wishlist-message" style="display: none;">
            Aún no has añadido ningún producto a tus favoritos. ¡Explora nuestros productos y encuentra algo que te encante!
        </p>
    </main>

    <?php include '../includes/footer.php'; // Incluye el footer ?>

    <script>
        window.CELAJE_BASE_URL = '<?php echo $project_root_url; ?>';
    </script>
    <script src="<?php echo $project_root_url; ?>js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const favoritesListContainer = document.getElementById('favorites-list');
            const noFavoritesMessage = document.getElementById('no-favorites-message');

            /**
             * Fetches favorite products from the server and renders them.
             */
            function fetchFavoriteProducts() {
                // Muestra un mensaje de carga o limpia la lista existente
                favoritesListContainer.innerHTML = ''; // Limpia los productos existentes
                noFavoritesMessage.style.display = 'none'; // Oculta el mensaje de no favoritos mientras carga

                fetch(window.CELAJE_BASE_URL + 'ajax/get_wishlist_items.php')
                    .then(response => {
                        if (!response.ok) {
                            // Si la respuesta no es 200 OK, intenta leer el texto para depurar
                            return response.text().then(text => {
                                throw new Error(`Network response was not ok: ${response.status} ${response.statusText} - ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Datos de favoritos recibidos:", data); // Para depuración
                        if (data.success && data.products.length > 0) {
                            data.products.forEach(product => {
                                favoritesListContainer.innerHTML += createProductCardHtml(product);
                            });
                            // Adjunta los event listeners a los nuevos botones
                            attachEventListenersToProductCards();
                            noFavoritesMessage.style.display = 'none'; // Asegura que el mensaje esté oculto
                        } else {
                            favoritesListContainer.innerHTML = ''; // Asegura que no haya productos duplicados
                            noFavoritesMessage.style.display = 'block'; // Muestra el mensaje de no favoritos
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener productos favoritos:', error);
                        favoritesListContainer.innerHTML = '<p class="empty-wishlist-message">Hubo un error al cargar tus favoritos. Intenta de nuevo más tarde.</p>';
                        noFavoritesMessage.style.display = 'none'; // Oculta el mensaje principal
                    });
            }

            /**
             * Attaches event listeners to the wishlist and add-to-cart buttons.
             * This function should be called after products are rendered.
             */
            function attachEventListenersToProductCards() {
                // Delegación de eventos para los botones de favoritos
                favoritesListContainer.removeEventListener('click', handleWishlistButtonClick); // Evita duplicados
                favoritesListContainer.addEventListener('click', handleWishlistButtonClick);

                // Delegación de eventos para los botones de añadir al carrito
                // Puedes agregar una función similar para el carrito si es necesario
                // favoritesListContainer.removeEventListener('click', handleAddToCartButtonClick);
                // favoritesListContainer.addEventListener('click', handleAddToCartButtonClick);
            }

            function handleWishlistButtonClick(event) {
                const button = event.target.closest('.btn-wishlist');
                if (button) {
                    event.preventDefault(); // Previene la acción por defecto si la hay
                    const productId = button.dataset.productId;
                    if (productId) {
                        // Llama a la función global para gestionar favoritos definida en main.js
                        if (typeof toggleWishlist === 'function') {
                            toggleWishlist(productId, button); // Pasa el botón para actualizar su estado
                        } else {
                            console.error('toggleWishlist function not found. Is main.js loaded correctly?');
                        }
                    }
                }
            }

            /**
             * Generates the HTML for a single product card.
             * @param {object} product - The product object from the API.
             * @returns {string} The HTML string for the product card.
             */
            function createProductCardHtml(product) {
                // Formatea el precio a moneda local (MXN en este caso, asumiendo configuración local)
                const productPrice = parseFloat(product.price).toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                });

                // La imagen siempre debe apuntar a la ruta base para que sea correcta.
                // Asumiendo que las imágenes se guardan en 'uploads/productos/'
                const imageUrl = window.CELAJE_BASE_URL + 'uploads/productos/' + product.image_path;

                // En la página de favoritos, el botón siempre debe estar 'activo'
                // porque por definición, todos los productos aquí SON favoritos.
                // Aunque el `toggleWishlist` en `main.js` lo maneje, es bueno que el HTML inicial lo refleje.
                const isFavorite = true; // Siempre true en esta página

                return `
                    <div class="favorite-product-card product-card">
                        <img src="${imageUrl}" alt="${product.name}">
                        <div class="favorite-product-info">
                            <h3 class="product-name">${product.name}</h3>
                            <p class="product-description">${product.description}</p>
                            <span class="product-price">${productPrice}</span>
                            <div class="product-actions">
                                <button class="btn-add-cart" data-product-id="${product.id}">
                                    <i class="fas fa-shopping-cart"></i> Agregar al Carrito
                                </button>
                                <button class="btn-wishlist ${isFavorite ? 'active' : ''}" data-product-id="${product.id}" aria-label="Eliminar de favoritos">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Llama a la función para cargar los productos favoritos cuando la página se carga
            fetchFavoriteProducts();

            // Escucha el evento personalizado 'wishlistUpdated' que puede ser disparado por main.js
            // cuando un producto se añade o elimina de favoritos desde cualquier parte del sitio.
            // Esto permite que la lista de favoritos se actualice dinámicamente sin recargar toda la página.
            document.addEventListener('wishlistUpdated', function(event) {
                console.log('Evento wishlistUpdated recibido. Recargando favoritos...');
                fetchFavoriteProducts(); // Recarga la lista de productos favoritos
            });
        });
    </script>
</body>
</html>