<?php
session_start();
require_once 'includes/db.php'; // Your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$user_addresses = [];
$cart_items = [];
$total_price = 0;
$payment_success = false;
$payment_method_selected = false;
$transaction_id = null; // To store a unique transaction ID
$order_id = null; // To store the new order ID

// Fetch user addresses
if ($conn) {
    $stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $user_addresses[] = $row;
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare address fetch statement: " . $conn->error);
    }

    // Fetch cart items for the logged-in user to display order summary
    $cart_stmt = $conn->prepare("
        SELECT c.*, p.name as product_name, p.price, p.image_path
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    if ($cart_stmt) {
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        while ($row = $cart_result->fetch_assoc()) {
            $cart_items[] = $row;
            $total_price += ($row['price'] * $row['quantity']);
        }
        $cart_stmt->close();
    } else {
        error_log("Failed to prepare cart fetch statement: " . $conn->error);
    }
}

// Handle form submission for checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_address_id = $_POST['address_option'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;
    $payment_method_selected = true; // Flag to show payment details

    $new_street = $_POST['new_street'] ?? '';
    $new_exterior_number = $_POST['new_exterior_number'] ?? '';
    $new_interior_number = $_POST['new_interior_number'] ?? '';
    $new_colony = $_POST['new_colony'] ?? '';
    $new_postal_code = $_POST['new_postal_code'] ?? '';
    $new_city = $_POST['new_city'] ?? '';
    $new_state = $_POST['new_state'] ?? '';
    $new_phone_number = $_POST['new_phone_number'] ?? '';
    $new_alias = $_POST['new_alias'] ?? '';
    $set_default = isset($_POST['set_default']) ? 1 : 0;
    $final_address_id = null; // Determine the address to use for the order

    if ($selected_address_id === 'new' && !empty($new_street) && !empty($new_postal_code) && !empty($new_city) && !empty($new_state) && !empty($new_phone_number) && !empty($new_alias)) {
        // Insert new address
        if ($conn) {
            // Unset previous default address if a new one is set as default
            if ($set_default) {
                $conn->query("UPDATE addresses SET is_default = 0 WHERE user_id = " . $user_id);
            }
            $stmt = $conn->prepare("INSERT INTO addresses (user_id, alias, street, exterior_number, interior_number, colony, postal_code, city, state, phone_number, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isssssssssi", $user_id, $new_alias, $new_street, $new_exterior_number, $new_interior_number, $new_colony, $new_postal_code, $new_city, $new_state, $new_phone_number, $set_default);
                if ($stmt->execute()) {
                    $final_address_id = $conn->insert_id;
                    // Add the new address to the user_addresses array so it's available for display
                    $user_addresses[] = [
                        'id' => $final_address_id,
                        'alias' => $new_alias,
                        'street' => $new_street,
                        'exterior_number' => $new_exterior_number,
                        'interior_number' => $new_interior_number,
                        'colony' => $new_colony,
                        'postal_code' => $new_postal_code,
                        'city' => $new_city,
                        'state' => $new_state,
                        'phone_number' => $new_phone_number,
                        'is_default' => $set_default
                    ];
                } else {
                    error_log("Error inserting new address: " . $stmt->error);
                    $_SESSION['error_message'] = "Error al guardar la nueva dirección.";
                }
                $stmt->close();
            } else {
                error_log("Failed to prepare new address insertion statement: " . $conn->error);
            }
        }
    } else if (is_numeric($selected_address_id)) {
        $final_address_id = (int)$selected_address_id;
    }

    if ($final_address_id && $payment_method) {
        $order_status = 'pending';
        $cod_code = null;
        if ($payment_method === 'cash_on_delivery') {
            $cod_code = strtoupper(uniqid('COD-')); // Generate unique code for cash on delivery
            $order_status = 'pending_delivery'; // Or 'awaiting_pickup' depending on your flow
        } else if ($payment_method === 'bank_transfer') {
            $order_status = 'awaiting_payment_proof'; // Status for bank transfer
        }

        // --- Start Transaction ---
        $conn->begin_transaction();
        try {
            // Insert order into 'orders' table
            $order_stmt = $conn->prepare("INSERT INTO orders (user_id, address_id, total_amount, payment_method, cash_on_delivery_code, status) VALUES (?, ?, ?, ?, ?, ?)");
            if ($order_stmt) {
                $order_stmt->bind_param("iidsis", $user_id, $final_address_id, $total_price, $payment_method, $cod_code, $order_status);
                if ($order_stmt->execute()) {
                    $order_id = $conn->insert_id;

                    // Insert cart items into 'order_items' table
                    $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order, color, material, size, closure, lamination, sheet, sheet_cut, paper_type, form_type, vinyl_type, margin, image_position, artist_image_1, artist_image_2, artist_image_3, dedication, custom_card_text, custom_card_image, is_gift, gift_dedication, gift_bag, gift_wrap, gift_card) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    foreach ($cart_items as $item) {
                        $product_id = $item['product_id'];
                        $quantity = $item['quantity'];
                        $price_at_order = $item['price']; // Use the price from the products table fetched earlier

                        $color = $item['color'] ?? null;
                        $material = $item['material'] ?? null;
                        $size = $item['size'] ?? null;
                        $closure = $item['closure'] ?? null;
                        $lamination = $item['lamination'] ?? null;
                        $sheet = $item['sheet'] ?? null;
                        $sheet_cut = $item['sheet_cut'] ?? null;
                        $paper_type = $item['paper_type'] ?? null;
                        $form_type = $item['form_type'] ?? null;
                        $vinyl_type = $item['vinyl_type'] ?? null;
                        $margin = $item['margin'] ?? null;
                        $image_position = $item['image_position'] ?? null;
                        $artist_image_1 = $item['artist_image_1'] ?? null;
                        $artist_image_2 = $item['artist_image_2'] ?? null;
                        $artist_image_3 = $item['artist_image_3'] ?? null;
                        $dedication = $item['dedication'] ?? null;
                        $custom_card_text = $item['custom_card_text'] ?? null;
                        $custom_card_image = $item['custom_card_image'] ?? null;
                        $is_gift = $item['is_gift'] ?? 0;
                        $gift_dedication = $item['gift_dedication'] ?? null;
                        $gift_bag = $item['gift_bag'] ?? 0;
                        $gift_wrap = $item['gift_wrap'] ?? 0;
                        $gift_card = $item['gift_card'] ?? 0;

                        $order_item_stmt->bind_param("iiidsisssssssssssssssssiiii",
                            $order_id, $product_id, $quantity, $price_at_order,
                            $color, $material, $size, $closure, $lamination, $sheet, $sheet_cut,
                            $paper_type, $form_type, $vinyl_type, $margin, $image_position,
                            $artist_image_1, $artist_image_2, $artist_image_3,
                            $dedication, $custom_card_text, $custom_card_image,
                            $is_gift, $gift_dedication, $gift_bag, $gift_wrap, $gift_card
                        );
                        $order_item_stmt->execute();
                    }
                    $order_item_stmt->close();

                    // Clear user's cart
                    $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                    $clear_cart_stmt->bind_param("i", $user_id);
                    $clear_cart_stmt->execute();
                    $clear_cart_stmt->close();

                    $conn->commit(); // Commit transaction
                    $payment_success = true;

                    // Redirect to confirmation page
                    $_SESSION['order_placed'] = true;
                    $_SESSION['transaction_id'] = $transaction_id; // For bank transfer/online
                    $_SESSION['cod_code'] = $cod_code; // For cash on delivery
                    $_SESSION['order_method'] = $payment_method;
                    $_SESSION['order_total'] = $total_price;
                    $_SESSION['order_id'] = $order_id; // Pass the order ID

                    header("Location: order_confirmation.php?method=" . urlencode($payment_method) . "&order_id=" . $order_id);
                    exit();

                } else {
                    error_log("Error inserting order: " . $order_stmt->error);
                    $_SESSION['error_message'] = "Error al procesar la orden.";
                    $conn->rollback(); // Rollback transaction on error
                }
                $order_stmt->close();
            } else {
                error_log("Failed to prepare order insertion statement: " . $conn->error);
                $_SESSION['error_message'] = "Error interno al preparar la orden.";
                $conn->rollback(); // Rollback transaction on error
            }
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            $_SESSION['error_message'] = "Error inesperado al procesar la orden.";
            $conn->rollback(); // Rollback transaction on exception
        }
    } else {
        $_SESSION['error_message'] = "Por favor, selecciona una dirección y un método de pago válidos.";
    }
}

