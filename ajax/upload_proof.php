<?php
session_start();
require_once 'includes/db.php'; // Your database connection file

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_proof'])) {
    $user_id = $_SESSION['user_id'] ?? null;
    $order_id = $_POST['order_id'] ?? null; // Get order ID from POST

    if (!$user_id) {
        $response['message'] = 'Usuario no autenticado.';
        echo json_encode($response);
        exit();
    }

    if (!$order_id) {
        $response['message'] = 'ID de pedido no proporcionado.';
        echo json_encode($response);
        exit();
    }

    $target_dir = "uploads/payment_proofs/";
    // Ensure the directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = uniqid('proof_') . '_' . basename($_FILES["payment_proof"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["payment_proof"]["tmp_name"]);
    if($check !== false) {
        // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $response['message'] = 'El archivo no es una imagen.';
        $uploadOk = 0;
    }

    // Check file size (e.g., max 5MB)
    if ($_FILES["payment_proof"]["size"] > 5000000) {
        $response['message'] = 'El archivo es demasiado grande (máx. 5MB).';
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" && $imageFileType != "pdf") { // Added PDF support
        $response['message'] = 'Solo se permiten archivos JPG, JPEG, PNG, GIF y PDF.';
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo json_encode($response);
        exit();
    } else {
        if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $target_file)) {
            // Update the order in the database with the payment proof path and status
            $stmt = $conn->prepare("UPDATE orders SET payment_proof_path = ?, status = 'processing' WHERE id = ? AND user_id = ? AND payment_method = 'bank_transfer'");
            if ($stmt) {
                $stmt->bind_param("sii", $target_file, $order_id, $user_id);
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Comprobante de pago subido con éxito. Tu pedido ahora está en procesamiento.';
                } else {
                    $response['message'] = 'Error al actualizar el estado del pedido en la base de datos: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = 'Error al preparar la consulta para actualizar el pedido: ' . $conn->error;
            }
        } else {
            $response['message'] = 'Error al subir el archivo.';
        }
    }
} else {
    $response['message'] = 'Solicitud inválida.';
}

echo json_encode($response);
?>