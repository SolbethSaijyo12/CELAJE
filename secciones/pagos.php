<?php
session_start();
// --- INICIO DE LA MODIFICACIÓN DE RUTAS DE INCLUSIÓN ---
// Usamos $_SERVER['DOCUMENT_ROOT'] para construir rutas absolutas
// Esto asume que tu proyecto CELAJE está directamente en htdocs (e.g., C:\xampp\htdocs\CELAJE\)
include $_SERVER['DOCUMENT_ROOT'] . '/CELAJE/includes/db.php'; // Ruta absoluta para db.php
// --- FIN DE LA MODIFICACIÓN ---

// Es buena práctica verificar si $conn se inicializó correctamente aquí también
// Si db.php falla, $conn podría ser null, causando un 500 error.
if (!isset($conn) || ($conn instanceof mysqli && $conn->connect_error)) {
    error_log("Error en pagos.php: No se pudo establecer conexión con la base de datos.");
    echo "Error interno del servidor. Por favor, inténtalo de nuevo más tarde. (DB)";
    exit;
}


$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['SCRIPT_NAME']) . "/../";

// Longitud del código (6 caracteres alfanuméricos)
define('CODE_LENGTH', 6);
// Tiempo de validez del código en segundos (3 minutos)
define('CODE_VALIDITY_SECONDS', 3 * 60);

// Función para generar un código alfanumérico seguro
function generateSecureCode($length = CODE_LENGTH) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Verificar si el usuario está logueado
$is_user_logged_in = isset($_SESSION['user_id']);
$show_bank_transfer_details = false;
$code_message = ''; // Mensaje para el usuario (éxito o error)

