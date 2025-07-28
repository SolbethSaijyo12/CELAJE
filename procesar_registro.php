<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validaciones
    $errors = [];
    
    if(empty($username) || strlen($username) < 4) {
        $errors[] = "El usuario debe tener al menos 4 caracteres";
    }
    
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo no es válido";
    }
    
    if($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden";
    }
    
    if(strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres";
    }
    
    // Verificar si el usuario o email ya existen
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $errors[] = "El nombre de usuario o correo ya está registrado";
    }
    
    if(!empty($errors)) {
        $_SESSION['reg_error'] = implode("<br>", $errors);
        header('Location: registro.php');
        exit;
    }

    // Hash de la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registro exitoso. Por favor inicia sesión";
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['reg_error'] = "Error: " . $conn->error;
        header('Location: registro.php');
        exit;
    }
}
?>