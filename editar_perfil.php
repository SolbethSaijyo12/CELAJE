<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /CELAJE/login.php');
    exit;
}
include 'includes/db.php'; // Asegúrate de que la ruta sea correcta

$user = null;
$message = '';
$message_type = '';

// Obtener datos actuales del usuario
$stmt = $conn->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die('Usuario no encontrado.');
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';

    // Validación básica
    if (empty($first_name) || empty($last_name)) {
        $message = 'Todos los campos son obligatorios.';
        $message_type = 'error';
    } else {
        $update_stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $first_name, $last_name, $_SESSION['user_id']);

        if ($update_stmt->execute()) {
            // Actualizar el nombre de usuario en la sesión
            $_SESSION['username'] = $first_name . ' ' . $last_name;
            // Actualizar los datos en la variable $user para que el formulario muestre los cambios inmediatamente
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $message = 'Perfil actualizado exitosamente.';
            $message_type = 'success';
        } else {
            $message = 'Error al actualizar el perfil: ' . $update_stmt->error;
            $message_type = 'error';
        }
        $update_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - CELAJE</title>
    <link rel="stylesheet" href="/CELAJE/css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; // Asegúrate de que la ruta sea correcta ?>

    <main class="container" style="max-width: 800px; margin: 40px auto; padding: 20px;">
        <section class="edit-profile-section" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h1 style="font-size: 2rem; margin-bottom: 25px; text-align: center;">Editar Mi Perfil</h1>

            <?php if ($message): ?>
                <div class="<?= $message_type ?>-message" style="padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; color: white; background-color: <?= $message_type === 'success' ? '#28a745' : '#dc3545' ?>;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form action="editar_perfil.php" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="first_name" style="display: block; margin-bottom: 8px; font-weight: bold;">Nombre:</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem;">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="last_name" style="display: block; margin-bottom: 8px; font-weight: bold;">Apellido:</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem;">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: bold;">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="width: 100%; padding: 10px; border: 1px solid #eee; border-radius: 5px; background-color: #f9f9f9; color: #666; font-size: 1rem;">
                    <small style="color: #6c757d; margin-top: 5px; display: block;">El email no se puede cambiar desde aquí.</small>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; background-color: var(--primary-color); color: white; border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer; transition: background-color 0.3s ease;">Guardar Cambios</button>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php'; // Asegúrate de que la ruta sea correcta ?>
</body>
</html>