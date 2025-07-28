<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

include '../includes/db.php'; // Ajusta la ruta si es necesario

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT id, alias, street, exterior_number, interior_number, colony, postal_code, city, state, country, phone_number, is_default FROM addresses WHERE user_id = ? ORDER BY is_default DESC, alias ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $addresses = [];
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }

    echo json_encode(['success' => true, 'addresses' => $addresses]);

    $stmt->close();
} catch (Exception $e) {
    error_log("Error al obtener direcciones: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al cargar las direcciones.']);
} finally {
    if ($conn) {
        $conn->close();
    }
}
?>