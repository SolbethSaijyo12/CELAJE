<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /CELAJE/login.php');
    exit;
}
include 'includes/db.php';

// Consultar datos del usuario
$user = null;
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Si no se encuentra el usuario, redirigir o manejar el error
    die('Usuario no encontrado.');
}

// Asegurarse de que el nombre completo del usuario esté en la sesión
// Esto es útil si el usuario se registró sin first_name/last_name y luego los añadió
if (empty($_SESSION['username']) && !empty($user['first_name'])) {
    $_SESSION['username'] = trim($user['first_name'] . ' ' . $user['last_name']);
} else if (empty($_SESSION['username'])) {
    $_SESSION['username'] = $user['email']; // Fallback si no hay nombre
}

// --- Fetch User Addresses ---
$user_addresses = [];
$addr_stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
if ($addr_stmt) {
    $addr_stmt->bind_param("i", $_SESSION['user_id']);
    $addr_stmt->execute();
    $addr_result = $addr_stmt->get_result();
    while ($row = $addr_result->fetch_assoc()) {
        $user_addresses[] = $row;
    }
    $addr_stmt->close();
} else {
    error_log("Error al preparar la consulta de direcciones: " . $conn->error);
}

// --- Fetch User Orders ---
$user_orders = [];
$order_stmt = $conn->prepare("SELECT id, order_date, total_amount, status, payment_method, cash_on_delivery_code, transaction_id FROM orders WHERE user_id = ? ORDER BY order_date DESC");
if ($order_stmt) {
    $order_stmt->bind_param("i", $_SESSION['user_id']);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    while ($row = $order_result->fetch_assoc()) {
        $user_orders[] = $row;
    }
    $order_stmt->close();
} else {
    error_log("Error al preparar la consulta de órdenes: " . $conn->error);
}

