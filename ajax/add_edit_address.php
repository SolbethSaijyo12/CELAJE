<?php
session_start();
// No headers here, as it can be used for both JSON and HTML output, depending on context.
// header('Content-Type: application/json'); // Removed for now, handle based on if it's an AJAX request or not

if (!isset($_SESSION['user_id'])) {
    // If not authenticated, redirect to login page for HTML requests
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        header('Location: /CELAJE/login.php');
        exit;
    } else {
        // For AJAX requests, return JSON error
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
        exit;
    }
}

include 'includes/db.php'; // Adjust path if necessary

$user_id = $_SESSION['user_id'];
$address_id = $_GET['id'] ?? null;
$address_data = null;
$message = '';
$message_type = '';

// Fetch address data if editing
if ($address_id && is_numeric($address_id)) {
    $stmt = $conn->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $address_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $address_data = $result->fetch_assoc();
        } else {
            $_SESSION['error_message'] = "Dirección no encontrada o no pertenece al usuario.";
            header('Location: /CELAJE/perfil.php');
            exit;
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an AJAX delete request
    if (isset($_POST['delete_id'])) {
        $address_id_to_delete = $_POST['delete_id'];
        try {
            $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
            if ($stmt) {
                $stmt->bind_param("ii", $address_id_to_delete, $user_id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Dirección eliminada con éxito.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar la dirección: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de eliminación: ' . $conn->error]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        exit; // Exit after handling AJAX delete request
    }

    // Handle add/edit address form submission
    $alias = trim($_POST['alias'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $exterior_number = trim($_POST['exterior_number'] ?? '');
    $interior_number = trim($_POST['interior_number'] ?? '');
    $colony = trim($_POST['colony'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    if (empty($alias) || empty($street) || empty($exterior_number) || empty($postal_code) || empty($city) || empty($state) || empty($phone_number)) {
        $message = 'Todos los campos obligatorios deben ser llenados.';
        $message_type = 'error';
    } else {
        try {
            // If setting as default, unset previous default for this user
            if ($is_default) {
                $conn->query("UPDATE addresses SET is_default = 0 WHERE user_id = " . $user_id);
            }

            if ($address_id) { // Editing existing address
                $stmt = $conn->prepare("UPDATE addresses SET alias = ?, street = ?, exterior_number = ?, interior_number = ?, colony = ?, postal_code = ?, city = ?, state = ?, phone_number = ?, is_default = ? WHERE id = ? AND user_id = ?");
                if ($stmt) {
                    $stmt->bind_param("sssssssssiii", $alias, $street, $exterior_number, $interior_number, $colony, $postal_code, $city, $state, $phone_number, $is_default, $address_id, $user_id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "Dirección actualizada con éxito.";
                        header('Location: /CELAJE/perfil.php');
                        exit;
                    } else {
                        $message = 'Error al actualizar la dirección: ' . $stmt->error;
                        $message_type = 'error';
                    }
                    $stmt->close();
                }
            } else { // Adding new address
                $stmt = $conn->prepare("INSERT INTO addresses (user_id, alias, street, exterior_number, interior_number, colony, postal_code, city, state, phone_number, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("isssssssssi", $user_id, $alias, $street, $exterior_number, $interior_number, $colony, $postal_code, $city, $state, $phone_number, $is_default);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "Dirección agregada con éxito.";
                        header('Location: /CELAJE/perfil.php');
                        exit;
                    } else {
                        $message = 'Error al agregar la dirección: ' . $stmt->error;
                        $message_type = 'error';
                    }
                    $stmt->close();
                }
            }
        } catch (Exception $e) {
            $message = 'Error en la base de datos: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $address_id ? 'Editar' : 'Agregar'; ?> Dirección - CELAJE</title>
    <link rel="stylesheet" href="css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos para el formulario de dirección */
        .address-form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .address-form-container h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text-dark);
        }

        .form-group input[type="text"] {
            width: calc(100% - 20px);
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1em;
            color: var(--text-dark);
            box-sizing: border-box; /* Incluir padding en el ancho */
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-submit, .btn-cancel {
            padding: 12px 25px;
            border-radius: var(--border-radius);
            font-size: 1em;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-submit {
            background: var(--primary-color);
            color: var(--white);
            border: none;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-hover);
        }

        .btn-cancel {
            background: var(--secondary-color);
            color: var(--text-dark);
            border: 1px solid var(--border-color);
        }

        .btn-cancel:hover {
            background: #e9ecef;
        }

        .message-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            font-weight: bold;
            text-align: center;
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
    </style>
</head>
<body>
    <?php include 'includes/header.php'; // Incluye el header ?>

    <main class="container">
        <div class="address-form-container">
            <h1><?php echo $address_id ? 'Editar Dirección' : 'Agregar Nueva Dirección'; ?></h1>

            <?php if ($message): ?>
                <div class="message-box <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="/CELAJE/add_edit_address.php<?php echo $address_id ? '?id=' . $address_id : ''; ?>" method="POST">
                <div class="form-group">
                    <label for="alias">Alias (Ej: Casa, Trabajo):</label>
                    <input type="text" id="alias" name="alias" value="<?php echo htmlspecialchars($address_data['alias'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="street">Calle:</label>
                    <input type="text" id="street" name="street" value="<?php echo htmlspecialchars($address_data['street'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="exterior_number">Número Exterior:</label>
                    <input type="text" id="exterior_number" name="exterior_number" value="<?php echo htmlspecialchars($address_data['exterior_number'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="interior_number">Número Interior (Opcional):</label>
                    <input type="text" id="interior_number" name="interior_number" value="<?php echo htmlspecialchars($address_data['interior_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="colony">Colonia:</label>
                    <input type="text" id="colony" name="colony" value="<?php echo htmlspecialchars($address_data['colony'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="postal_code">Código Postal:</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($address_data['postal_code'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="city">Ciudad:</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($address_data['city'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="state">Estado:</label>
                    <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($address_data['state'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Número de Teléfono:</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($address_data['phone_number'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="is_default" name="is_default" <?php echo ($address_data['is_default'] ?? 0) ? 'checked' : ''; ?>>
                    <label for="is_default" style="display: inline-block; margin-left: 5px;">Establecer como dirección predeterminada</label>
                </div>

                <div class="form-actions">
                    <a href="/CELAJE/perfil.php" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-submit"><?php echo $address_id ? 'Actualizar Dirección' : 'Guardar Dirección'; ?></button>
                </div>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; // Incluye el footer ?>
</body>
</html>