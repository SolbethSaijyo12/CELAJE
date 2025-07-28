<?php
session_start();
// Asegúrate que esta ruta sea correcta para tu estructura de carpetas
include '../includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['user_id'])) {
        // Usuario logueado: Vaciar de la base de datos
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Carrito vaciado exitosamente en la DB.';
        } else {
            $response['message'] = 'Error al vaciar el carrito en la DB: ' . $stmt->error;
        }

    } else {
        // Usuario no logueado: Vaciar de la sesión
        unset($_SESSION['cart']);
        $response['success'] = true;
        $response['message'] = 'Carrito vaciado exitosamente de la sesión.';
    }
} else {
    $response['message'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?>