<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Debes iniciar sesión';
    echo json_encode($response);
    exit;
}

$order_id = intval($_GET['id']);

if ($order_id <= 0) {
    $response['error'] = 'ID de pedido inválido';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Verificar que el pedido pertenece al usuario
    $stmt = $conn->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
    
    if (!$order) {
        $response['error'] = 'Pedido no encontrado';
        echo json_encode($response);
        exit;
    }
    
    if ($order['status'] !== 'pendiente') {
        $response['error'] = 'Solo se pueden cancelar pedidos pendientes';
        echo json_encode($response);
        exit;
    }
    
    // Actualizar estado del pedido
    $stmt = $conn->prepare("UPDATE orders SET status = 'cancelado' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Error al cancelar pedido';
    }
    
    $stmt->close();

} catch (Exception $e) {
    error_log("Error en cancel_order.php: " . $e->getMessage());
    $response['error'] = 'Error interno del servidor';
}

echo json_encode($response);
?>