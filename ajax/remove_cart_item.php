<?php
session_start();
// Asegúrate que esta ruta sea correcta para tu estructura de carpetas
include '../includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'] ?? null;

    if (!$item_id) {
        $response['message'] = 'ID de item no proporcionado.';
        echo json_encode($response);
        exit;
    }

    if (isset($_SESSION['user_id'])) {
        // Usuario logueado: Eliminar de la base de datos
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $item_id, $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Producto eliminado del carrito.';
            } else {
                $response['message'] = 'No se encontró el producto en el carrito o ya fue eliminado.';
            }
        } else {
            $response['message'] = 'Error al eliminar el producto de la DB: ' . $stmt->error;
        }

    } else {
        // Usuario no logueado: Eliminar de la sesión
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $item_id) {
                unset($_SESSION['cart'][$key]);
                $found = true;
                break;
            }
        }

        if ($found) {
            $response['success'] = true;
            $response['message'] = 'Producto eliminado del carrito de sesión.';
        } else {
            $response['message'] = 'Producto no encontrado en el carrito de sesión.';
        }
    }
} else {
    $response['message'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?>