<?php
// File: ajax/add_to_wishlist.php
session_start(); // Inicia la sesión para acceder a user_id y guest_wishlist
include '../includes/db.php'; // Incluye el archivo de conexión a la base de datos

// --- Configuración de reporte de errores (Solo para desarrollo, QUITAR EN PRODUCCIÓN) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Indica que la respuesta será en formato JSON

$response = ['success' => false, 'message' => '', 'action' => '']; // Inicializa la respuesta

// Si la conexión a la base de datos falló en db.php, retorna un error
if (!$conn) {
    $response['message'] = 'Error de conexión a la base de datos.';
    echo json_encode($response);
    error_log("add_to_wishlist.php: Conexión a DB fallida al inicio del script.");
    die(); // Detiene la ejecución para evitar más errores o salida no JSON
}

// Verifica si se proporcionó el ID del producto
if (!isset($_POST['product_id'])) {
    $response['message'] = 'ID de producto no proporcionado.';
    echo json_encode($response);
    die(); // Detiene la ejecución
}

$productId = intval($_POST['product_id']); // Convierte el ID del producto a entero
$userId = $_SESSION['user_id'] ?? null;    // Obtiene el user_id de la sesión si está logueado

if ($userId) {
    // --- Usuario logueado: Gestionar favoritos en la base de datos ---
    try {
        // Prepara la consulta para verificar si el producto ya está en la wishlist del usuario
        $stmt_check = $conn->prepare("SELECT id FROM user_wishlist WHERE user_id = ? AND product_id = ?");
        if ($stmt_check === false) {
            throw new Exception("Error al preparar la consulta de verificación de wishlist: " . $conn->error);
        }
        $stmt_check->bind_param("ii", $userId, $productId);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Si el producto ya está en favoritos, lo eliminamos
            $stmt_delete = $conn->prepare("DELETE FROM user_wishlist WHERE user_id = ? AND product_id = ?");
            if ($stmt_delete === false) {
                throw new Exception("Error al preparar la consulta de eliminación de wishlist: " . $conn->error);
            }
            $stmt_delete->bind_param("ii", $userId, $productId);
            if ($stmt_delete->execute()) {
                $response['success'] = true;
                $response['message'] = 'Producto eliminado de favoritos.';
                $response['action'] = 'removed';
            } else {
                $response['message'] = 'Error al eliminar el producto de favoritos.';
            }
            $stmt_delete->close();
        } else {
            // Si el producto no está en favoritos, lo añadimos
            $stmt_insert = $conn->prepare("INSERT INTO user_wishlist (user_id, product_id) VALUES (?, ?)");
            if ($stmt_insert === false) {
                throw new Exception("Error al preparar la consulta de inserción de wishlist: " . $conn->error);
            }
            $stmt_insert->bind_param("ii", $userId, $productId);
            if ($stmt_insert->execute()) {
                $response['success'] = true;
                $response['message'] = 'Producto añadido a favoritos.';
                $response['action'] = 'added';
            } else {
                $response['message'] = 'Error al añadir el producto a favoritos.';
            }
            $stmt_insert->close();
        }
        $stmt_check->close();

    } catch (Exception $e) {
        // Captura cualquier excepción de la base de datos
        error_log("Error en add_to_wishlist.php (DB): " . $e->getMessage());
        $response['message'] = 'Error interno del servidor al gestionar favoritos: ' . $e->getMessage();
    }
} else {
    // --- Usuario no logueado: Gestionar favoritos en la sesión (como invitado) ---
    // Inicializa el array de favoritos de invitado si no existe
    if (!isset($_SESSION['guest_wishlist'])) {
        $_SESSION['guest_wishlist'] = [];
    }

    $productIdsInWishlist = $_SESSION['guest_wishlist'];

    if (in_array($productId, $productIdsInWishlist)) {
        // Si el producto ya está en favoritos de la sesión, lo elimina
        $_SESSION['guest_wishlist'] = array_diff($productIdsInWishlist, [$productId]);
        $_SESSION['guest_wishlist'] = array_values($_SESSION['guest_wishlist']); // Reindexa el array
        $response['success'] = true;
        $response['message'] = 'Producto eliminado de favoritos (sesión).';
        $response['action'] = 'removed';
    } else {
        // Si el producto no está en favoritos de la sesión, lo añade
        $_SESSION['guest_wishlist'][] = $productId;
        $response['success'] = true;
        $response['message'] = 'Producto añadido a favoritos (sesión).';
        $response['action'] = 'added';
    }
}

// Cierra la conexión a la base de datos al finalizar el script
if ($conn) {
    $conn->close();
}

echo json_encode($response);
?>