<?php
// File: ajax/get_wishlist_items.php
session_start(); // Inicia la sesión
include '../includes/db.php'; // Incluye el archivo de conexión a la base de datos

// --- Configuración de reporte de errores (Solo para desarrollo, QUITAR EN PRODUCCIÓN) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Indica que la respuesta será en formato JSON

$response = ['success' => false, 'products' => [], 'message' => '']; // Inicializa la respuesta

// Si la conexión a la base de datos falló en db.php, retorna un error
if (!$conn) {
    $response['message'] = 'Error de conexión a la base de datos.';
    echo json_encode($response);
    error_log("get_wishlist_items.php: Conexión a DB fallida al inicio del script.");
    die(); // Detiene la ejecución
}

// Si el usuario no ha iniciado sesión, no puede ver sus favoritos basados en DB
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Debes iniciar sesión para ver tus favoritos.';
    echo json_encode($response);
    die(); // Detiene la ejecución
}

$user_id = $_SESSION['user_id']; // Obtiene el user_id de la sesión

try {
    // Prepara la consulta para obtener los detalles de los productos en la wishlist del usuario
    // Las columnas de opciones de producto (available_colors, etc.) se seleccionan aquí.
    // Usamos AS para los alias (ej. `colors`) para que coincidan con cómo JS los espera.
    $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.price, p.image_path, p.category,
                            p.available_colors AS colors,
                            p.available_materials AS materials,
                            p.available_sizes AS sizes,
                            p.available_closures AS closures,
                            p.available_laminations AS laminations,
                            p.available_sheets AS sheets,
                            p.available_sheet_cuts AS sheet_cuts,
                            p.available_paper_types AS paper_types,
                            p.available_vinyl_types AS vinyl_types,
                            p.available_margins AS margins,
                            p.available_image_positions AS image_positions,
                            p.stock
                            FROM user_wishlist uw
                            JOIN products p ON uw.product_id = p.id
                            WHERE uw.user_id = ?");
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta de wishlist items: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Asegúrate de que los campos de opciones (colors, materials, sizes, etc.) sean arrays
        // y que los valores vacíos no causen problemas con explode.
        $row['colors'] = ($row['colors'] !== null && $row['colors'] !== '') ? array_map('trim', explode(',', $row['colors'])) : [];
        $row['materials'] = ($row['materials'] !== null && $row['materials'] !== '') ? array_map('trim', explode(',', $row['materials'])) : [];
        $row['sizes'] = ($row['sizes'] !== null && $row['sizes'] !== '') ? array_map('trim', explode(',', $row['sizes'])) : [];
        $row['closures'] = ($row['closures'] !== null && $row['closures'] !== '') ? array_map('trim', explode(',', $row['closures'])) : [];
        $row['laminations'] = ($row['laminations'] !== null && $row['laminations'] !== '') ? array_map('trim', explode(',', $row['laminations'])) : [];
        $row['sheets'] = ($row['sheets'] !== null && $row['sheets'] !== '') ? array_map('trim', explode(',', $row['sheets'])) : [];
        $row['sheet_cuts'] = ($row['sheet_cuts'] !== null && $row['sheet_cuts'] !== '') ? array_map('trim', explode(',', $row['sheet_cuts'])) : [];
        $row['paper_types'] = ($row['paper_types'] !== null && $row['paper_types'] !== '') ? array_map('trim', explode(',', $row['paper_types'])) : [];
        $row['vinyl_types'] = ($row['vinyl_types'] !== null && $row['vinyl_types'] !== '') ? array_map('trim', explode(',', $row['vinyl_types'])) : [];
        $row['margins'] = ($row['margins'] !== null && $row['margins'] !== '') ? array_map('trim', explode(',', $row['margins'])) : [];
        $row['image_positions'] = ($row['image_positions'] !== null && $row['image_positions'] !== '') ? array_map('trim', explode(',', $row['image_positions'])) : [];

        $products[] = $row; // Añade el producto procesado al array
    }

    $stmt->close();
    $response['success'] = true;
    $response['products'] = $products;

} catch (Exception $e) {
    // Captura cualquier excepción de la base de datos
    error_log("Error en get_wishlist_items.php: " . $e->getMessage());
    $response['message'] = 'Error interno del servidor al obtener favoritos: ' . $e->getMessage();
}

// Cierra la conexión a la base de datos al finalizar el script
if ($conn) {
    $conn->close();
}

echo json_encode($response);
?>