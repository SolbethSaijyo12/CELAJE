<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false];

if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Obtener conteo de pedidos
    $stmt = $conn->prepare("SELECT COUNT(*) AS orders FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_assoc()['orders'];
    $stmt->close();

    // Obtener conteo de favoritos
    $stmt = $conn->prepare("SELECT COUNT(*) AS wishlist FROM user_wishlist WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $wishlist = $stmt->get_result()->fetch_assoc()['wishlist'];
    $stmt->close();

    $response = [
        'success' => true,
        'orders' => $orders,
        'wishlist' => $wishlist
    ];

} catch (Exception $e) {
    error_log("Error en get_user_stats.php: " . $e->getMessage());
}

echo json_encode($response);
?>