// Check for success/error messages from other pages
$message = '';
$message_type = '';
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $message_type = 'success';
    unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    $message_type = 'error';
    unset($_SESSION['error_message']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - CELAJE</title>
    <link rel="stylesheet" href="/CELAJE/css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos adicionales para la sección de Direcciones y Pedidos */
        .profile-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 25px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .profile-header {
            width: 100%;
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-size: 2.5em;
        }

        .profile-section {
            flex: 1;
            min-width: 300px; /* Asegura que no se haga demasiado pequeño */
            background: var(--secondary-color);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .profile-section h2 {
            color: var(--primary-dark);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .profile-section h2 .section-actions {
            font-size: 0.8em;
        }

        .profile-info p {
            margin-bottom: 10px;
            color: var(--text-dark);
        }
        .profile-info strong {
            color: var(--primary-color);
        }

        /* Message styles */
        .message-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            font-weight: bold;
            text-align: center;
            width: 100%;
        }
        .message-box.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Address styles */
        .address-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .address-card {
            background: #f9f9f9;
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 20px;
            position: relative;
            box-shadow: 0 1px 5px rgba(0,0,0,0.05);
        }
        .address-card h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .address-card p {
            margin-bottom: 5px;
            color: var(--text-dark);
            font-size: 0.95rem;
        }
        .address-card .actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .address-card .actions button, .address-card .actions a {
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .address-card .actions .edit-btn {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .address-card .actions .edit-btn:hover {
            background-color: #0056b3;
        }
        .address-card .actions .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .address-card .actions .delete-btn:hover {
            background-color: #a71d2a;
        }
        .address-card .default-badge {
            background-color: var(--accent-color);
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.75em;
            margin-left: 10px;
        }

        .no-addresses, .no-orders {
            text-align: center;
            color: var(--text-light);
            padding: 20px;
            border: 1px dashed var(--border-color);
            border-radius: var(--border-radius);
            margin-top: 20px;
        }

        /* Order styles */
        .order-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .order-card {
            background: #f9f9f9;
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.05);
        }
        .order-card h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        .order-card p {
            margin-bottom: 5px;
            color: var(--text-dark);
            font-size: 0.95rem;
        }
        .order-card .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
            display: inline-block;
            margin-top: 5px;
        }
        .status-pending { background-color: #ffc107; } /* Warning yellow */
        .status-processing { background-color: #17a2b8; } /* Info blue */
        .status-completed { background-color: #28a745; } /* Success green */
        .status-cancelled { background-color: #dc3545; } /* Danger red */
        .status-awaiting_payment_proof { background-color: #fd7e14; } /* Orange */
        .status-pending_delivery { background-color: #6f42c1; } /* Purple */

        .order-card .order-actions {
            margin-top: 15px;
            text-align: right;
        }
        .order-card .order-actions .btn-view-details {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .order-card .order-actions .btn-view-details:hover {
            background-color: var(--primary-dark);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                padding: 15px;
                margin: 20px auto;
            }

            .profile-section {
                min-width: unset;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; // Incluye el header ?>

    <main class="container">
        <div class="profile-container">
            <h1 class="profile-header">Mi Perfil</h1>

            <?php if ($message): ?>
                <div class="message-box <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="profile-section profile-info">
                <h2>Información Personal <a href="/CELAJE/editar_perfil.php" class="btn-secondary" style="font-size: 0.7em; padding: 5px 10px;">Editar <i class="fas fa-edit"></i></a></h2>
                <?php if ($user): ?>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Miembro desde:</strong> <?php echo (new DateTime($user['created_at']))->format('d/m/Y'); ?></p>
                <?php else: ?>
                    <p>No se pudieron cargar los datos del usuario.</p>
                <?php endif; ?>
            </div>

            <div class="profile-section">
                <h2>Mis Direcciones <a href="/CELAJE/add_edit_address.php" class="btn-primary" style="font-size: 0.7em; padding: 5px 10px;">Agregar Nueva <i class="fas fa-plus-circle"></i></a></h2>
                <div class="address-list">
                    <?php if (!empty($user_addresses)): ?>
                        <?php foreach ($user_addresses as $address): ?>
                            <div class="address-card">
                                <h4>
                                    <?php echo htmlspecialchars($address['alias']); ?>
                                    <?php if ($address['is_default']): ?>
                                        <span class="default-badge">Predeterminada</span>
                                    <?php endif; ?>
                                </h4>
                                <p><?php echo htmlspecialchars($address['street'] . ' ' . $address['exterior_number'] . (!empty($address['interior_number']) ? ' Int. ' . $address['interior_number'] : '')); ?></p>
                                <p><?php echo htmlspecialchars($address['colony'] . ', ' . $address['postal_code']); ?></p>
                                <p><?php echo htmlspecialchars($address['city'] . ', ' . $address['state']); ?></p>
                                <p>Teléfono: <?php echo htmlspecialchars($address['phone_number']); ?></p>
                                <div class="actions">
                                    <a href="/CELAJE/add_edit_address.php?id=<?php echo $address['id']; ?>" class="edit-btn"><i class="fas fa-edit"></i> Editar</a>
                                    <button class="delete-btn" data-id="<?php echo $address['id']; ?>"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-addresses">
                            <p>Aún no tienes direcciones guardadas. <a href="/CELAJE/add_edit_address.php">¡Agrega una ahora!</a></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="/CELAJE/add_edit_address.php" class="btn-primary">Gestionar Todas las Direcciones</a>
                </div>
            </div>

            <div class="profile-section">
                <h2>Mis Pedidos</h2>
                <div class="order-list">
                    <?php if (!empty($user_orders)): ?>
                        <?php foreach ($user_orders as $order): ?>
                            <div class="order-card">
                                <h4>Pedido #<?php echo htmlspecialchars($order['id']); ?></h4>
                                <p><strong>Fecha:</strong> <?php echo (new DateTime($order['order_date']))->format('d/m/Y H:i'); ?></p>
                                <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?> MXN</p>
                                <p><strong>Método de Pago:</strong>
                                    <?php
                                    if ($order['payment_method'] === 'bank_transfer') {
                                        echo 'Transferencia Bancaria / Depósito';
                                    } elseif ($order['payment_method'] === 'cash_on_delivery') {
                                        echo 'Pago Contra Entrega';
                                    } else {
                                        echo 'Otro';
                                    }
                                    ?>
                                </p>
                                <p><strong>Estado:</strong>
                                    <span class="status-badge status-<?php echo str_replace(' ', '_', strtolower($order['status'])); ?>">
                                        <?php
                                        // Mapeo de estados para una mejor visualización
                                        $status_map = [
                                            'pending' => 'Pendiente',
                                            'processing' => 'En Procesamiento',
                                            'completed' => 'Completado',
                                            'cancelled' => 'Cancelado',
                                            'awaiting_payment_proof' => 'Esperando Comprobante',
                                            'pending_delivery' => 'Pendiente de Entrega'
                                        ];
                                        echo htmlspecialchars($status_map[$order['status']] ?? $order['status']);
                                        ?>
                                    </span>
                                </p>
                                <?php if ($order['payment_method'] === 'cash_on_delivery' && !empty($order['cash_on_delivery_code'])): ?>
                                    <p style="font-size: 0.9em; color: var(--accent-color); font-weight: bold;">Código Contra Entrega: <?php echo htmlspecialchars($order['cash_on_delivery_code']); ?></p>
                                <?php endif; ?>
                                <div class="order-actions">
                                    <a href="order_confirmation.php?order_id=<?php echo $order['id']; ?>" class="btn-view-details">Ver Detalles</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-orders">
                            <p>Aún no has realizado ningún pedido.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; // Incluye el footer ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejo de eliminación de dirección con AJAX
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const addressId = this.dataset.id;
                    if (confirm('¿Estás seguro de que quieres eliminar esta dirección?')) {
                        fetch('/CELAJE/add_edit_address.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `delete_id=${addressId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                window.location.reload(); // Recargar la página para ver el cambio
                            } else {
                                alert('Error al eliminar la dirección: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Ocurrió un error al intentar eliminar la dirección.');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>