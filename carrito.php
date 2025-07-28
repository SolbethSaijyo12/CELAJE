<?php
// carrito.php
session_start();
include 'includes/db.php'; // Asegúrate que esta ruta sea correcta para tu estructura de carpetas

// Función para obtener los ítems del carrito, ya sea de sesión o DB
function getCartItems($conn) {
    $cart_items = [];
    if (isset($_SESSION['user_id'])) {
        // Usuario logueado: Obtener del base de datos
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT c.id AS item_id, c.product_id, p.name, p.price, c.quantity, p.image_path, p.category,
                                    c.color, c.material, c.size, c.closure, c.lamination, c.sheet, c.sheet_cut,
                                    c.paper_type, c.form_type, c.vinyl_type, c.margin, c.image_position,
                                    c.artist_image_1, c.artist_image_2, c.artist_image_3,
                                    c.dedication, c.custom_card_text, c.custom_card_image,
                                    c.is_gift, c.gift_dedication, c.gift_bag, c.gift_wrap, c.gift_card
                                FROM cart c JOIN products p ON c.product_id = p.id
                                WHERE c.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
        }
        $stmt->close();
    } else {
        // Usuario no logueado: Obtener de la sesión y obtener detalles del producto de la DB
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            $session_cart = array_values($_SESSION['cart']); // Reindexar si hubo eliminaciones

            // Obtener product_ids para una sola consulta a la base de datos
            $product_ids = [];
            foreach ($session_cart as $s_item) {
                if (isset($s_item['product_id'])) {
                    $product_ids[] = $s_item['product_id'];
                }
            }

            if (!empty($product_ids)) {
                $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
                $stmt = $conn->prepare("SELECT id, name, price, image_path, category,
                                            available_colors, available_materials, available_sizes, available_closures,
                                            available_paper_types, available_vinyl_types, available_image_positions
                                        FROM products WHERE id IN ($placeholders)");
                $types = str_repeat('i', count($product_ids));
                $stmt->bind_param($types, ...$product_ids);
                $stmt->execute();
                $result = $stmt->get_result();
                $products_data = [];
                while ($row = $result->fetch_assoc()) {
                    $products_data[$row['id']] = $row;
                }
                $stmt->close();

                foreach ($session_cart as $s_item) {
                    if (isset($s_item['product_id']) && isset($products_data[$s_item['product_id']])) {
                        $product = $products_data[$s_item['product_id']];
                        // Merge product details with session item details
                        $cart_items[] = array_merge($product, $s_item);
                    }
                }
            }
        }
    }
    return $cart_items;
}

