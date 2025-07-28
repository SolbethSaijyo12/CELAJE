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
    <title>CELAJE - Puntos de Entrega</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/faq.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Encuentra los puntos de entrega CELAJE más cercanos. Envío a domicilio y puntos de recogida en toda la República Mexicana.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="faq-hero" style="background: linear-gradient(135deg, #e67e22, #d35400);">
        <div class="container">
            <div class="hero-content">
                <h1><i class="fas fa-map-marker-alt"></i> Puntos de Entrega</h1>
                <p>Encuentra la opción de entrega que más te convenga. Cobertura en toda la República Mexicana.</p>
            </div>
        </div>
    </section>

    <!-- Delivery Options -->
    <section class="delivery-options">
        <div class="container">
            <div class="section-header">
                <h2>Opciones de Entrega</h2>
                <p>Elige entre envío a domicilio o recogida en punto de entrega</p>
            </div>

            <div class="options-grid">
                
                <!-- Envío a Domicilio -->
                <div class="delivery-card home-delivery">
                    <div class="delivery-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Envío a Domicilio</h3>
                    <p>Recibe tu pedido directamente en tu casa u oficina</p>
                    
                    <div class="delivery-features">
                        <div class="feature">
                            <i class="fas fa-clock"></i>
                            <span>1-3 días hábiles</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-shield-alt"></i>
                            <span>Paquete asegurado</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-search"></i>
                            <span>Seguimiento en tiempo real</span>
                        </div>
                    </div>
                    
                    <div class="delivery-price">
                        <span class="price">$50</span>
                        <span class="note">Gratis en compras +$800</span>
                    </div>
                </div>

                <!-- Punto de Entrega -->
                <div class="delivery-card pickup-point">
                    <div class="delivery-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>Punto de Entrega</h3>
                    <p>Recoge tu pedido en el punto más cercano a ti</p>
                    
                    <div class="delivery-features">
                        <div class="feature">
                            <i class="fas fa-clock"></i>
                            <span>1-2 días hábiles</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-calendar"></i>
                            <span>Horarios extendidos</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-id-card"></i>
                            <span>Solo necesitas tu ID</span>
                        </div>
                    </div>
                    
                    <div class="delivery-price">
                        <span class="price">$30</span>
                        <span class="note">Más económico</span>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <!-- Coverage Map -->
    <section class="coverage-map">
        <div class="container">
            <h2>Cobertura Nacional</h2>
            <p>Enviamos a toda la República Mexicana con diferentes tiempos de entrega</p>
            
            <div class="map-container">
                <div class="map-placeholder">
                     <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1u68lO0PAmmE8aLQpfhIthguw4UIwVH8&ehbc=2E312F&noprof=1" width="640" height="480"></iframe>
                </div>
                
                <div class="coverage-legend">
                    <h4>Tiempos de Entrega por Zona</h4>
                    <div class="legend-items">
                        <div class="legend-item zone-1">
                            <div class="color-indicator"></div>
                            <div class="zone-info">
                                <strong>Zona 1 - </strong>
                        <span><i class="fas fa-subway"></i> Metro Línea A (Santa Marta)</span><br>
                        <span><i class="fas fa-subway"></i> Metro Línea 1 (Pantitlán)</span><br>
                        <span><i class="fas fa-subway"></i> Metro Línea 2 (Taxqueña)</span><br>
                        <span><i class="fas fa-subway"></i> Metro Línea 8 (Garibaldi/Lagunilla)</span><br>
                        <span><i class="fas fa-subway"></i> Metro Línea 9 (Centro Médico)</span><br>
                        <span><i class="fas fa-subway"></i> Metro Línea 12 (Mixcoac)</span><br>
                            </div>
                        </div>
                        
                        <div class="legend-item zone-2">
                            <div class="color-indicator"></div>
                            <div class="zone-info">
                                <strong>Zona 2 - </strong>
                        <span><i class="fas fa-city"></i> Chalco</span>
                            </div>
                        </div>
                        
                        <div class="legend-item zone-3">
                            <div class="color-indicator"></div>
                            <div class="zone-info">
                                <strong>Zona 3 - </strong>
                        <span><i class="fas fa-city"></i> Ixtapaluca</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pickup Points -->
    <section class="pickup-points">
        <div class="container">
            <h2>Puntos de Entrega Disponibles</h2>
            <p>Red de puntos de entrega en las principales ciudades del país</p>
            
            <div class="points-grid">
                
                <!-- OXXO -->
                <div class="point-card">
                    <div class="point-logo">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>OXXO</h3>
                    <div class="point-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>+19,000 tiendas</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>24/7 disponible</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>7 días para recoger</span>
                        </div>
                    </div>
                </div>

                <!-- 7-Eleven -->
                <div class="point-card">
                    <div class="point-logo">
                        <i class="fas fa-store-alt"></i>
                    </div>
                    <h3>7-Eleven</h3>
                    <div class="point-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>+1,800 tiendas</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>24/7 disponible</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>5 días para recoger</span>
                        </div>
                    </div>
                </div>

                <!-- Farmacias Guadalajara -->
                <div class="point-card">
                    <div class="point-logo">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h3>Farmacias Guadalajara</h3>
                    <div class="point-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>+1,500 sucursales</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>6:00 - 23:00</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>7 días para recoger</span>
                        </div>
                    </div>
                </div>

                <!-- Estafeta -->
                <div class="point-card">
                    <div class="point-logo">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Estafeta</h3>
                    <div class="point-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>+1,200 oficinas</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>9:00 - 18:00</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>10 días para recoger</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FAQ Delivery -->
    <section class="delivery-faq">
        <div class="container">
            <h2>Preguntas Frecuentes sobre Entregas</h2>
            <div class="faq-items">
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>¿Qué necesito para recoger mi pedido en un punto de entrega?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Solo necesitas:</p>
                        <ul>
                            <li>Tu identificación oficial (INE, pasaporte, cédula profesional)</li>
                            <li>El código de recogida que te enviamos por email/WhatsApp</li>
                            <li>El número de pedido (opcional, pero recomendado)</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>¿Puedo cambiar la dirección de entrega después de hacer el pedido?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Sí, puedes cambiar la dirección siempre y cuando tu pedido no haya sido enviado aún. Contáctanos por WhatsApp lo antes posible con tu número de pedido y la nueva dirección.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>¿Qué pasa si no estoy en casa cuando llega mi pedido?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>El repartidor intentará la entrega hasta 3 veces. Si no te encuentra, el paquete se llevará al punto de entrega más cercano y recibirás un aviso con la dirección y horarios para recogerlo.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>¿Cuánto tiempo tengo para recoger mi pedido en un punto de entrega?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Tienes entre 5 a 10 días dependiendo del punto de entrega (OXXO: 7 días, 7-Eleven: 5 días, Estafeta: 10 días). Después de este tiempo, el paquete se devuelve a nuestro almacén.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/faq.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>

    
    <style>
    .delivery-options {
        padding: 80px 0;
    }
    
    .options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }
    
    .delivery-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        border-top: 4px solid;
    }
    
    .delivery-card:hover {
        transform: translateY(-5px);
    }
    
    .home-delivery {
        border-top-color: #3498db;
    }
    
    .pickup-point {
        border-top-color: #27ae60;
    }
    
    .express-delivery {
        border-top-color: #e67e22;
    }
    
    .delivery-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
        color: white;
    }
    
    .home-delivery .delivery-icon {
        background: #3498db;
    }
    
    .pickup-point .delivery-icon {
        background: #27ae60;
    }
    
    .express-delivery .delivery-icon {
        background: #e67e22;
    }
    
    .delivery-card h3 {
        margin-bottom: 15px;
        color: var(--text-dark);
        font-size: 1.3rem;
    }
    
    .delivery-card p {
        color: var(--text-light);
        margin-bottom: 25px;
        line-height: 1.6;
    }
    
    .delivery-features {
        margin-bottom: 25px;
    }
    
    .feature {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 10px;
        color: var(--text-dark);
        font-size: 0.9rem;
    }
    
    .feature i {
        color: var(--primary-color);
        width: 16px;
    }
    
    .delivery-price {
        border-top: 1px solid var(--border-color);
        padding-top: 20px;
    }
    
    .price {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
        display: block;
    }
    
    .note {
        color: var(--text-light);
        font-size: 0.9rem;
    }
    
    .coverage-map {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .coverage-map h2 {
        text-align: center;
        margin-bottom: 15px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .coverage-map p {
        text-align: center;
        color: var(--text-light);
        margin-bottom: 50px;
        font-size: 1.1rem;
    }
    
    .map-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
        align-items: start;
    }
    
    .map-placeholder {
        background: white;
        border-radius: 12px;
        padding: 60px 30px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .map-placeholder i {
        font-size: 4rem;
        color: var(--text-light);
        margin-bottom: 20px;
    }
    
    .map-placeholder h3 {
        margin-bottom: 10px;
        color: var(--text-dark);
    }
    
    .coverage-legend {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .coverage-legend h4 {
        margin-bottom: 20px;
        color: var(--text-dark);
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .color-indicator {
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }
    
    .zone-1 .color-indicator {
        background: #27ae60;
    }
    
    .zone-2 .color-indicator {
        background: #f39c12;
    }
    
    .zone-3 .color-indicator {
        background: #e74c3c;
    }
    
    .zone-info strong {
        display: block;
        color: var(--text-dark);
        font-size: 0.9rem;
    }
    
    .zone-info span {
        color: var(--text-light);
        font-size: 0.8rem;
    }
    
    .pickup-points {
        padding: 80px 0;
    }
    
    .pickup-points h2 {
        text-align: center;
        margin-bottom: 15px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .pickup-points p {
        text-align: center;
        color: var(--text-light);
        margin-bottom: 50px;
        font-size: 1.1rem;
    }
    
    .points-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .point-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .point-card:hover {
        transform: translateY(-5px);
    }
    
    .point-logo {
        width: 60px;
        height: 60px;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.5rem;
        color: white;
    }
    
    .point-card h3 {
        margin-bottom: 20px;
        color: var(--text-dark);
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        color: var(--text-dark);
        font-size: 0.9rem;
    }
    
    .info-item i {
        color: var(--primary-color);
        width: 16px;
    }
    
    .tracking-section {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .tracking-section h2 {
        text-align: center;
        margin-bottom: 15px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .tracking-section p {
        text-align: center;
        color: var(--text-light);
        margin-bottom: 50px;
        font-size: 1.1rem;
    }
    
    .tracking-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .tracking-form {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .tracking-form h3 {
        margin-bottom: 20px;
        color: var(--text-dark);
    }
    
    .tracking-form .form-group {
        display: flex;
        gap: 0;
    }
    
    .tracking-form input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 6px 0 0 6px;
        font-size: 1rem;
        outline: none;
    }
    
    .tracking-form input:focus {
        border-color: var(--primary-color);
    }
    
    .tracking-form button {
        padding: 12px 20px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 0 6px 6px 0;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .tracking-form button:hover {
        background: var(--primary-dark);
    }
    
    .tracking-info {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .tracking-info h4 {
        margin-bottom: 20px;
        color: var(--text-dark);
    }
    
    .tracking-info ul {
        list-style: none;
        padding: 0;
    }
    
    .tracking-info li {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        color: var(--text-dark);
    }
    
    .tracking-info i {
        color: var(--primary-color);
        width: 16px;
    }
    
    .delivery-faq {
        padding: 80px 0;
    }
    
    .delivery-faq h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    @media (max-width: 768px) {
        .options-grid {
            grid-template-columns: 1fr;
        }
        
        .map-container {
            grid-template-columns: 1fr;
        }
        
        .points-grid {
            grid-template-columns: 1fr;
        }
        
        .tracking-container {
            grid-template-columns: 1fr;
        }
    }
    </style>
    
    <script>
    document.getElementById('trackingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const trackingNumber = document.getElementById('trackingNumber').value;
        
        if (trackingNumber.trim()) {
            // Simulate tracking lookup
            alert(`Buscando información para el número de seguimiento: ${trackingNumber}\n\nEsta función estará disponible próximamente. Por ahora, contacta por WhatsApp para rastrear tu pedido.`);
        }
    });
    </script>
</body>
</html>