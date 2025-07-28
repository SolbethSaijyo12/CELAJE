<?php 
session_start();

// Redirigir si ya está autenticado
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - CELAJE</title>
    <link rel="stylesheet" href="css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .btn-login {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <?php if(isset($_SESSION['login_error'])): ?>
            <div class="error-message">
                <?= $_SESSION['login_error'] ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="procesar_login.php" method="POST">
            <div class="form-group">
                <label for="username">Usuario o Email:</label>
                <input type="text" id="username" name="username" placeholder="Usuario o email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember"> Recordar sesión
                </label>
            </div>
            
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
        
        <div class="register-link">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>