// Check for existing error messages
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear after displaying
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - CELAJE</title>
    <link rel="stylesheet" href="css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos específicos para la página de checkout */
        .checkout-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 25px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .checkout-header {
            width: 100%;
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
        }

        .checkout-section {
            flex: 1;
            min-width: 300px; /* Asegura que no se haga demasiado pequeño */
        }

        .checkout-section h2 {
            color: var(--primary-dark);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        .address-selection, .payment-selection {
            margin-bottom: 20px;
        }

        .address-option, .payment-option {
            display: block;
            margin-bottom: 10px;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background: var(--secondary-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .address-option:hover, .payment-option:hover,
        input[type="radio"]:checked + label.address-option,
        input[type="radio"]:checked + label.payment-option {
            border-color: var(--primary-light);
            background-color: #e6e6e6; /* Un poco más oscuro que secondary-color */
            box-shadow: var(--shadow);
        }

        .address-option input[type="radio"],
        .payment-option input[type="radio"] {
            margin-right: 10px;
            accent-color: var(--primary-color); /* Color del radio button */
        }

        .address-option p, .payment-option p {
            margin: 0;
            font-size: 0.95em;
            color: var(--text-dark);
        }

        .new-address-form {
            background: #f0f0f0;
            padding: 20px;
            border-radius: var(--border-radius);
            margin-top: 15px;
            display: <?php echo ($selected_address_id === 'new' && !$payment_success) ? 'block' : 'none'; ?>; /* Mostrar si se seleccionó "Nueva dirección" */
        }

        .new-address-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text-dark);
        }

        .new-address-form input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1em;
            color: var(--text-dark);
        }

        .new-address-form input[type="checkbox"] {
            margin-right: 10px;
        }

        .payment-details {
            margin-top: 20px;
            padding: 20px;
            background: #e6f7ff; /* Un fondo suave para los detalles de pago */
            border: 1px solid #b3e0ff;
            border-radius: var(--border-radius);
            display: none; /* Oculto por defecto, se muestra con JS */
        }

        .payment-details.show {
            display: block;
        }

        .payment-details h3 {
            color: var(--primary-dark);
            margin-top: 0;
            margin-bottom: 15px;
        }

        .payment-details p {
            margin-bottom: 8px;
            color: var(--text-dark);
            line-height: 1.5;
        }

        .payment-details strong {
            color: var(--accent-color);
        }

        .payment-details ul {
            list-style: none;
            padding: 0;
        }

        .payment-details ul li {
            margin-bottom: 5px;
        }


        .order-summary {
            flex: 1;
            min-width: 300px;
            background: var(--secondary-color);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .order-summary h2 {
            color: var(--primary-dark);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        .order-item-summary {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed var(--border-color);
        }

        .order-item-summary:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .order-item-summary img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: var(--border-radius);
        }

        .item-info {
            flex-grow: 1;
        }

        .item-info h4 {
            margin: 0 0 5px 0;
            color: var(--text-dark);
            font-size: 1em;
        }

        .item-info p {
            margin: 0;
            color: var(--text-light);
            font-size: 0.85em;
        }

        .order-total-summary {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2em;
            font-weight: bold;
            color: var(--primary-color);
        }

        .checkout-actions {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .place-order-btn {
            background: var(--accent-color);
            color: var(--white);
            padding: 15px 30px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1em;
            cursor: pointer;
            transition: var(--transition);
        }

        .place-order-btn:hover {
            background: #e65c2a; /* Tono más oscuro del acento */
            box-shadow: var(--shadow-hover);
        }

        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            text-align: center;
            width: 100%;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .checkout-container {
                flex-direction: column;
                padding: 15px;
                margin: 20px auto;
            }

            .checkout-section, .order-summary {
                min-width: unset;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; // Incluye el header ?>

    <main class="container">
        <div class="checkout-container">
            <h1 class="checkout-header">Finalizar Compra</h1>

            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="checkout.php" method="POST" class="checkout-form">
                <div class="checkout-section">
                    <h2>1. Dirección de Envío</h2>
                    <div class="address-selection">
                        <?php if (!empty($user_addresses)): ?>
                            <?php foreach ($user_addresses as $address): ?>
                                <input type="radio" id="address_<?php echo $address['id']; ?>" name="address_option" value="<?php echo $address['id']; ?>" <?php echo ($address['is_default'] || (isset($_POST['address_option']) && $_POST['address_option'] == $address['id'])) ? 'checked' : ''; ?>>
                                <label for="address_<?php echo $address['id']; ?>" class="address-option">
                                    <p><strong><?php echo htmlspecialchars($address['alias']); ?></strong> <?php echo $address['is_default'] ? '(Predeterminada)' : ''; ?></p>
                                    <p><?php echo htmlspecialchars($address['street'] . ' ' . $address['exterior_number'] . (!empty($address['interior_number']) ? ' Int. ' . $address['interior_number'] : '')); ?></p>
                                    <p><?php echo htmlspecialchars($address['colony'] . ', ' . $address['postal_code']); ?></p>
                                    <p><?php echo htmlspecialchars($address['city'] . ', ' . $address['state']); ?></p>
                                    <p>Teléfono: <?php echo htmlspecialchars($address['phone_number']); ?></p>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <input type="radio" id="address_new" name="address_option" value="new" <?php echo (isset($_POST['address_option']) && $_POST['address_option'] == 'new') ? 'checked' : ''; ?>>
                        <label for="address_new" class="address-option">
                            <p><strong>+ Agregar nueva dirección</strong></p>
                        </label>

                        <div id="newAddressForm" class="new-address-form">
                            <h3>Nueva Dirección</h3>
                            <label for="new_alias">Alias (Ej: Casa, Trabajo):</label>
                            <input type="text" id="new_alias" name="new_alias" required value="<?php echo htmlspecialchars($new_alias); ?>">

                            <label for="new_street">Calle:</label>
                            <input type="text" id="new_street" name="new_street" required value="<?php echo htmlspecialchars($new_street); ?>">

                            <label for="new_exterior_number">Número Exterior:</label>
                            <input type="text" id="new_exterior_number" name="new_exterior_number" required value="<?php echo htmlspecialchars($new_exterior_number); ?>">

                            <label for="new_interior_number">Número Interior (Opcional):</label>
                            <input type="text" id="new_interior_number" name="new_interior_number" value="<?php echo htmlspecialchars($new_interior_number); ?>">

                            <label for="new_colony">Colonia:</label>
                            <input type="text" id="new_colony" name="new_colony" value="<?php echo htmlspecialchars($new_colony); ?>">

                            <label for="new_postal_code">Código Postal:</label>
                            <input type="text" id="new_postal_code" name="new_postal_code" required value="<?php echo htmlspecialchars($new_postal_code); ?>">

                            <label for="new_city">Ciudad:</label>
                            <input type="text" id="new_city" name="new_city" required value="<?php echo htmlspecialchars($new_city); ?>">

                            <label for="new_state">Estado:</label>
                            <input type="text" id="new_state" name="new_state" required value="<?php echo htmlspecialchars($new_state); ?>">

                            <label for="new_phone_number">Número de Teléfono:</label>
                            <input type="text" id="new_phone_number" name="new_phone_number" required value="<?php echo htmlspecialchars($new_phone_number); ?>">

                            <input type="checkbox" id="set_default" name="set_default" <?php echo $set_default ? 'checked' : ''; ?>>
                            <label for="set_default" style="display: inline-block;">Establecer como dirección predeterminada</label>
                        </div>
                    </div>

                    <h2>2. Método de Pago</h2>
                    <div class="payment-selection">
                        <input type="radio" id="payment_bank_transfer" name="payment_method" value="bank_transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank_transfer') ? 'checked' : ''; ?>>
                        <label for="payment_bank_transfer" class="payment-option">
                            <p><strong>Transferencia Bancaria / Depósito</strong></p>
                            <p>Realiza una transferencia o depósito bancario. Se te proporcionarán los datos de la cuenta.</p>
                        </label>

                        <input type="radio" id="payment_cash_on_delivery" name="payment_method" value="cash_on_delivery" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cash_on_delivery') ? 'checked' : ''; ?>>
                        <label for="payment_cash_on_delivery" class="payment-option">
                            <p><strong>Pago Contra Entrega</strong></p>
                            <p>Paga en efectivo o con tarjeta al momento de recibir tu pedido.</p>
                        </label>
                    </div>

                    <div id="paymentDetails" class="payment-details <?php echo $payment_method_selected ? 'show' : ''; ?>">
                        <?php if (isset($_POST['payment_method']) && $_POST['payment_method'] === 'bank_transfer'): ?>
                            <h3>Instrucciones para Transferencia Bancaria</h3>
                            <p>Por favor, realiza la transferencia o depósito a la siguiente cuenta:</p>
                            <ul>
                                <li><strong>Banco:</strong> [Nombre de tu Banco]</li>
                                <li><strong>Número de Cuenta:</strong> [Tu Número de Cuenta]</li>
                                <li><strong>CLABE Interbancaria:</strong> [Tu CLABE]</li>
                                <li><strong>Beneficiario:</strong> [Tu Nombre / Razón Social]</li>
                                <li><strong>Monto Total:</strong> <strong>$<?php echo number_format($total_price, 2); ?> MXN</strong></li>
                            </ul>
                            <p>Una vez realizada la transferencia, sube el comprobante en la sección de "Comprobantes de Pago" en tu perfil o sigue las instrucciones en la página de confirmación de pedido.</p>
                        <?php elseif (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cash_on_delivery'): ?>
                            <h3>Pago Contra Entrega</h3>
                            <p>El pago se realizará al momento de la entrega de tu pedido.</p>
                            <p>Por favor, ten listo el monto exacto: <strong>$<?php echo number_format($total_price, 2); ?> MXN</strong>.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="order-summary">
                    <h2>Resumen del Pedido</h2>
                    <?php if (empty($cart_items)): ?>
                        <p>Tu carrito está vacío.</p>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="order-item-summary">
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                    <p>Cantidad: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                    <p>Precio Unitario: $<?php echo number_format($item['price'], 2); ?></p>
                                    <?php
                                    // Display custom attributes if they exist
                                    $custom_attributes = [];
                                    if (!empty($item['color'])) $custom_attributes[] = 'Color: ' . htmlspecialchars($item['color']);
                                    if (!empty($item['size'])) $custom_attributes[] = 'Talla: ' . htmlspecialchars($item['size']);
                                    if (!empty($item['material'])) $custom_attributes[] = 'Material: ' . htmlspecialchars($item['material']);
                                    // Add more custom attributes as needed
                                    if (!empty($custom_attributes)) {
                                        echo '<p style="font-size: 0.8em; color: var(--text-light);">' . implode(', ', $custom_attributes) . '</p>';
                                    }
                                    ?>
                                </div>
                                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="order-total-summary">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total_price, 2); ?> MXN</span>
                    </div>

                    <div class="checkout-actions">
                        <button type="submit" class="place-order-btn">Realizar Pedido</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; // Incluye el footer ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addressOptions = document.querySelectorAll('input[name="address_option"]');
            const newAddressForm = document.getElementById('newAddressForm');
            const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
            const paymentDetails = document.getElementById('paymentDetails');

            function toggleNewAddressForm() {
                if (document.getElementById('address_new').checked) {
                    newAddressForm.style.display = 'block';
                    // Make new address fields required when selected
                    newAddressForm.querySelectorAll('input[required]').forEach(input => {
                        input.setAttribute('required', 'required');
                    });
                } else {
                    newAddressForm.style.display = 'none';
                    // Remove required attribute when not selected
                    newAddressForm.querySelectorAll('input[required]').forEach(input => {
                        input.removeAttribute('required');
                    });
                }
            }

            function togglePaymentDetails() {
                let selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                if (selectedMethod) {
                    paymentDetails.classList.add('show');
                    paymentDetails.innerHTML = ''; // Clear previous content

                    if (selectedMethod.value === 'bank_transfer') {
                        paymentDetails.innerHTML = `
                            <h3>Instrucciones para Transferencia Bancaria</h3>
                            <p>Por favor, realiza la transferencia o depósito a la siguiente cuenta:</p>
                            <ul>
                                <li><strong>Banco:</strong> [Nombre de tu Banco]</li>
                                <li><strong>Número de Cuenta:</strong> [Tu Número de Cuenta]</li>
                                <li><strong>CLABE Interbancaria:</strong> [Tu CLABE]</li>
                                <li><strong>Beneficiario:</strong> [Tu Nombre / Razón Social]</li>
                                <li><strong>Monto Total:</strong> <strong>$<?php echo number_format($total_price, 2); ?> MXN</strong></li>
                            </ul>
                            <p>Una vez realizada la transferencia, sube el comprobante en la sección de "Comprobantes de Pago" en tu perfil o sigue las instrucciones en la página de confirmación de pedido.</p>
                        `;
                    } else if (selectedMethod.value === 'cash_on_delivery') {
                        paymentDetails.innerHTML = `
                            <h3>Pago Contra Entrega</h3>
                            <p>El pago se realizará al momento de la entrega de tu pedido.</p>
                            <p>Por favor, ten listo el monto exacto: <strong>$<?php echo number_format($total_price, 2); ?> MXN</strong>.</p>
                        `;
                    }
                } else {
                    paymentDetails.classList.remove('show');
                }
            }

            addressOptions.forEach(option => {
                option.addEventListener('change', toggleNewAddressForm);
            });

            paymentOptions.forEach(option => {
                option.addEventListener('change', togglePaymentDetails);
            });

            // Initial checks on page load
            toggleNewAddressForm();
            togglePaymentDetails();
        });
    </script>
</body>
</html>