$cart_items = getCartItems($conn);
$total_price = 0;
foreach ($cart_items as $item) {
    // Asegurarse de que price y quantity son números para evitar errores de cálculo
    $item_price = isset($item['price']) ? (float)$item['price'] : 0;
    $item_quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
    $total_price += $item_price * $item_quantity;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - CELAJE</title>
    <link rel="stylesheet" href="css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos específicos para carrito.php */
        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .cart-header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 40px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            gap: 20px;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: var(--border-radius);
        }

        .item-details {
            flex-grow: 1;
        }

        .item-details h3 {
            margin: 0 0 5px 0;
            color: var(--text-dark);
        }

        .item-details p {
            margin: 0;
            color: var(--text-light);
            font-size: 0.9em;
        }

        .item-price {
            font-weight: bold;
            color: var(--primary-color);
            margin-top: 5px;
        }

        .item-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .quantity-control button {
            background: var(--secondary-color);
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            font-weight: bold;
            color: var(--text-dark);
            transition: var(--transition);
        }

        .quantity-control button:hover {
            background: var(--primary-light);
            color: var(--white);
        }

        .quantity-control span {
            padding: 0 15px;
            background: var(--white);
            color: var(--text-dark);
        }

        .remove-item-btn {
            background: #dc3545; /* Rojo */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .remove-item-btn:hover {
            background: #c82333; /* Rojo más oscuro */
        }

        /* Estilo para el nuevo botón de edición */
        .edit-item-btn {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 8px 12px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 10px; /* Ajusta según tu diseño */
        }

        .edit-item-btn:hover {
            background: var(--primary-dark);
        }

        .cart-summary {
            background: var(--secondary-color);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: right;
            margin-top: 20px;
        }

        .cart-summary p {
            font-size: 1.2em;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        .cart-summary .total-price {
            font-size: 1.5em;
            font-weight: bold;
            color: var(--accent-color);
        }

        .cart-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .clear-cart-btn {
            background: var(--text-light);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .clear-cart-btn:hover {
            background: #5a6268;
        }

        .checkout-btn {
            background: var(--accent-color);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none; /* Para el caso de link */
        }

        .checkout-btn:hover {
            background: #e05e2d;
        }

        /* Mensaje de alerta para no logueado */
        .login-prompt {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            border-radius: var(--border-radius);
            color: #856404;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
        }
        .login-prompt .fas {
            color: #ffc107;
        }

        /* --- ESTILOS DEL MODAL DE PERSONALIZACIÓN --- */
        /* La clave para que no aparezca automáticamente es display: none; */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto; /* Para centrar si display es block */
            padding: 30px;
            border: 1px solid #888;
            width: 90%;
            max-width: 600px; /* Ajustado para más campos */
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
            max-height: 90vh; /* Para permitir scroll si es muy largo */
            overflow-y: auto;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-content h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }

        .modal-content .form-group {
            margin-bottom: 15px;
        }

        .modal-content label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--text-dark);
        }

        .modal-content select,
        .modal-content input[type="number"],
        .modal-content input[type="text"],
        .modal-content input[type="file"],
        .modal-content textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1em;
            margin-bottom: 10px;
            box-sizing: border-box; /* Para incluir padding en el width */
        }

        .modal-content input[type="checkbox"] {
            margin-right: 10px;
            width: auto; /* Anular el 100% */
        }

        .modal-content .checkbox-group label {
            display: inline-block;
            margin-bottom: 0;
            font-weight: normal;
        }

        .modal-content button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .modal-content button[type="submit"]:hover {
            background-color: #e05e2d;
        }

        /* Estilos para ocultar/mostrar secciones del formulario */
        .product-specific-fields {
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="cart-container">
        <header class="cart-header">
            <h1>Tu Carrito de Compras</h1>
            <p>Revisa y gestiona los productos que tienes en tu carrito antes de finalizar la compra.</p>
        </header>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart-message">
                <p>Tu carrito está vacío. ¡Explora nuestros productos y añade algo!</p>
                <a href="index.php" class="checkout-btn" style="background-color: var(--primary-color);">Ver Productos</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item):
                    // Ensure item_id exists, fallback to a unique key for session items if needed (though product_id + options is better)
                    $item_id = $item['item_id'] ?? ($item['product_id'] . uniqid()); // Unique ID for session items if no 'item_id' from DB
                ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="item-details">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p>Categoría: <?= htmlspecialchars($item['category']) ?></p>
                            <?php if (!empty($item['color'])): ?><p>Color: <?= htmlspecialchars($item['color']) ?></p><?php endif; ?>
                            <?php if (!empty($item['material'])): ?><p>Material: <?= htmlspecialchars($item['material']) ?></p><?php endif; ?>
                            <?php if (!empty($item['size'])): ?><p>Talla: <?= htmlspecialchars($item['size']) ?></p><?php endif; ?>
                            <?php if (!empty($item['closure'])): ?><p>Cierre: <?= htmlspecialchars($item['closure']) ?></p><?php endif; ?>
                            <?php if (isset($item['lamination']) && $item['lamination'] !== null): ?><p>Laminado: <?= $item['lamination'] ? 'Sí' : 'No' ?></p><?php endif; ?>
                            <?php if (isset($item['sheet']) && $item['sheet'] !== null): ?><p>Planilla: <?= $item['sheet'] ? 'Sí' : 'No' ?><?= (isset($item['sheet']) && $item['sheet'] && isset($item['sheet_cut']) && $item['sheet_cut'] !== null) ? ' (' . ($item['sheet_cut'] ? 'Con Corte' : 'Sin Recorte') . ')' : '' ?></p><?php endif; ?>
                            <?php if (!empty($item['paper_type'])): ?><p>Tipo de papel: <?= htmlspecialchars($item['paper_type']) ?><?php if (!empty($item['form_type'])) echo ' (Forma: ' . htmlspecialchars($item['form_type']) . ')'; ?></p><?php endif; ?>
                            <?php if (!empty($item['vinyl_type'])): ?><p>Vinil: <?= htmlspecialchars($item['vinyl_type']) ?></p><?php endif; ?>
                            <?php if (isset($item['margin']) && $item['margin'] !== null): ?><p>Margen: <?= $item['margin'] ? 'Sí' : 'No' ?></p><?php endif; ?>
                            <?php if (!empty($item['image_position'])): ?><p>Posición de imagen: <?= htmlspecialchars($item['image_position']) ?></p><?php endif; ?>
                            <?php if (!empty($item['dedication'])): ?><p>Dedicatoria: "<?= htmlspecialchars($item['dedication']) ?>"</p><?php endif; ?>
                            <?php if (!empty($item['custom_card_text'])): ?><p>Carta personalizada: "<?= htmlspecialchars($item['custom_card_text']) ?>"</p><?php endif; ?>
                            <?php
                                // Display image paths if they exist
                                if (!empty($item['artist_image_1'])): ?><p>Imagen 1 del artista/usuario: <a href="<?= htmlspecialchars($item['artist_image_1']) ?>" target="_blank">Ver</a></p><?php endif; ?>
                            <?php if (!empty($item['artist_image_2'])): ?><p>Imagen 2 del artista/usuario: <a href="<?= htmlspecialchars($item['artist_image_2']) ?>" target="_blank">Ver</a></p><?php endif; ?>
                            <?php if (!empty($item['artist_image_3'])): ?><p>Imagen 3 del artista/usuario: <a href="<?= htmlspecialchars($item['artist_image_3']) ?>" target="_blank">Ver</a></p><?php endif; ?>
                            <?php if (!empty($item['custom_card_image'])): ?><p>Imagen de carta personalizada: <a href="<?= htmlspecialchars($item['custom_card_image']) ?>" target="_blank">Ver</a></p><?php endif; ?>

                            <?php if (isset($item['is_gift']) && $item['is_gift']): ?>
                                <p style="font-weight: bold; color: green;">Es para regalo <i class="fas fa-gift"></i></p>
                                <?php if (!empty($item['gift_dedication'])): ?><p style="font-size: 0.85em;">Mensaje de regalo: "<?= htmlspecialchars($item['gift_dedication']) ?>"</p><?php endif; ?>
                                <?php if (isset($item['gift_bag']) && $item['gift_bag']): ?><p style="font-size: 0.85em;">Con bolsa de regalo <i class="fas fa-shopping-bag"></i></p><?php endif; ?>
                                <?php if (isset($item['gift_wrap']) && $item['gift_wrap']): ?><p style="font-size: 0.85em;">Con papel de regalo <i class="fas fa-box"></i></p><?php endif; ?>
                                <?php if (isset($item['gift_card']) && $item['gift_card']): ?><p style="font-size: 0.85em;">Con tarjeta de regalo <i class="fas fa-id-card"></i></p><?php endif; ?>
                            <?php endif; ?>
                            <p class="item-price">$<?= number_format($item['price'], 2) ?></p>
                        </div>
                        <div class="item-actions">
                            <div class="quantity-control">
                                <button onclick="updateQuantity('<?= $item_id ?>', <?= $item['quantity'] - 1 ?>)">-</button>
                                <span><?= htmlspecialchars($item['quantity']) ?></span>
                                <button onclick="updateQuantity('<?= $item_id ?>', <?= $item['quantity'] + 1 ?>)">+</button>
                            </div>
                            <button class="edit-item-btn"
                                data-item-id="<?= htmlspecialchars($item_id) ?>"
                                data-product-id="<?= htmlspecialchars($item['product_id']) ?>"
                                data-product-name="<?= htmlspecialchars($item['name']) ?>"
                                data-product-category="<?= htmlspecialchars($item['category']) ?>"
                                data-current-quantity="<?= htmlspecialchars($item['quantity']) ?>"
                                data-current-color="<?= htmlspecialchars($item['color'] ?? '') ?>"
                                data-current-material="<?= htmlspecialchars($item['material'] ?? '') ?>"
                                data-current-size="<?= htmlspecialchars($item['size'] ?? '') ?>"
                                data-current-closure="<?= htmlspecialchars($item['closure'] ?? '') ?>"
                                data-current-lamination="<?= (int)($item['lamination'] ?? 0) ?>"
                                data-current-sheet="<?= (int)($item['sheet'] ?? 0) ?>"
                                data-current-sheet-cut="<?= (int)($item['sheet_cut'] ?? 0) ?>"
                                data-current-paper-type="<?= htmlspecialchars($item['paper_type'] ?? '') ?>"
                                data-current-form-type="<?= htmlspecialchars($item['form_type'] ?? '') ?>"
                                data-current-vinyl-type="<?= htmlspecialchars($item['vinyl_type'] ?? '') ?>"
                                data-current-margin="<?= (int)($item['margin'] ?? 0) ?>"
                                data-current-image-position="<?= htmlspecialchars($item['image_position'] ?? '') ?>"
                                data-current-artist-image-1="<?= htmlspecialchars($item['artist_image_1'] ?? '') ?>"
                                data-current-artist-image-2="<?= htmlspecialchars($item['artist_image_2'] ?? '') ?>"
                                data-current-artist-image-3="<?= htmlspecialchars($item['artist_image_3'] ?? '') ?>"
                                data-current-dedication="<?= htmlspecialchars($item['dedication'] ?? '') ?>"
                                data-current-custom-card-text="<?= htmlspecialchars($item['custom_card_text'] ?? '') ?>"
                                data-current-custom-card-image="<?= htmlspecialchars($item['custom_card_image'] ?? '') ?>"
                                data-current-is-gift="<?= (int)($item['is_gift'] ?? 0) ?>"
                                data-current-gift-dedication="<?= htmlspecialchars($item['gift_dedication'] ?? '') ?>"
                                data-current-gift-bag="<?= (int)($item['gift_bag'] ?? 0) ?>"
                                data-current-gift-wrap="<?= (int)($item['gift_wrap'] ?? 0) ?>"
                                data-current-gift-card="<?= (int)($item['gift_card'] ?? 0) ?>"
                                <?php // Add available options to data attributes for simpler JS access if product data is merged
                                echo 'data-available-colors="' . htmlspecialchars($item['available_colors'] ?? '') . '" ';
                                echo 'data-available-materials="' . htmlspecialchars($item['available_materials'] ?? '') . '" ';
                                echo 'data-available-sizes="' . htmlspecialchars($item['available_sizes'] ?? '') . '" ';
                                echo 'data-available-closures="' . htmlspecialchars($item['available_closures'] ?? '') . '" ';
                                echo 'data-available-paper-types="' . htmlspecialchars($item['available_paper_types'] ?? '') . '" ';
                                echo 'data-available-vinyl-types="' . htmlspecialchars($item['available_vinyl_types'] ?? '') . '" ';
                                echo 'data-available-image-positions="' . htmlspecialchars($item['available_image_positions'] ?? '') . '" ';
                                ?>
                                onclick="openEditModal(this)">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="remove-item-btn" onclick="removeItem('<?= $item_id ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <p>Total de productos: <span class="total-price">$<?= number_format($total_price, 2) ?></span></p>
                <div class="cart-actions">
                    <button class="clear-cart-btn" onclick="clearCart()">Vaciar Carrito</button>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="checkout.php" class="checkout-btn">
                            <i class="fas fa-money-check-alt"></i> Proceder al Pago
                        </a>
                    <?php else: ?>
                        <div class="login-prompt">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Para proceder al pago, por favor <a href="login.php">inicia sesión</a> o <a href="register.php">regístrate</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <div id="editItemModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditModal()">&times;</span>
            <h2>Editar Artículo</h2>
            <form id="editItemForm" method="POST" action="update_cart_item.php" enctype="multipart/form-data">
                <input type="hidden" name="item_id" id="modal_item_id">
                <input type="hidden" name="product_id" id="modal_product_id">
                <input type="hidden" name="current_quantity" id="modal_current_quantity_hidden">

                <div class="form-group">
                    <label for="modal_product_name">Producto:</label>
                    <input type="text" id="modal_product_name" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="modal_quantity">Cantidad:</label>
                    <input type="number" id="modal_quantity" name="quantity" min="1" value="1" class="form-control" required>
                </div>

                <div id="productSpecificFields" class="product-specific-fields">
                    <div class="form-group" id="form_group_color" style="display:none;">
                        <label for="modal_color">Color:</label>
                        <select id="modal_color" name="color" class="form-control"></select>
                    </div>
                    <div class="form-group" id="form_group_material" style="display:none;">
                        <label for="modal_material">Material:</label>
                        <select id="modal_material" name="material" class="form-control"></select>
                    </div>
                    <div class="form-group" id="form_group_size" style="display:none;">
                        <label for="modal_size">Talla:</label>
                        <select id="modal_size" name="size" class="form-control"></select>
                    </div>
                    <div class="form-group" id="form_group_closure" style="display:none;">
                        <label for="modal_closure">Cierre:</label>
                        <select id="modal_closure" name="closure" class="form-control"></select>
                    </div>
                    <div class="form-group checkbox-group" id="form_group_lamination" style="display:none;">
                        <input type="checkbox" id="modal_lamination" name="lamination">
                        <label for="modal_lamination">Laminado</label>
                    </div>
                    <div class="form-group checkbox-group" id="form_group_sheet" style="display:none;">
                        <input type="checkbox" id="modal_sheet" name="sheet">
                        <label for="modal_sheet">¿En planilla?</label>
                        <div id="sheet_cut_options" style="display:none; margin-top: 10px; margin-left: 20px;">
                            <input type="radio" id="sheet_cut_yes" name="sheet_cut" value="1">
                            <label for="sheet_cut_yes">Con Corte</label>
                            <input type="radio" id="sheet_cut_no" name="sheet_cut" value="0">
                            <label for="sheet_cut_no">Sin Recorte</label>
                        </div>
                    </div>
                    <div class="form-group" id="form_group_paper_type" style="display:none;">
                        <label for="modal_paper_type">Tipo de papel:</label>
                        <select id="modal_paper_type" name="paper_type" class="form-control"></select>
                        <div id="form_type_options" style="display:none; margin-top: 10px;">
                            <label for="modal_form_type">Forma:</label>
                            <input type="text" id="modal_form_type" name="form_type" class="form-control">
                        </div>
                    </div>
                    <div class="form-group" id="form_group_vinyl_type" style="display:none;">
                        <label for="modal_vinyl_type">Tipo de Vinil:</label>
                        <select id="modal_vinyl_type" name="vinyl_type" class="form-control"></select>
                    </div>
                    <div class="form-group checkbox-group" id="form_group_margin" style="display:none;">
                        <input type="checkbox" id="modal_margin" name="margin">
                        <label for="modal_margin">Margen</label>
                    </div>
                    <div class="form-group" id="form_group_image_position" style="display:none;">
                        <label for="modal_image_position">Posición de la Imagen:</label>
                        <select id="modal_image_position" name="image_position" class="form-control"></select>
                    </div>
                    <div class="form-group" id="form_group_artist_image_1" style="display:none;">
                        <label for="modal_artist_image_1">Imagen 1 (Artista/Usuario):</label>
                        <input type="file" id="modal_artist_image_1" name="artist_image_1" class="form-control-file">
                        <p id="current_artist_image_1_link" style="margin-top: 5px;"><small><a href="#" target="_blank">Ver imagen actual</a></small></p>
                    </div>
                    <div class="form-group" id="form_group_artist_image_2" style="display:none;">
                        <label for="modal_artist_image_2">Imagen 2 (Artista/Usuario):</label>
                        <input type="file" id="modal_artist_image_2" name="artist_image_2" class="form-control-file">
                        <p id="current_artist_image_2_link" style="margin-top: 5px;"><small><a href="#" target="_blank">Ver imagen actual</a></small></p>
                    </div>
                    <div class="form-group" id="form_group_artist_image_3" style="display:none;">
                        <label for="modal_artist_image_3">Imagen 3 (Artista/Usuario):</label>
                        <input type="file" id="modal_artist_image_3" name="artist_image_3" class="form-control-file">
                        <p id="current_artist_image_3_link" style="margin-top: 5px;"><small><a href="#" target="_blank">Ver imagen actual</a></small></p>
                    </div>
                    <div class="form-group" id="form_group_dedication" style="display:none;">
                        <label for="modal_dedication">Dedicatoria:</label>
                        <textarea id="modal_dedication" name="dedication" rows="3" class="form-control" maxlength="255"></textarea>
                        <small class="form-text text-muted">Máximo 255 caracteres.</small>
                    </div>
                    <div class="form-group" id="form_group_custom_card_text" style="display:none;">
                        <label for="modal_custom_card_text">Texto de tarjeta personalizada:</label>
                        <textarea id="modal_custom_card_text" name="custom_card_text" rows="3" class="form-control" maxlength="500"></textarea>
                        <small class="form-text text-muted">Máximo 500 caracteres.</small>
                    </div>
                    <div class="form-group" id="form_group_custom_card_image" style="display:none;">
                        <label for="modal_custom_card_image">Imagen para tarjeta personalizada:</label>
                        <input type="file" id="modal_custom_card_image" name="custom_card_image" class="form-control-file">
                        <p id="current_custom_card_image_link" style="margin-top: 5px;"><small><a href="#" target="_blank">Ver imagen actual</a></small></p>
                    </div>

                    <div class="form-group checkbox-group" id="form_group_is_gift" style="display:none;">
                        <input type="checkbox" id="modal_is_gift" name="is_gift">
                        <label for="modal_is_gift">¿Es para regalo?</label>
                    </div>
                    <div id="gift_options" style="display:none; border-top: 1px dashed #ccc; padding-top: 15px; margin-top: 15px;">
                        <div class="form-group" id="form_group_gift_dedication">
                            <label for="modal_gift_dedication">Mensaje de Dedicatoria para Regalo (opcional):</label>
                            <textarea id="modal_gift_dedication" name="gift_dedication" rows="2" class="form-control" maxlength="255"></textarea>
                        </div>
                        <div class="form-group checkbox-group" id="form_group_gift_bag">
                            <input type="checkbox" id="modal_gift_bag" name="gift_bag">
                            <label for="modal_gift_bag">Añadir bolsa de regalo</label>
                        </div>
                        <div class="form-group checkbox-group" id="form_group_gift_wrap">
                            <input type="checkbox" id="modal_gift_wrap" name="gift_wrap">
                            <label for="modal_gift_wrap">Añadir papel de regalo</label>
                        </div>
                        <div class="form-group checkbox-group" id="form_group_gift_card">
                            <input type="checkbox" id="modal_gift_card" name="gift_card">
                            <label for="modal_gift_card">Añadir tarjeta de regalo</label>
                        </div>
                    </div>
                </div>

                <button type="submit">Actualizar Carrito</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) {
                if (confirm('¿Estás seguro de que quieres eliminar este artículo del carrito?')) {
                    removeItem(itemId);
                }
                return;
            }

            $.ajax({
                url: 'update_cart_item.php',
                type: 'POST',
                data: {
                    item_id: itemId,
                    quantity: newQuantity,
                    action: 'update_quantity'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload(); // Recargar la página para ver los cambios
                    } else {
                        alert('Error al actualizar la cantidad: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error en la comunicación con el servidor: ' + error);
                }
            });
        }

        function removeItem(itemId) {
            if (confirm('¿Estás seguro de que quieres eliminar este artículo del carrito?')) {
                $.ajax({
                    url: 'remove_cart_item.php',
                    type: 'POST',
                    data: {
                        item_id: itemId
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload(); // Recargar la página para ver los cambios
                        } else {
                            alert('Error al eliminar el artículo: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la comunicación con el servidor: ' + error);
                    }
                });
            }
        }

        function clearCart() {
            if (confirm('¿Estás seguro de que quieres vaciar todo el carrito?')) {
                $.ajax({
                    url: 'clear_cart.php',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            location.reload(); // Recargar la página para ver el carrito vacío
                        } else {
                            alert('Error al vaciar el carrito: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la comunicación con el servidor: ' + error);
                    }
                });
            }
        }

        // --- Funcionalidad del Modal de Edición ---
        const editItemModal = document.getElementById('editItemModal');
        const editItemForm = document.getElementById('editItemForm');

        function openEditModal(button) {
            const itemId = button.dataset.itemId;
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            const productCategory = button.dataset.productCategory;
            const currentQuantity = button.dataset.currentQuantity;

            // Rellenar campos comunes del modal
            document.getElementById('modal_item_id').value = itemId;
            document.getElementById('modal_product_id').value = productId;
            document.getElementById('modal_product_name').value = productName;
            document.getElementById('modal_quantity').value = currentQuantity;
            document.getElementById('modal_current_quantity_hidden').value = currentQuantity; // Para pasar la cantidad original

            // Ocultar todos los campos específicos primero
            document.querySelectorAll('.product-specific-fields .form-group').forEach(group => {
                group.style.display = 'none';
                // Restablecer campos a valores por defecto o vacíos
                const input = group.querySelector('select, input[type="number"], input[type="text"], textarea');
                if (input) input.value = '';
                const checkbox = group.querySelector('input[type="checkbox"]');
                if (checkbox) checkbox.checked = false;
                const radios = group.querySelectorAll('input[type="radio"]');
                radios.forEach(radio => radio.checked = false);
                const fileInput = group.querySelector('input[type="file"]');
                if (fileInput) fileInput.value = ''; // Clear file input
                const currentImageLink = group.querySelector('p[id^="current_"][id$="_link"] a');
                if (currentImageLink) {
                    currentImageLink.href = '#';
                    currentImageLink.parentElement.style.display = 'none';
                }
            });
            document.getElementById('gift_options').style.display = 'none';
            document.getElementById('sheet_cut_options').style.display = 'none';
            document.getElementById('form_type_options').style.display = 'none';

            // Mostrar y rellenar campos según la categoría del producto y los datos actuales
            // Helper para rellenar select
            function populateSelect(selectId, availableOptions, currentValue) {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Selecciona...</option>'; // Opción por defecto
                if (availableOptions) {
                    const optionsArray = availableOptions.split(',');
                    optionsArray.forEach(option => {
                        const opt = document.createElement('option');
                        opt.value = option.trim();
                        opt.textContent = option.trim();
                        if (opt.value === currentValue) {
                            opt.selected = true;
                        }
                        select.appendChild(opt);
                    });
                }
                document.getElementById(selectId).closest('.form-group').style.display = 'block';
            }

            // Helper para mostrar enlace de imagen
            function showImageLink(linkId, currentPath) {
                const linkElement = document.getElementById(linkId);
                if (currentPath) {
                    linkElement.href = currentPath;
                    linkElement.parentElement.style.display = 'block';
                } else {
                    linkElement.parentElement.style.display = 'none';
                }
            }


            // Cargar y mostrar datos actuales
            const fieldsToPopulate = {
                'color': 'modal_color',
                'material': 'modal_material',
                'size': 'modal_size',
                'closure': 'modal_closure',
                'paper_type': 'modal_paper_type',
                'vinyl_type': 'modal_vinyl_type',
                'image_position': 'modal_image_position'
            };

            for (const key in fieldsToPopulate) {
                const modalId = fieldsToPopulate[key];
                const dataAttribute = `data-current-${key.replace(/_/g, '-')}`;
                const availableAttribute = `data-available-${key.replace(/_/g, '-')}`;

                const currentValue = button.dataset[dataAttribute.replace('data-', '')] || '';
                const availableOptions = button.dataset[availableAttribute.replace('data-', '')] || '';

                if (availableOptions) {
                    populateSelect(modalId, availableOptions, currentValue);
                } else if (currentValue) { // Si hay un valor actual pero no opciones disponibles (e.g., campo libre)
                    const input = document.getElementById(modalId);
                    if (input) {
                        input.value = currentValue;
                        input.closest('.form-group').style.display = 'block';
                    }
                }
            }

            // Manejo de checkboxes
            const checkboxFields = {
                'lamination': 'modal_lamination',
                'margin': 'modal_margin',
                'is_gift': 'modal_is_gift',
                'gift_bag': 'modal_gift_bag',
                'gift_wrap': 'modal_gift_wrap',
                'gift_card': 'modal_gift_card',
                'sheet': 'modal_sheet'
            };

            for (const key in checkboxFields) {
                const modalId = checkboxFields[key];
                const dataAttribute = `data-current-${key.replace(/_/g, '-')}`;
                const isChecked = parseInt(button.dataset[dataAttribute.replace('data-', '')] || 0);
                const checkbox = document.getElementById(modalId);
                if (checkbox) {
                    checkbox.checked = isChecked;
                    checkbox.closest('.form-group').style.display = 'block';
                }
            }

            // Manejo especial para sheet_cut (radio buttons dentro de sheet)
            const currentSheetCut = parseInt(button.dataset.currentSheetCut || 0);
            if (document.getElementById('modal_sheet').checked) {
                document.getElementById('sheet_cut_options').style.display = 'block';
                if (currentSheetCut === 1) {
                    document.getElementById('sheet_cut_yes').checked = true;
                } else {
                    document.getElementById('sheet_cut_no').checked = true;
                }
            }
            // Listener para sheet checkbox
            document.getElementById('modal_sheet').onchange = function() {
                if (this.checked) {
                    document.getElementById('sheet_cut_options').style.display = 'block';
                    document.getElementById('sheet_cut_yes').checked = true; // Default to con corte
                } else {
                    document.getElementById('sheet_cut_options').style.display = 'none';
                    document.getElementById('sheet_cut_yes').checked = false;
                    document.getElementById('sheet_cut_no').checked = false;
                }
            };

            // Manejo especial para form_type (input dentro de paper_type)
            const currentFormType = button.dataset.currentFormType || '';
            if (button.dataset.availablePaperTypes && button.dataset.availablePaperTypes.includes('Personalizado')) { // Asumiendo que "Personalizado" es lo que activa el campo de forma
                 document.getElementById('form_group_paper_type').style.display = 'block';
                // Si el tipo de papel actual es 'Personalizado' o si el campo de forma tiene contenido
                if (button.dataset.currentPaperType === 'Personalizado' || currentFormType) {
                    document.getElementById('form_type_options').style.display = 'block';
                    document.getElementById('modal_form_type').value = currentFormType;
                }
            }
            // Listener para paper_type select para mostrar/ocultar form_type
            document.getElementById('modal_paper_type').onchange = function() {
                if (this.value === 'Personalizado') { // O el valor que indique que se necesita un campo de forma
                    document.getElementById('form_type_options').style.display = 'block';
                } else {
                    document.getElementById('form_type_options').style.display = 'none';
                    document.getElementById('modal_form_type').value = ''; // Limpiar el campo si se oculta
                }
            };


            // Manejo de textareas
            const textareaFields = {
                'dedication': 'modal_dedication',
                'custom_card_text': 'modal_custom_card_text',
                'gift_dedication': 'modal_gift_dedication'
            };

            for (const key in textareaFields) {
                const modalId = textareaFields[key];
                const dataAttribute = `data-current-${key.replace(/_/g, '-')}`;
                const currentValue = button.dataset[dataAttribute.replace('data-', '')] || '';
                const textarea = document.getElementById(modalId);
                if (textarea) {
                    textarea.value = currentValue;
                    textarea.closest('.form-group').style.display = 'block';
                }
            }

            // Manejo de imágenes de artista/usuario y custom card image
            const imageFields = {
                'artist_image_1': 'modal_artist_image_1',
                'artist_image_2': 'modal_artist_image_2',
                'artist_image_3': 'modal_artist_image_3',
                'custom_card_image': 'modal_custom_card_image'
            };

            for (const key in imageFields) {
                const modalId = imageFields[key];
                const dataAttribute = `data-current-${key.replace(/_/g, '-')}`;
                const currentPath = button.dataset[dataAttribute.replace('data-', '')] || '';
                const fileInput = document.getElementById(modalId);
                if (fileInput) {
                    fileInput.closest('.form-group').style.display = 'block';
                    showImageLink(`current_${key}_link`, currentPath);
                }
            }


            // Mostrar/Ocultar opciones de regalo basadas en is_gift
            const isGiftCheckbox = document.getElementById('modal_is_gift');
            const giftOptionsDiv = document.getElementById('gift_options');
            if (isGiftCheckbox && giftOptionsDiv) {
                if (isGiftCheckbox.checked) {
                    giftOptionsDiv.style.display = 'block';
                }
                isGiftCheckbox.onchange = function() {
                    if (this.checked) {
                        giftOptionsDiv.style.display = 'block';
                    } else {
                        giftOptionsDiv.style.display = 'none';
                        // Opcional: limpiar campos de regalo si se desactiva
                        document.getElementById('modal_gift_dedication').value = '';
                        document.getElementById('modal_gift_bag').checked = false;
                        document.getElementById('modal_gift_wrap').checked = false;
                        document.getElementById('modal_gift_card').checked = false;
                    }
                };
            }

            editItemModal.style.display = 'flex'; // Usar flex para centrar
        }

        function closeEditModal() {
            editItemModal.style.display = 'none';
            editItemForm.reset(); // Limpiar el formulario al cerrar
        }

        // Cerrar el modal haciendo clic fuera de él
        window.onclick = function(event) {
            if (event.target == editItemModal) {
                closeEditModal();
            }
        }

        // Manejo del envío del formulario de edición mediante AJAX
        $('#editItemForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            // Asegurarse de que el input de cantidad oculta es siempre el valor original,
            // ya que el valor en el campo visible puede ser modificado por el usuario.
            // La lógica de `update_cart_item.php` debe manejar la diferencia para calcular el cambio de cantidad.
            formData.append('original_quantity', document.getElementById('modal_current_quantity_hidden').value);


            $.ajax({
                url: 'update_cart_item.php', // El mismo archivo que usas para +- cantidad
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Artículo del carrito actualizado exitosamente.');
                        location.reload(); // Recargar la página para ver los cambios
                    } else {
                        alert('Error al actualizar el artículo: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error en la comunicación con el servidor: ' + xhr.responseText);
                }
            });
        });
    </script>
</body>
</html>