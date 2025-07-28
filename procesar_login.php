<?php
session_start();
// --- INICIO DE LA MODIFICACIÓN DE RUTAS DE INCLUSIÓN ---
// Usamos $_SERVER['DOCUMENT_ROOT'] para construir rutas absolutas
// Esto asume que tu proyecto CELAJE está directamente en htdocs (e.g., C:\xampp\htdocs\CELAJE\)
// Asegúrate de que la ruta sea correcta para tu entorno.
// Si tu proyecto está en C:\xampp\htdocs\CELAJE\, esta ruta es correcta.
// Si no, ajústala (ej. si está en C:\xampp\htdocs\mi_proyecto\CELAJE\, sería '/mi_proyecto/CELAJE/includes/db.php')
include $_SERVER['DOCUMENT_ROOT'] . '/CELAJE/includes/db.php';
// --- FIN DE LA MODIFICACIÓN ---

// Verificar si la conexión a la base de datos se estableció correctamente
if ($conn === null) {
    $_SESSION['login_error'] = "Error de conexión a la base de datos. Por favor, inténtalo de nuevo más tarde.";
    header('Location: login.php');
    exit;
}

// Longitud del código (6 caracteres alfanuméricos)
define('CODE_LENGTH', 6);

// Función para generar un código alfanumérico seguro (la misma que en pagos.php)
function generateSecureCode($length = CODE_LENGTH) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Utilizar un operador ternario para asegurar que $_POST['username'] y $_POST['password'] existen
    // antes de intentar acceder a ellos y pasarlos a trim y real_escape_string.
    // Aunque el empty() posterior también lo manejaría, esto es más explícito.
    $username = isset($_POST['username']) ? $conn->real_escape_string(trim($_POST['username'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember = isset($_POST['remember']);

    // Validación básica
    if(empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Por favor completa todos los campos";
        header('Location: login.php');
        exit;
    }

    // Buscar usuario por username o email
    // Es mejor buscar solo por username si es el campo de login principal para evitar ambigüedades.
    // Si quieres que el usuario pueda logearse con email o username, la consulta está bien.
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    if ($stmt === false) {
        error_log("CELAJE Login Error: Falló la preparación de la consulta de usuario: " . $conn->error);
        $_SESSION['login_error'] = "Error interno del servidor. Inténtalo de nuevo más tarde.";
        header('Location: login.php');
        exit;
    }
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña
        if (password_verify($password, $user['password'])) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Limpiar cualquier estado previo del código de seguridad en la sesión
            unset($_SESSION['code_is_valid']);
            unset($_SESSION['code_display_start_time']);

            // ¡CRÍTICO!: Generar y almacenar el nuevo código de seguridad en la sesión
            // Este es el código que el usuario deberá ingresar en pagos.php
            $_SESSION['security_code'] = generateSecureCode();
            
            // Recordar sesión
            if($remember) {
                $token = bin2hex(random_bytes(32));
                $expire = time() + 60 * 60 * 24 * 30; // 30 días
                
                setcookie('remember_token', $token, $expire, "/");
                // Es importante que la tabla 'users' tenga una columna 'remember_token'
                $update_token_stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                if ($update_token_stmt === false) {
                     error_log("CELAJE Login Error: Falló la preparación para actualizar remember_token: " . $conn->error);
                     // No es un error crítico para el login, pero es bueno registrarlo.
                } else {
                    $update_token_stmt->bind_param("si", $token, $user['id']);
                    $update_token_stmt->execute();
                    $update_token_stmt->close();
                }
            }
            
            // --- INICIO DE LA MODIFICACIÓN DE REDIRECCIÓN ---
            // Redirigir a la página principal de la tienda
            header('Location: index.php');
            // --- FIN DE LA MODIFICACIÓN ---
            exit;
        }
    }

    // Si las credenciales son inválidas o el usuario no se encuentra
    $_SESSION['login_error'] = "Credenciales inválidas";
    header('Location: /login.php');
    exit;
}
?>