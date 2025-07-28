<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - CELAJE</title>
    <link rel="stylesheet" href="css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        
        .register-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
        }
        
        .btn-register {
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
        
        .btn-register:hover {
            background: var(--primary-dark);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="register-container">
        <h2>Registrarse</h2>
        
        <?php if(isset($_SESSION['reg_error'])): ?>
            <div class="error-message">
                <?= $_SESSION['reg_error'] ?>
                <?php unset($_SESSION['reg_error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="procesar_registro.php" method="POST">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>
                <small>Mínimo 4 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
                <small>Mínimo 8 caracteres, incluir números y letras</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite tu contraseña" required>
            </div>
            
            <button type="submit" class="btn-register">Registrarse</button>
        </form>
        
        <div class="login-link">
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>