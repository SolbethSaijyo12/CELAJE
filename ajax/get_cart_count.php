<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

$count = 0;

try {
    if (isset($_SESSION['user_id'])) {
        // User is logged in - get from database
        $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $count = $result['total'] ?? 0;
    } else {
        // User not logged in - get from session
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }
        }
    }
    
    echo json_encode(['success' => true, 'count' => $count]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'count' => 0]);
}
?>