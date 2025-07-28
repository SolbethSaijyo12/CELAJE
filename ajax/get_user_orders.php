<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'orders' => []];

if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $query = "SELECT id, total, status, created_at, shipping_address, payment_method 
              FROM orders 
              WHERE user_id = ?";
    
    $params = ["i", $user_id];
    
    if ($status !== 'all') {
        $query .= " AND status = ?";
        $params[0] .= "s";
        $params[] = $status;
    }
    
    if (!empty($search)) {
        $query .= " AND (id LIKE ? OR shipping_address LIKE ?)";
        $params[0] .= "ss";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($query);
    
    if (count($params) > 1) {
        $stmt->bind_param(...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response['orders'][] = [
            'id' => $row['id'],
            'total' => number_format($row['total'], 2),
            'status' => ucfirst($row['status']),
            'date' => date('d M Y', strtotime($row['created_at'])),
            'shipping_address' => $row['shipping_address'],
            'payment_method' => $row['payment_method']
        ];
    }
    
    $response['success'] = true;
    $stmt->close();

} catch (Exception $e) {
    error_log("Error en get_user_orders.php: " . $e->getMessage());
}

echo json_encode($response);
?>