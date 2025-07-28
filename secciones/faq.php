<?php 
session_start();
include '../includes/db.php';
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['SCRIPT_NAME']) . "/../";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CELAJE - Preguntas Frecuentes</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/faq.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Encuentra respuestas a las preguntas más frecuentes sobre CELAJE. Envíos, devoluciones, tallas y más.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <section class="faq-hero">
        <div class="container">
            <div class="hero-content">
                <h1><i class="fas fa-question-circle"></i> Preguntas Frecuentes</h1>
                <p>Encuentra respuestas rápidas a las dudas más comunes sobre CELAJE</p>
                
                <div class="faq-search">
                    <input type="text" id="faqSearch" placeholder="Buscar en preguntas frecuentes...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="quick-links">
        <div class="container">
            <h2>Temas más consultados</h2>
            <div class="quick-links-grid">
                <a href="#envios" class="quick-link-card">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>Envíos</h3>
                    <p>Tiempos y costos de entrega</p>
                </a>
                <a href="#tallas" class="quick-link-card">
                    <i class="fas fa-ruler"></i>
                    <h3>Tallas</h3>
                    <p>Guía de tallas y medidas</p>
                </a>
                <a href="#pagos" class="quick-link-card">
                    <i class="fas fa-credit-card"></i>
                    <h3>Pagos</h3>
                    <p>Métodos de pago aceptados</p>
                </a>
                <a href="#productos" class="quick-link-card">
                    <i class="fas fa-tshirt"></i>
                    <h3>Productos</h3>
                    <p>Cuidado y materiales</p>
                </a>
                <a href="#cuenta" class="quick-link-card">
                    <i class="fas fa-user"></i>
                    <h3>Mi Cuenta</h3>
                    <p>Registro y gestión de cuenta</p>
                </a>
            </div>
        </div>
    </section>

    <section class="faq-content">
        <div class="container">
            
            <div class="faq-category" id="envios">
                <h2><i class="fas fa-shipping-fast"></i> Envíos y Entregas</h2>
                <div class="faq-items">
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Cuánto tiempo tarda en llegar mi pedido?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Los tiempos de entrega varían según tu ubicación:</p>
                            <ul>
                                <li><strong>Ciudad de México y área metropolitana:</strong> 1-2 días hábiles</li>
                                <li><strong>Guadalajara, Monterrey, Puebla:</strong> 2-3 días hábiles</li>
                                <li><strong>Resto del país:</strong> 3-5 días hábiles</li>
                            </ul>
                            <p>Una vez que tu pedido sea enviado, recibirás un número de seguimiento para rastrear tu paquete.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Cuál es el costo de envío?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Manejamos una tarifa fija de envío de <strong>$50 pesos</strong> a cualquier parte de México.</p>
                            <p><strong>¡Envío gratis!</strong> En compras mayores a $800 pesos.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Puedo cambiar mi dirección de envío después de hacer el pedido?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Sí, puedes cambiar tu dirección de envío siempre y cuando tu pedido no haya sido procesado aún. Contáctanos inmediatamente a través de:</p>
                            <ul>
                                <li>WhatsApp: <a href="https://wa.me/5215512345678">+52 55 1234-5678</a></li>
                                <li>Email: <a href="mailto:info@celaje.com">info@celaje.com</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <div class="faq-category" id="tallas">
                <h2><i class="fas fa-ruler"></i> Tallas y Medidas</h2>
                <div class="faq-items">
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Cómo sé qué talla elegir?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Cada producto tiene una guía de tallas específica. Te recomendamos:</p>
                            <ul>
                                <li>Medir una prenda similar que te quede bien</li>
                                <li>Comparar con nuestra tabla de medidas</li>
                                <li>Si dudas entre dos tallas, elige la mayor</li>
                                <li>Contactarnos si necesitas ayuda personalizada</li>
                            </ul>
                            <p><a href="guia-tallas.php" class="btn-link">Ver guía completa de tallas</a></p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Las tallas son iguales para hombres y mujeres?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>La mayoría de nuestros productos son <strong>unisex</strong> con tallas estándar. Sin embargo, algunos productos pueden tener cortes específicos. Siempre revisa la descripción del producto y la guía de tallas.</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="faq-category" id="pagos">
                <h2><i class="fas fa-credit-card"></i> Métodos de Pago</h2>
                <div class="faq-items">
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Qué métodos de pago aceptan?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Aceptamos los siguientes métodos de pago:</p>
                            <div class="payment-methods">
                                <div class="payment-category">
                                    <h4><i class="fas fa-credit-card"></i> Tarjetas</h4>
                                    <ul>
                                        <li>Visa</li>
                                        <li>Mastercard</li>
                                        <li>American Express</li>
                                        <li>Tarjetas de débito</li>
                                    </ul>
                                </div>
                                <div class="payment-category">
                                    <h4><i class="fas fa-university"></i> Transferencias</h4>
                                    <ul>
                                        <li>SPEI</li>
                                        <li>Depósito bancario</li>
                                    </ul>
                                </div>
                                <div class="payment-category">
                                    <h4><i class="fas fa-store"></i> Efectivo</h4>
                                    <ul>
                                        <li>OXXO</li>
                                        <li>7-Eleven</li>
                                        <li>Farmacias del Ahorro</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Es seguro pagar en línea?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p><strong>¡Absolutamente!</strong> Tu seguridad es nuestra prioridad:</p>
                            <ul>
                                <li><i class="fas fa-shield-alt"></i> Conexión SSL encriptada</li>
                                <li><i class="fas fa-lock"></i> No almacenamos datos de tarjetas</li>
                                <li><i class="fas fa-certificate"></i> Procesadores de pago certificados</li>
                                <li><i class="fas fa-eye-slash"></i> Información protegida</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <div class="faq-category" id="productos">
                <h2><i class="fas fa-tshirt"></i> Productos y Cuidados</h2>
                <div class="faq-items">
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Cómo debo cuidar mis productos CELAJE?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Para mantener la calidad de tus productos:</p>
                            <div class="care-instructions">
                                <div class="care-item">
                                    <i class="fas fa-tint"></i>
                                    <div>
                                        <h4>Lavado</h4>
                                        <p>Agua fría (máx. 30°C), del revés, con colores similares</p>
                                    </div>
                                </div>
                                <div class="care-item">
                                    <i class="fas fa-wind"></i>
                                    <div>
                                        <h4>Secado</h4>
                                        <p>Al aire libre, evita la exposición directa al sol</p>
                                    </div>
                                </div>
                                <div class="care-item">
                                    <i class="fas fa-iron"></i>
                                    <div>
                                        <h4>Planchado</h4>
                                        <p>Temperatura media, del revés, evita planchar sobre el diseño</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Los diseños se desvanecen con el tiempo?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Nuestros diseños están hechos con <strong>tintas de alta calidad</strong> que resisten múltiples lavados. Siguiendo las instrucciones de cuidado, tus productos mantendrán su apariencia original por mucho tiempo.</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="faq-category" id="cuenta">
                <h2><i class="fas fa-user"></i> Mi Cuenta</h2>
                <div class="faq-items">
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Necesito crear una cuenta para comprar?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Puedes navegar y explorar nuestros productos sin crear una cuenta, pero <strong>necesitas registrarte para realizar una compra</strong>.</p>
                            <p><strong>Beneficios de tener cuenta:</strong></p>
                            <ul>
                                <li>Historial de pedidos</li>
                                <li>Seguimiento de envíos</li>
                                <li>Lista de favoritos</li>
                                <li>Direcciones guardadas</li>
                                <li>Ofertas exclusivas</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>¿Cómo puedo cambiar mi contraseña?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Para cambiar tu contraseña:</p>
                            <ol>
                                <li>Inicia sesión en tu cuenta</li>
                                <li>Ve a "Mi Perfil"</li>
                                <li>Selecciona "Cambiar contraseña"</li>
                                <li>Ingresa tu contraseña actual y la nueva</li>
                                <li>Confirma los cambios</li>
                            </ol>
                            <p>Si olvidaste tu contraseña, usa la opción "¿Olvidaste tu contraseña?" en la página de login.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <section class="faq-contact">
        <div class="container">
            <div class="contact-content">
                <h2>¿No encontraste lo que buscabas?</h2>
                <p>Nuestro equipo de atención al cliente está aquí para ayudarte</p>
                
                <div class="contact-options">
                    <a href="https://wa.me/5215512345678" class="contact-option whatsapp">
                        <i class="fab fa-whatsapp"></i>
                        <div>
                            <h3>WhatsApp</h3>
                            <p>Respuesta inmediata</p>
                            <span>+52 55 1234-5678</span>
                        </div>
                    </a>
                    
                    <a href="mailto:info@celaje.com" class="contact-option email">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>Respuesta en 24 hrs</p>
                            <span>info@celaje.com</span>
                        </div>
                    </a>
                    
                    <div class="contact-option hours">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Horarios</h3>
                            <p>Lun - Vie: 9:00 - 18:00</p>
                            <span>Sáb: 10:00 - 14:00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

   <script src="/CELAJE/js/main.js"></script>
   <script src="/CELAJE/js/faq.js"></script>
   <script src="/CELAJE/js/stars.js"></script>

</body>
</html>