if ($is_user_logged_in) {
    // Si se envió el formulario con el código
    if (isset($_POST['code_submitted']) && isset($_POST['security_code'])) {
        $user_input_code = trim($_POST['security_code']);

        // Verificar si hay un código en sesión para comparar
        if (isset($_SESSION['security_code']) && strtoupper($user_input_code) === strtoupper($_SESSION['security_code'])) {
            // Código correcto, establecer tiempo de inicio de visualización
            $_SESSION['code_display_start_time'] = time();
            $_SESSION['code_is_valid'] = true;
            $code_message = "Código correcto. Datos visibles por 3 minutos.";
        } else {
            // Código incorrecto
            $_SESSION['code_is_valid'] = false;
            $code_message = "Código incorrecto. Inténtalo de nuevo.";
        }
        // Redirigir para limpiar datos POST y evitar reenvíos
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

    // Lógica para determinar si se deben mostrar los datos bancarios
    if (isset($_SESSION['code_is_valid']) && $_SESSION['code_is_valid'] === true && isset($_SESSION['code_display_start_time'])) {
        $elapsed_time = time() - $_SESSION['code_display_start_time'];
        if ($elapsed_time < CODE_VALIDITY_SECONDS) {
            $show_bank_transfer_details = true;
        } else {
            // El tiempo ha expirado, invalidar el código y el tiempo de inicio
            unset($_SESSION['code_is_valid']);
            unset($_SESSION['code_display_start_time']);
            $_SESSION['security_code'] = generateSecureCode(); // Generar nuevo código para el próximo intento
            $code_message = "El tiempo para ver los datos bancarios ha expirado. Ingresa un nuevo código.";
        }
    } else {
        // Si no hay código válido o es el primer acceso, generar uno nuevo
        if (!isset($_SESSION['security_code'])) {
             $_SESSION['security_code'] = generateSecureCode();
             $code_message = "Ingresa el código para ver los datos bancarios.";
        } else if (!isset($_SESSION['code_is_valid']) || $_SESSION['code_is_valid'] === false) {
             // Si el código fue incorrecto previamente o expiró y se borró, se mantiene el mensaje de error o expiración
             // y el código en sesión ya debería haberse generado/actualizado al fallar o expirar.
        }
    }

} else {
    // Si no está logueado, limpiar cualquier sesión de código
    unset($_SESSION['security_code']);
    unset($_SESSION['code_display_start_time']);
    unset($_SESSION['code_is_valid']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CELAJE - Métodos de Pago</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/faq.css">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer">
    <meta name="description" content="Conoce todos los métodos de pago disponibles en CELAJE. Tarjetas, transferencias, efectivo y más opciones seguras.">
</head>
<body data-user-logged-in="<?= $is_user_logged_in ? 'true' : 'false' ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CELAJE/includes/header.php'; // También corregir header y footer ?>
    <section class="faq-hero" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
        <div class="container">
            <div class="hero-content">
                <h1><i class="fas fa-credit-card"></i> Métodos de Pago</h1>
                <p>Múltiples opciones seguras y convenientes para realizar tu compra</p>
            </div>
        </div>
    </section>

    <section class="payment-methods-section">
        <div class="container">
            <div class="section-header">
                <h2>Formas de Pago Disponibles</h2>
                <p>Elige la opción que más te convenga. Todas nuestras transacciones son 100% seguras.</p>
            </div>

            <div class="payment-categories">

                <div class="payment-category">
                    <div class="category-header">
                        <div class="category-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <h3>Transferencias Bancarias</h3>
                        <p>Directo desde tu banco</p>
                    </div>

                    <?php if ($is_user_logged_in): ?>
                        <div id="bank-transfer-security-area">
                            <?php if ($show_bank_transfer_details): ?>
                                <div class="payment-options">
                                    <div class="payment-option">
                                        <div class="option-icon">
                                            <i class="fas fa-bolt"></i>
                                        </div>
                                        <div class="option-info">
                                            <h4>SPEI</h4>
                                            <p>Transferencia electrónica</p>
                                            <span class="processing-time">Confirmación en 1-2 horas</span>
                                        </div>
                                    </div>

                                    <div class="payment-option">
                                        <div class="option-icon">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="option-info">
                                            <h4>Depósito Bancario</h4>
                                            <p>En sucursal o cajero</p>
                                            <span class="processing-time">Confirmación en 24 horas</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bank-info">
                                    <h4>Datos Bancarios:</h4>
                                    <div class="bank-details">
                                        <div class="bank-item">
                                            <strong>Banco:</strong> BBVA México
                                        </div>
                                        <div class="bank-item">
                                            <strong>Cuenta:</strong> 0123456789
                                        </div>
                                        <div class="bank-item">
                                            <strong>CLABE:</strong> 012345678901234567
                                        </div>
                                        <div class="bank-item">
                                            <strong>Beneficiario:</strong> CELAJE S.A. de C.V.
                                        </div>
                                    </div>
                                    <p class="bank-note">
                                        <i class="fas fa-info-circle"></i>
                                        Envía tu comprobante de pago por WhatsApp para confirmar tu pedido
                                    </p>
                                    <p id="code-timer-display" style="font-size: 0.9em; color: #e74c3c; text-align: center; margin-top: 15px;">Tiempo restante: <span id="time-left"></span></p>
                                </div>
                            <?php else: ?>
                                <div class="code-input-area" style="padding: 30px; text-align: center;">
                                    <p><?= $code_message ?></p>
                                    <?php if (isset($_SESSION['security_code'])): // Mostrar el código solo para pruebas, eliminar en producción ?>
                                        <p style="font-weight: bold; color: #2ecc71;">Código actual: <span id="current-security-code"><?= $_SESSION['security_code'] ?></span></p>
                                    <?php endif; ?>
                                    <form action="" method="POST" id="security-code-form" style="margin-top: 20px;">
                                        <input type="text" name="security_code" id="security_code_input" placeholder="Ingresa el código" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 200px; margin-right: 10px; text-transform: uppercase;">
                                        <button type="submit" name="code_submitted" class="btn btn-primary" style="padding: 10px 20px; background-color: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">Ver Datos Bancarios</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="login-prompt" style="padding: 30px; text-align: center;">
                            <p>Para ver los datos de transferencia bancaria, <a href="<?= $base_url ?>login.php" style="color: var(--primary-color); font-weight: bold;">inicia sesión</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="payment-category">
                    <div class="category-header">
                        <div class="category-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3>Pago en Efectivo</h3>
                        <p>En tiendas de conveniencia</p>
                    </div>

                    <div class="payment-options">
                        <div class="payment-option">
                            <div class="option-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="option-info">
                                <h4>OXXO</h4>
                                <p>Más de 19,000 tiendas</p>
                                <span class="processing-time">Confirmación en 2-4 horas</span>
                            </div>
                        </div>

                        <div class="payment-option">
                            <div class="option-icon">
                                <i class="fas fa-store-alt"></i>
                            </div>
                            <div class="option-info">
                                <h4>7-Eleven</h4>
                                <p>Tiendas de conveniencia</p>
                                <span class="processing-time">Confirmación en 2-4 horas</span>
                            </div>
                        </div>

                        <div class="payment-option">
                            <div class="option-icon">
                                <i class="fas fa-pills"></i>
                            </div>
                            <div class="option-info">
                                <h4>Farmacias del Ahorro</h4>
                                <p>Red de farmacias</p>
                                <span class="processing-time">Confirmación en 2-4 horas</span>
                            </div>
                        </div>
                    </div>

                    <div class="cash-instructions">
                        <h4>¿Cómo pagar en efectivo?</h4>
                        <ol>
                            <li>Selecciona "Pago en efectivo" al finalizar tu compra</li>
                            <li>Recibirás un código de referencia por email</li>
                            <li>Ve a cualquier tienda afiliada</li>
                            <li>Proporciona el código y paga en efectivo</li>
                            <li>Tu pedido se procesará automáticamente</li>
                        </ol>
                    </div>
                </div>

    </section>

    <section class="security-section">
        <div class="container">
            <h2>Tu Seguridad es Nuestra Prioridad</h2>
            <div class="security-features">
                <div class="security-feature">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Encriptación SSL</h3>
                    <p>Todos los datos se transmiten de forma segura con certificado SSL de 256 bits</p>
                </div>

                <div class="security-feature">
                    <div class="feature-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3>PCI DSS Compliant</h3>
                    <p>Cumplimos con los estándares internacionales de seguridad para pagos</p>
                </div>

                <div class="security-feature">
                    <div class="feature-icon">
                        <i class="fas fa-eye-slash"></i>
                    </div>
                    <h3>No Almacenamos Datos</h3>
                    <p>No guardamos información sensible de tarjetas en nuestros servidores</p>
                </div>

                <div class="security-feature">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3>Protección del Comprador</h3>
                    <p>Garantía de reembolso en caso de problemas con tu compra</p>
                </div>
            </div>
        </div>
    </section>

    <section class="payment-faq">
        <div class="container">
            <h2>Preguntas Frecuentes sobre Pagos</h2>
            <div class="faq-items">

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>¿Es seguro pagar con tarjeta en línea?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Absolutamente. Utilizamos encriptación SSL de 256 bits y cumplimos con todos los estándares internacionales de seguridad. Además, no almacenamos datos de tarjetas en nuestros servidores.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>¿Puedo pagar a meses sin intereses?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Sí, ofrecemos meses sin intereses con tarjetas participantes. Las opciones disponibles se muestran al momento del pago según tu tarjeta.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>¿Cuánto tiempo tarda en confirmarse un pago en efectivo?</h3>
                    </div>
                    <div class="faq-answer">
                        <p>Los pagos en efectivo se confirman automáticamente entre 2 a 4 horas después de realizado el pago en la tienda.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>¿Qué hago si mi pago no se procesó correctamente?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Contáctanos inmediatamente por WhatsApp o email con tu número de pedido y comprobante de pago. Resolveremos el problema en menos de 24 horas.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CELAJE/includes/footer.php'; // También corregir footer ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/faq.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bankTransferSecurityArea = document.getElementById('bank-transfer-security-area');
            const timeLeftSpan = document.getElementById('time-left');
            const userLoggedIn = document.body.dataset.userLoggedIn === 'true';

            let timerInterval;
            const CODE_VALIDITY_SECONDS_JS = <?= CODE_VALIDITY_SECONDS ?>; // Sincronizar con PHP

            function startTimer(initialRemainingTime) {
                let timeLeft = initialRemainingTime;

                clearInterval(timerInterval); // Limpiar cualquier temporizador existente

                timerInterval = setInterval(() => {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    timeLeftSpan.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        // Recargar la sección para mostrar el formulario de código nuevamente
                        location.reload(); // Recarga simple para manejar el estado de la sesión de PHP
                        // Una alternativa más compleja sería una petición AJAX para obtener el nuevo formulario
                    }
                    timeLeft--;
                }, 1000);
            }

            if (userLoggedIn) {
                // Si los datos bancarios están visibles, iniciar el temporizador
                const codeTimerDisplay = document.getElementById('code-timer-display');
                if (codeTimerDisplay && timeLeftSpan) {
                    // Calculamos el tiempo restante basado en el timestamp de PHP
                    const phpServerTime = <?php echo time(); ?>;
                    const codeDisplayStartTime = <?php echo isset($_SESSION['code_display_start_time']) ? $_SESSION['code_display_start_time'] : '0'; ?>;
                    const elapsed = phpServerTime - codeDisplayStartTime;
                    const initialRemainingTime = CODE_VALIDITY_SECONDS_JS - elapsed;

                    if (initialRemainingTime > 0) {
                        startTimer(initialRemainingTime);
                    } else {
                        // Si el tiempo ya expiró, recargar para que PHP oculte los datos
                        location.reload();
                    }
                }

                // Manejar la conversión a mayúsculas del input
                const securityCodeInput = document.getElementById('security_code_input');
                if (securityCodeInput) {
                    securityCodeInput.addEventListener('input', function() {
                        this.value = this.value.toUpperCase();
                    });
                }
            }
        });
    </script>

    <style>
    /* Tu CSS existente */
    .payment-methods-section {
        padding: 80px 0;
    }

    .payment-categories {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .payment-category {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .category-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        padding: 30px;
        text-align: center;
    }

    .category-icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
    }

    .category-header h3 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .payment-options {
        padding: 30px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .payment-option {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        transition: var(--transition);
    }

    .payment-option:hover {
        border-color: var(--primary-color);
        background: var(--secondary-color);
    }

    .option-icon {
        font-size: 2rem;
        color: var(--primary-color);
        min-width: 50px;
    }

    .option-info h4 {
        margin-bottom: 5px;
        color: var(--text-dark);
    }

    .option-info p {
        margin-bottom: 5px;
        color: var(--text-light);
        font-size: 0.9rem;
    }

    .processing-time {
        font-size: 0.8rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    .category-benefits {
        padding: 0 30px 30px;
    }

    .category-benefits h4 {
        margin-bottom: 15px;
        color: var(--text-dark);
    }

    .category-benefits ul {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
    }

    .category-benefits li {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-dark);
    }

    .category-benefits i {
        color: #27ae60;
    }

    .bank-info {
        padding: 0 30px 30px;
        background: var(--secondary-color);
        margin: 0 30px 30px;
        border-radius: 8px;
        padding: 20px;
    }

    .bank-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
        margin-bottom: 15px;
    }

    .bank-item {
        padding: 10px;
        background: white;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .bank-note {
        background: #e3f2fd;
        padding: 15px;
        border-radius: 6px;
        color: #1976d2;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cash-instructions {
        padding: 0 30px 30px;
    }

    .cash-instructions h4 {
        margin-bottom: 15px;
        color: var(--text-dark);
    }

    .cash-instructions ol {
        padding-left: 20px;
    }

    .cash-instructions li {
        margin-bottom: 8px;
        color: var(--text-dark);
        line-height: 1.5;
    }

    .security-section {
        padding: 80px 0;
        background: var(--secondary-color);
    }

    .security-section h2 {
        text-align: center;
        margin-bottom: 50px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }

    .security-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .security-feature {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .security-feature .feature-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.8rem;
        color: white;
    }

    .security-feature h3 {
        margin-bottom: 15px;
        color: var(--text-dark);
    }

    .security-feature p {
        color: var(--text-light);
        line-height: 1.6;
    }

    .payment-faq {
        padding: 80px 0;
    }

    .payment-faq h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }

    @media (max-width: 768px) {
        .payment-options {
            grid-template-columns: 1fr;
        }

        .bank-details {
            grid-template-columns: 1fr;
        }

        .security-features {
            grid-template-columns: 1fr;
        }

        .category-benefits ul {
            grid-template-columns: 1fr;
        }
    }
    </style>