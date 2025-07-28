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

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
    $response['error'] = 'Las contraseñas no coinciden';
    echo json_encode($response);
    exit;
}

try {
    // Verificar contraseña actual
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!password_verify($current_password, $user['password'])) {
        $response['error'] = 'Contraseña actual incorrecta';
        echo json_encode($response);
        exit;
    }
    
    // Actualizar contraseña
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Error al actualizar contraseña';
    }
    
    $stmt->close();

} catch (Exception $e) {
    error_log("Error en change_password.php: " . $e->getMessage());
    $response['error'] = 'Error interno del servidor';
}

echo json_encode($response);
?>