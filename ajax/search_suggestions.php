<?php
include '../includes/db.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$suggestions = [];

if (strlen($query) >= 2) {
    $search_term = '%' . $query . '%';
    $stmt = $conn->prepare("SELECT id, name FROM products WHERE name LIKE ? AND is_active = 1 LIMIT 5");
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
}

echo json_encode(['suggestions' => $suggestions]);
?>