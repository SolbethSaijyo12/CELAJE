<?php

include '../includes/db.php'; // Asegúrate que esta ruta sea correcta para tu estructura de carpetas

header('Content-Type: application/json');

$productId = intval($_GET['id'] ?? 0);

if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de producto inválido']);
    exit;
}

try {
    // *** CAMBIO CLAVE AQUÍ: Solo seleccionar las columnas que existen en la tabla `products` ***
    $stmt = $conn->prepare("SELECT id, name, description, price, image_path, category, stock,
                                    colors, materials, sizes
                            FROM products WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($product) {
        // Convertir campos de texto a arrays para JavaScript si es necesario (ej. "Negro,Blanco" -> ["Negro", "Blanco"])
        // Esto depende de cómo uses estos campos en tu JS. Si los usas directamente como string, no es necesario.
        // Pero para el quick view, es útil tenerlos como array.
        $product['colors'] = $product['colors'] ? explode(',', $product['colors']) : [];
        $product['materials'] = $product['materials'] ? explode(',', $product['materials']) : [];
        $product['sizes'] = $product['sizes'] ? explode(',', $product['sizes']) : [];

        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
    }

} catch (Exception $e) {
    error_log("Error en get_product.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
}
?>