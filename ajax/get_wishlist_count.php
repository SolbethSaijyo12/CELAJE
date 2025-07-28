<?php
// File: ajax/get_wishlist_count.php
session_start(); // Inicia la sesión
include '../includes/db.php'; // Incluye el archivo de conexión a la base de datos

// --- Configuración de reporte de errores (Solo para desarrollo, QUITAR EN PRODUCCIÓN) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Indica que la respuesta será en formato JSON

$response = ['success' => false, 'count' => 0, 'message' => '']; // Inicializa la respuesta

// Si la conexión a la base de datos falló en db.php, retorna un error
if (!$conn) {
    $response['message'] = 'Error de conexión a la base de datos.';
    echo json_encode($response);
    error_log("get_wishlist_count.php: Conexión a DB fallida al inicio del script.");
    die(); // Detiene la ejecución
}

$userId = $_SESSION['user_id'] ?? null; // Obtiene el user_id de la sesión

if ($userId) {
    // --- Usuario logueado: Obtener el conteo de la base de datos ---
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM user_wishlist WHERE user_id = ?");
        if ($stmt === false) {
            throw new Exception("Error al preparar la consulta de conteo de wishlist: " . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $response['count'] = $row['count']; // Asigna el conteo
        $response['success'] = true;
        $stmt->close();
    } catch (Exception $e) {
        // Captura cualquier excepción de la base de datos
        error_log("Error en get_wishlist_count.php (DB): " . $e->getMessage());
        $response['message'] = 'Error interno del servidor: ' . $e->getMessage();
    }
} else {
    // --- Usuario no logueado: Obtener el conteo de la sesión ---
    $response['count'] = count($_SESSION['guest_wishlist'] ?? []); // Cuenta los elementos en el array de sesión
    $response['success'] = true;
}

// Cierra la conexión a la base de datos al finalizar el script
if ($conn) {
    $conn->close();
}

echo json_encode($response);
?>