<?php
// ajax/update_cart_item.php
session_start();
include '../includes/db.php'; // Asegúrate que esta ruta sea correcta para tu estructura de carpetas

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'] ?? null;
    $new_quantity = intval($_POST['quantity'] ?? 0);

    // Capturar todos los nuevos campos de personalización (igual que en add_to_cart.php)
    $new_color = $_POST['color'] ?? null;
    $new_material = $_POST['material'] ?? null;
    $new_size = $_POST['size'] ?? null;
    
    // Opciones específicas por categoría
    $closure = $_POST['closure'] ?? null;
    $lamination = isset($_POST['lamination']) ? intval($_POST['lamination']) : 0;
    $sheet = isset($_POST['sheet']) ? intval($_POST['sheet']) : 0;
    $sheet_cut = isset($_POST['sheet_cut']) ? intval($_POST['sheet_cut']) : 0;
    $paper_type = $_POST['paper_type'] ?? null;
    $form_type = $_POST['form_type'] ?? null;
    $vinyl_type = $_POST['vinyl_type'] ?? null;
    $margin = isset($_POST['margin']) ? intval($_POST['margin']) : 0;
    $image_position = $_POST['image_position'] ?? null;

    // Manejo de imágenes de artista
    $artist_image_1 = $_POST['artist_image_1'] ?? null;
    if (isset($_FILES['artist_image_1']) && $_FILES['artist_image_1']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/artist_images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_ext = pathinfo($_FILES['artist_image_1']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('artist_img_') . '.' . $file_ext;
        $upload_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['artist_image_1']['tmp_name'], $upload_path)) {
            $artist_image_1 = 'uploads/artist_images/' . $file_name; // Ruta relativa para guardar en DB
        } else {
            error_log("Error subiendo artist_image_1: " . $_FILES['artist_image_1']['error']);
        }
    }

    $artist_image_2 = $_POST['artist_image_2'] ?? null;
    if (isset($_FILES['artist_image_2']) && $_FILES['artist_image_2']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/artist_images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_ext = pathinfo($_FILES['artist_image_2']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('artist_img_') . '.' . $file_ext;
        $upload_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['artist_image_2']['tmp_name'], $upload_path)) {
            $artist_image_2 = 'uploads/artist_images/' . $file_name;
        } else {
            error_log("Error subiendo artist_image_2: " . $_FILES['artist_image_2']['error']);
        }
    }

    $artist_image_3 = $_POST['artist_image_3'] ?? null;
    if (isset($_FILES['artist_image_3']) && $_FILES['artist_image_3']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/artist_images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_ext = pathinfo($_FILES['artist_image_3']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('artist_img_') . '.' . $file_ext;
        $upload_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['artist_image_3']['tmp_name'], $upload_path)) {
            $artist_image_3 = 'uploads/artist_images/' . $file_name;
        } else {
            error_log("Error subiendo artist_image_3: " . $_FILES['artist_image_3']['error']);
        }
    }

    $dedication = $_POST['dedication'] ?? null;
    $custom_card_text = $_POST['custom_card_text'] ?? null;

    $custom_card_image = $_POST['custom_card_image'] ?? null;
    if (isset($_FILES['custom_card_image']) && $_FILES['custom_card_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/custom_cards/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_ext = pathinfo($_FILES['custom_card_image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('card_img_') . '.' . $file_ext;
        $upload_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['custom_card_image']['tmp_name'], $upload_path)) {
            $custom_card_image = 'uploads/custom_cards/' . $file_name;
        } else {
            error_log("Error subiendo custom_card_image: " . $_FILES['custom_card_image']['error']);
        }
    }
    
    // Opciones de regalo
    $is_gift = isset($_POST['is_gift']) ? intval($_POST['is_gift']) : 0; // Se asume 0 si no se envía o es inválido
    $gift_dedication = $_POST['gift_dedication'] ?? null;
    $gift_bag = isset($_POST['gift_bag']) ? intval($_POST['gift_bag']) : 0;
    $gift_wrap = isset($_POST['gift_wrap']) ? intval($_POST['gift_wrap']) : 0;
    $gift_card = isset($_POST['gift_card']) ? intval($_POST['gift_card']) : 0;

    if (!$item_id || $new_quantity < 0) {
        $response['message'] = 'Datos inválidos. ID de ítem o cantidad no válidos.';
        echo json_encode($response);
        exit;
    }

    // --- VERIFICACIÓN DE CONEXIÓN A LA BD ---
    if (!isset($conn) || ($conn instanceof mysqli && $conn->connect_error)) {
        error_log("Error en update_cart_item.php: No se pudo establecer conexión con la base de datos.");
        echo json_encode(['success' => false, 'error' => 'Error interno del servidor al conectar con DB.']);
        exit;
    }
    // --- FIN DE VERIFICACIÓN DE CONEXIÓN ---

    if (isset($_SESSION['user_id'])) {
        // Usuario logueado: Actualizar en la base de datos
        $user_id = $_SESSION['user_id'];
        
        if ($new_quantity == 0) {
            // Si la nueva cantidad es 0, eliminar el item
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            if ($stmt === false) {
                throw new Exception("Error al preparar la eliminación: " . $conn->error);
            }
            $stmt->bind_param("ii", $item_id, $user_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Producto eliminado del carrito.';
                } else {
                    $response['message'] = 'No se encontró el producto en el carrito o ya fue eliminado.';
                }
            } else {
                $response['message'] = 'Error al eliminar el producto de la DB: ' . $stmt->error;
            }

        } else {
            // Si la cantidad es > 0, actualizar el item
            // ACTUALIZAR TODOS LOS CAMPOS DE PERSONALIZACIÓN Y REGALO
            $stmt = $conn->prepare("UPDATE cart SET 
                                    quantity = ?, color = ?, material = ?, size = ?, is_gift = ?,
                                    closure = ?, lamination = ?, sheet = ?, sheet_cut = ?, paper_type = ?, 
                                    form_type = ?, vinyl_type = ?, margin = ?, image_position = ?, 
                                    artist_image_1 = ?, artist_image_2 = ?, artist_image_3 = ?,
                                    dedication = ?, custom_card_text = ?, custom_card_image = ?,
                                    gift_dedication = ?, gift_bag = ?, gift_wrap = ?, gift_card = ?
                                    WHERE id = ? AND user_id = ?");
            if ($stmt === false) {
                throw new Exception("Error al preparar la consulta de actualización: " . $conn->error);
            }

            // Cadena de tipos (26 parámetros):
            // 1i (quantity)
            // 3s (color, material, size)
            // 1i (is_gift)
            // 1s (closure)
            // 3i (lamination, sheet, sheet_cut)
            // 3s (paper_type, form_type, vinyl_type)
            // 1i (margin)
            // 1s (image_position)
            // 3s (artist_image_1,2,3)
            // 3s (dedication, custom_card_text, custom_card_image)
            // 1s (gift_dedication)
            // 3i (gift_bag, gift_wrap, gift_card)
            // 2i (item_id, user_id - para el WHERE)
            // Total: 1 + 3 + 1 + 1 + 3 + 3 + 1 + 1 + 3 + 3 + 1 + 3 + 2 = 26
            // "isssisiiisssisssssssiiiiss"
            $stmt->bind_param("isssisiiisssisssssssiiiiss",
                $new_quantity, $new_color, $new_material, $new_size, $is_gift,
                $closure, $lamination, $sheet, $sheet_cut, $paper_type,
                $form_type, $vinyl_type, $margin, $image_position,
                $artist_image_1, $artist_image_2, $artist_image_3,
                $dedication, $custom_card_text, $custom_card_image,
                $gift_dedication, $gift_bag, $gift_wrap, $gift_card,
                $item_id, $user_id
            );

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Carrito actualizado en DB.';
            } else {
                $response['message'] = 'Error al actualizar el carrito en la DB: ' . $stmt->error;
            }
        }
    } else {
        // Usuario no logueado: Actualizar en la sesión
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            // Compara con el 'id' único generado para la sesión
            if ($item['id'] == $item_id) {
                if ($new_quantity == 0) {
                    unset($_SESSION['cart'][$key]); // Eliminar si la cantidad es 0
                } else {
                    $_SESSION['cart'][$key]['quantity'] = $new_quantity;
                    // Actualizar las opciones de personalización en la sesión
                    // Usar el nuevo valor si se proporcionó, de lo contrario, mantener el anterior
                    $_SESSION['cart'][$key]['color'] = $new_color ?? $item['color'];
                    $_SESSION['cart'][$key]['material'] = $new_material ?? $item['material'];
                    $_SESSION['cart'][$key]['size'] = $new_size ?? $item['size'];
                    
                    // Actualizar todos los campos de personalización y regalo en la sesión
                    $_SESSION['cart'][$key]['closure'] = $closure ?? $item['closure'];
                    $_SESSION['cart'][$key]['lamination'] = $lamination;
                    $_SESSION['cart'][$key]['sheet'] = $sheet;
                    $_SESSION['cart'][$key]['sheet_cut'] = $sheet_cut;
                    $_SESSION['cart'][$key]['paper_type'] = $paper_type ?? $item['paper_type'];
                    $_SESSION['cart'][$key]['form_type'] = $form_type ?? $item['form_type'];
                    $_SESSION['cart'][$key]['vinyl_type'] = $vinyl_type ?? $item['vinyl_type'];
                    $_SESSION['cart'][$key]['margin'] = $margin;
                    $_SESSION['cart'][$key]['image_position'] = $image_position ?? $item['image_position'];
                    
                    // Las imágenes en sesión se manejan un poco diferente: si se subió una nueva, esa es; si no, se mantiene la anterior.
                    // Para el caso de la sesión, los archivos de $_FILES ya estarían cargados en el servidor temporalmente si se subieron.
                    // Aquí simplemente guardamos la referencia a la nueva ruta o mantenemos la existente.
                    $_SESSION['cart'][$key]['artist_image_1'] = ($artist_image_1 && strpos($artist_image_1, 'uploads/') === 0) ? $artist_image_1 : ($item['artist_image_1'] ?? null);
                    $_SESSION['cart'][$key]['artist_image_2'] = ($artist_image_2 && strpos($artist_image_2, 'uploads/') === 0) ? $artist_image_2 : ($item['artist_image_2'] ?? null);
                    $_SESSION['cart'][$key]['artist_image_3'] = ($artist_image_3 && strpos($artist_image_3, 'uploads/') === 0) ? $artist_image_3 : ($item['artist_image_3'] ?? null);

                    $_SESSION['cart'][$key]['dedication'] = $dedication ?? $item['dedication'];
                    $_SESSION['cart'][$key]['custom_card_text'] = $custom_card_text ?? $item['custom_card_text'];
                    $_SESSION['cart'][$key]['custom_card_image'] = ($custom_card_image && strpos($custom_card_image, 'uploads/') === 0) ? $custom_card_image : ($item['custom_card_image'] ?? null);
                    
                    $_SESSION['cart'][$key]['is_gift'] = $is_gift;
                    $_SESSION['cart'][$key]['gift_dedication'] = $gift_dedication ?? $item['gift_dedication'];
                    $_SESSION['cart'][$key]['gift_bag'] = $gift_bag;
                    $_SESSION['cart'][$key]['gift_wrap'] = $gift_wrap;
                    $_SESSION['cart'][$key]['gift_card'] = $gift_card;

                }
                $found = true;
                break;
            }
        }

        if ($found) {
            $response['success'] = true;
            $response['message'] = 'Carrito actualizado en sesión.';
        } else {
            $response['message'] = 'Ítem no encontrado en el carrito de sesión.';
        }
    }
} catch (Exception $e) {
    error_log("Error en update_cart_item.php (excepción): " . $e->getMessage());
    $response['message'] = 'Error interno del servidor: ' . $e->getMessage();
}

echo json_encode($response);
?>