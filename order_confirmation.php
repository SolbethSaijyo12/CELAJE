<?php
session_start();
require_once 'includes/db.php'; // Asegúrate que esta ruta sea correcta

$order_id = $_GET['order_id'] ?? null;
$method = $_GET['method'] ?? 'unknown';

$order_details = null;
$order_items = [];

if ($order_id && $conn) {
    // Fetch order details
    $stmt = $conn->prepare("SELECT o.*, a.street, a.exterior_number, a.interior_number, a.colony, a.postal_code, a.city, a.state, a.phone_number, a.alias as address_alias
                            FROM orders o
                            JOIN addresses a ON o.address_id = a.id
                            WHERE o.id = ? AND o.user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $order_details = $result->fetch_assoc();

            // Fetch order items
            $items_stmt = $conn->prepare("SELECT oi.*, p.name as product_name, p.image_path
                                          FROM order_items oi
                                          JOIN products p ON oi.product_id = p.id
                                          WHERE oi.order_id = ?");
            if ($items_stmt) {
                $items_stmt->bind_param("i", $order_id);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                while ($row = $items_result->fetch_assoc()) {
                    $order_items[] = $row;
                }
                $items_stmt->close();
            }
        }
        $stmt->close();
    }
}

// Clear specific session variables related to order placement after they've been used
if (isset($_SESSION['order_placed'])) {
    unset($_SESSION['order_placed']);
}
if (isset($_SESSION['transaction_id'])) {
    unset($_SESSION['transaction_id']);
}
if (isset($_SESSION['cod_code'])) {
    unset($_SESSION['cod_code']);
}
if (isset($_SESSION['order_method'])) {
    unset($_SESSION['order_method']);
}
if (isset($_SESSION['order_total'])) {
    unset($_SESSION['order_total']);
}
if (isset($_SESSION['order_id'])) {
    unset($_SESSION['order_id']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido - CELAJE</title>
    <link rel="stylesheet" href="css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos específicos para la confirmación de pedido */
        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
        }

        .confirmation-icon {
            color: var(--primary-color);
            font-size: 4em;
            margin-bottom: 20px;
        }

        .confirmation-container h1 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 2.5em;
        }

        .confirmation-container p {
            font-size: 1.1em;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .confirmation-details {
            background: var(--secondary-color);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-top: 30px;
            text-align: left;
        }

        .confirmation-details h2 {
            color: var(--primary-dark);
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dotted var(--border-color);
        }

        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-item strong {
            color: var(--text-dark);
        }

        .detail-item span {
            color: var(--text-light);
        }

        .order-items-summary {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .order-items-summary h3 {
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .order-item-card {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding: 10px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .order-item-card img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: var(--border-radius);
        }

        .order-item-info {
            flex-grow: 1;
        }

        .order-item-info h4 {
            margin: 0 0 5px 0;
            color: var(--text-dark);
        }

        .order-item-info p {
            margin: 0;
            color: var(--text-light);
            font-size: 0.9em;
        }

        .confirmation-actions {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn-primary, .btn-secondary {
            display: inline-block;
            padding: 12px 25px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: bold;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
            border: 2px solid var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            box-shadow: var(--shadow-hover);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background: var(--primary-light);
            color: var(--white);
            box-shadow: var(--shadow-hover);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; // Incluye el header ?>

    <main class="container">
        <div class="confirmation-container">
            <i class="fas fa-check-circle confirmation-icon"></i>
            <h1>¡Pedido Realizado con Éxito!</h1>
            <p>Gracias por tu compra. Tu pedido ha sido recibido y está siendo procesado.</p>

            <?php if ($order_details): ?>
                <div class="confirmation-details">
                    <h2>Detalles del Pedido #<?php echo htmlspecialchars($order_details['id']); ?></h2>
                    <div class="detail-item">
                        <strong>Fecha del Pedido:</strong>
                        <span><?php echo (new DateTime($order_details['order_date']))->format('d/m/Y H:i'); ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Monto Total:</strong>
                        <span>$<?php echo number_format($order_details['total_amount'], 2); ?> MXN</span>
                    </div>
                    <div class="detail-item">
                        <strong>Método de Pago:</strong>
                        <span>
                            <?php
                            if ($order_details['payment_method'] === 'bank_transfer') {
                                echo 'Transferencia Bancaria / Depósito';
                            } elseif ($order_details['payment_method'] === 'cash_on_delivery') {
                                echo 'Pago Contra Entrega';
                            } else {
                                echo 'Otro';
                            }
                            ?>
                        </span>
                    </div>

                    <?php if ($order_details['payment_method'] === 'cash_on_delivery' && ($order_details['cash_on_delivery_code'] ?? null)): ?>
                        <div class="detail-item">
                            <strong>Código de Confirmación Contra Entrega:</strong>
                            <span style="font-weight: bold; color: var(--accent-color); font-size: 1.2em;"><?php echo htmlspecialchars($order_details['cash_on_delivery_code']); ?></span>
                        </div>
                        <p style="margin-top: 15px; font-size: 0.9em; color: var(--text-light);">Por favor, proporciona este código al repartidor al momento de la entrega para confirmar tu pedido.</p>
                    <?php endif; ?>

                    <?php if ($order_details['payment_method'] === 'bank_transfer'): ?>
                        <p style="margin-top: 15px; font-size: 0.9em; color: var(--text-light);">Tu pedido está en estado "Esperando Comprobante de Pago". Por favor, sube tu comprobante para que podamos procesar tu orden.</p>
                    <?php endif; ?>

                    <div class="detail-item">
                        <strong>Dirección de Envío:</strong>
                        <span>
                            <?php echo htmlspecialchars($order_details['address_alias'] ?? 'N/A'); ?><br>
                            <?php echo htmlspecialchars($order_details['street'] ?? 'N/A') . ' ' . htmlspecialchars($order_details['exterior_number'] ?? ''); ?> <?php echo htmlspecialchars($order_details['interior_number'] ? 'Int. ' . $order_details['interior_number'] : ''); ?><br>
                            <?php echo htmlspecialchars($order_details['colony'] ?? 'N/A') . ', CP ' . htmlspecialchars($order_details['postal_code'] ?? 'N/A'); ?><br>
                            <?php echo htmlspecialchars($order_details['city'] ?? 'N/A') . ', ' . htmlspecialchars($order_details['state'] ?? 'N/A'); ?><br>
                            Teléfono: <?php echo htmlspecialchars($order_details['phone_number'] ?? 'N/A'); ?>
                        </span>
                    </div>

                    <div class="order-items-summary">
                        <h3>Artículos del Pedido:</h3>
                        <?php if (!empty($order_items)): ?>
                            <?php foreach ($order_items as $item): ?>
                                <div class="order-item-card">
                                    <img src="<?php echo htmlspecialchars($item['image_path'] ?? 'assets/images/default_product.png'); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    <div class="order-item-info">
                                        <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                        <p>Cantidad: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                        <p>Precio Unitario: $<?php echo number_format($item['price_at_order'], 2); ?></p>
                                        <?php
                                        $custom_attributes = [];
                                        if (!empty($item['color'])) $custom_attributes[] = 'Color: ' . htmlspecialchars($item['color']);
                                        if (!empty($item['size'])) $custom_attributes[] = 'Talla: ' . htmlspecialchars($item['size']);
                                        if (!empty($item['material'])) $custom_attributes[] = 'Material: ' . htmlspecialchars($item['material']);
                                        if (!empty($item['closure'])) $custom_attributes[] = 'Cierre: ' . htmlspecialchars($item['closure']);
                                        if (!empty($item['lamination'])) $custom_attributes[] = 'Laminado: ' . htmlspecialchars($item['lamination']);
                                        if (!empty($item['sheet'])) $custom_attributes[] = 'Hoja: ' . htmlspecialchars($item['sheet']);
                                        if (!empty($item['sheet_cut'])) $custom_attributes[] = 'Corte de Hoja: ' . htmlspecialchars($item['sheet_cut']);
                                        if (!empty($item['paper_type'])) $custom_attributes[] = 'Tipo de Papel: ' . htmlspecialchars($item['paper_type']);
                                        if (!empty($item['form_type'])) $custom_attributes[] = 'Tipo de Forma: ' . htmlspecialchars($item['form_type']);
                                        if (!empty($item['vinyl_type'])) $custom_attributes[] = 'Tipo de Vinilo: ' . htmlspecialchars($item['vinyl_type']);
                                        if (!empty($item['margin'])) $custom_attributes[] = 'Margen: ' . htmlspecialchars($item['margin']);
                                        if (!empty($item['image_position'])) $custom_attributes[] = 'Posición de Imagen: ' . htmlspecialchars($item['image_position']);
                                        if (!empty($item['dedication'])) $custom_attributes[] = 'Dedicación: ' . htmlspecialchars($item['dedication']);
                                        if (!empty($item['custom_card_text'])) $custom_attributes[] = 'Texto Tarjeta Personalizada: ' . htmlspecialchars($item['custom_card_text']);
                                        if (!empty($item['custom_card_image'])) $custom_attributes[] = 'Imagen Tarjeta Personalizada: <a href="' . htmlspecialchars($item['custom_card_image']) . '" target="_blank">Ver</a>';
                                        if (!empty($item['artist_image_1'])) $custom_attributes[] = 'Imagen Artista 1: <a href="' . htmlspecialchars($item['artist_image_1']) . '" target="_blank">Ver</a>';
                                        if (!empty($item['artist_image_2'])) $custom_attributes[] = 'Imagen Artista 2: <a href="' . htmlspecialchars($item['artist_image_2']) . '" target="_blank">Ver</a>';
                                        if (!empty($item['artist_image_3'])) $custom_attributes[] = 'Imagen Artista 3: <a href="' . htmlspecialchars($item['artist_image_3']) . '" target="_blank">Ver</a>';


                                        if (!empty($custom_attributes)) {
                                            echo '<p style="font-size: 0.85em; color: var(--text-light);">' . implode(', ', $custom_attributes) . '</p>';
                                        }
                                        ?>
                                    </div>
                                    <span>$<?php echo number_format($item['price_at_order'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No se encontraron artículos para este pedido.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="confirmation-actions">
                    <a href="perfil.php" class="btn-primary">Ver mis Pedidos</a>
                    <?php if ($order_details['payment_method'] === 'bank_transfer'): ?>
                        <a href="upload_proof.php?order_id=<?php echo $order_details['id']; ?>" class="btn-secondary">Subir Comprobante de Pago</a>
                    <?php endif; ?>
                    <a href="index.php" class="btn-secondary">Volver al Inicio</a>
                </div>
            <?php else: ?>
                <p>No se pudo encontrar la información del pedido. Por favor, verifica tus pedidos en <a href="perfil.php">Mi Perfil</a>.</p>
                <div class="confirmation-actions">
                    <a href="perfil.php" class="btn-primary">Ver mis Pedidos</a>
                    <a href="index.php" class="btn-secondary">Volver al Inicio</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; // Incluye el footer ?>
</body>
</html>