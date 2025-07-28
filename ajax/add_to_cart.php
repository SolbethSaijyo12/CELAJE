<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

try {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    // Validar producto
    $stmt = $conn->prepare("SELECT id, name, price, image_path, category FROM products WHERE id = ?");
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta de producto: " . $conn->error);
    }
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado.']);
        exit;
    }

    // Verificar sesión de usuario
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión para agregar productos al carrito.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Verificar si el producto ya está en el carrito
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta de selección: " . $conn->error);
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_item = $result->fetch_assoc();
    $stmt->close();

    if ($existing_item) {
        // Actualizar cantidad
        $new_quantity = $existing_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        if ($stmt === false) {
            throw new Exception("Error al preparar la actualización: " . $conn->error);
        }
        $stmt->bind_param("ii", $new_quantity, $existing_item['id']);
        $stmt->execute();
        $stmt->close();
        $message = 'Cantidad actualizada en el carrito.';
    } else {
        // Insertar nuevo item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        if ($stmt === false) {
            throw new Exception("Error al preparar la inserción: " . $conn->error);
        }
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
        $stmt->close();
        $message = 'Producto agregado al carrito.';
    }
    
    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    error_log("Error en add_to_cart.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor.']);
}
?>