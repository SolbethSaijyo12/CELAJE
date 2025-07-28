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
    <title>CELAJE - Contacto y Redes Sociales</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/celaje.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/faq.css">
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
    <meta name="description" content="Contacta con CELAJE. Encuentra nuestras redes sociales, WhatsApp, email y horarios de atención.">
</head>
<body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="faq-hero" style="background: linear-gradient(135deg, #3498db, #2980b9);">
        <div class="container">
            <div class="hero-content">
                <h1><i class="fas fa-envelope"></i> Contacto y Redes Sociales</h1>
                <p>Estamos aquí para ayudarte. Conéctate con nosotros a través de múltiples canales</p>
            </div>
        </div>
    </section>

    <!-- Contact Methods -->
    <section class="contact-methods">
        <div class="container">
            <div class="section-header">
                <h2>¿Cómo prefieres contactarnos?</h2>
                <p>Elige el canal que más te convenga. Estamos disponibles para resolver todas tus dudas.</p>
            </div>

            <div class="contact-grid">
                
                <!-- WhatsApp -->
                <div class="contact-card whatsapp">
                    <div class="contact-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3>WhatsApp</h3>
                    <p>Respuesta inmediata</p>
                    <div class="contact-info">
                        <strong>+52 55 1234-5678</strong>
                        <span>Lun - Vie: 9:00 - 18:00</span>
                        <span>Sáb: 10:00 - 14:00</span>
                    </div>
                    <a href="https://wa.me/5215512345678?text=Hola%20CELAJE,%20tengo%20una%20consulta" 
                       target="_blank" 
                       class="contact-btn">
                        <i class="fab fa-whatsapp"></i>
                        Chatear Ahora
                    </a>
                </div>

                <!-- Email -->
                <div class="contact-card email">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Correo Electrónico</h3>
                    <p>Respuesta en 24 horas</p>
                    <div class="contact-info">
                        <strong>info@celaje.com</strong>
                        <span>Soporte general</span>
                        <strong>ventas@celaje.com</strong>
                        <span>Consultas de ventas</span>
                    </div>
                    <a href="mailto:info@celaje.com?subject=Consulta%20desde%20la%20web" 
                       class="contact-btn">
                        <i class="fas fa-envelope"></i>
                        Enviar Email
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Social Media -->
    <section class="social-media">
        <div class="container">
            <h2>Síguenos en Redes Sociales</h2>
            <p>Mantente al día con nuestras novedades, promociones y contenido exclusivo</p>
            
            <div class="social-grid">
                
                <!-- Instagram -->
                <div class="social-card instagram">
                    <div class="social-header">
                        <div class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <div class="social-info">
                            <h3>Instagram</h3>
                            <span>@celaje.oficial</span>
                        </div>
                    </div>
                    <div class="social-content">
                        <p>Descubre nuestros productos, looks del día y contenido detrás de cámaras</p>
                        <div class="social-stats">
                            <div class="stat">
                                <strong>2.5K</strong>
                                <span>Seguidores</span>
                            </div>
                            <div class="stat">
                                <strong>150+</strong>
                                <span>Posts</span>
                            </div>
                        </div>
                    </div>
                    <a href="https://www.instagram.com/celaje.oficial" 
                       target="_blank" 
                       class="social-btn">
                        <i class="fab fa-instagram"></i>
                        Seguir
                    </a>
                </div>

                <!-- Facebook -->
                <div class="social-card facebook">
                    <div class="social-header">
                        <div class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        <div class="social-info">
                            <h3>Facebook</h3>
                            <span>CELAJE Oficial</span>
                        </div>
                    </div>
                    <div class="social-content">
                        <p>Noticias, eventos especiales y comunidad de fans de CELAJE</p>
                        <div class="social-stats">
                            <div class="stat">
                                <strong>1.8K</strong>
                                <span>Me gusta</span>
                            </div>
                            <div class="stat">
                                <strong>50+</strong>
                                <span>Reseñas</span>
                            </div>
                        </div>
                    </div>
                    <a href="https://www.facebook.com/celaje.oficial" 
                       target="_blank" 
                       class="social-btn">
                        <i class="fab fa-facebook-f"></i>
                        Me Gusta
                    </a>
                </div>

                <!-- TikTok -->
                <div class="social-card tiktok">
                    <div class="social-header">
                        <div class="social-icon">
                            <i class="fab fa-tiktok"></i>
                        </div>
                        <div class="social-info">
                            <h3>TikTok</h3>
                            <span>@celaje_oficial</span>
                        </div>
                    </div>
                    <div class="social-content">
                        <p>Videos creativos, tendencias de moda y contenido divertido</p>
                        <div class="social-stats">
                            <div class="stat">
                                <strong>3.2K</strong>
                                <span>Seguidores</span>
                            </div>
                            <div class="stat">
                                <strong>25K</strong>
                                <span>Likes</span>
                            </div>
                        </div>
                    </div>
                    <a href="https://www.tiktok.com/@celaje_oficial" 
                       target="_blank" 
                       class="social-btn">
                        <i class="fab fa-tiktok"></i>
                        Seguir
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="contact-form-section">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>Envíanos un Mensaje</h2>
                    <p>¿Tienes alguna pregunta específica? Completa el formulario y te responderemos pronto.</p>
                </div>

                <form class="contact-form" id="contactForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nombre Completo *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Teléfono</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="subject">Asunto *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Selecciona un tema</option>
                                <option value="consulta-producto">Consulta sobre producto</option>
                                <option value="pedido">Estado de mi pedido</option>
                                <option value="devolucion">Devolución o cambio</option>
                                <option value="colaboracion">Colaboración</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Mensaje *</label>
                        <textarea id="message" name="message" rows="5" required 
                                  placeholder="Describe tu consulta o comentario..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="newsletter" id="newsletter">
                            <span class="checkmark"></span>
                            Quiero recibir noticias y promociones de CELAJE
                        </label>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Mensaje
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Business Hours -->
    <section class="business-hours">
        <div class="container">
            <h2>Horarios de Atención</h2>
            <div class="hours-grid">
                <div class="hours-card">
                    <h3>Atención al Cliente</h3>
                    <div class="schedule">
                        <div class="day">
                            <span>Lunes - Viernes</span>
                            <strong>9:00 - 18:00</strong>
                        </div>
                        <div class="day">
                            <span>Sábados</span>
                            <strong>10:00 - 14:00</strong>
                        </div>
                        <div class="day">
                            <span>Domingos</span>
                            <strong>Cerrado</strong>
                        </div>
                    </div>
                </div>

                <div class="hours-card">
                    <h3>WhatsApp</h3>
                    <div class="schedule">
                        <div class="day">
                            <span>Lunes - Viernes</span>
                            <strong>9:00 - 20:00</strong>
                        </div>
                        <div class="day">
                            <span>Sábados</span>
                            <strong>10:00 - 16:00</strong>
                        </div>
                        <div class="day">
                            <span>Domingos</span>
                            <strong>12:00 - 16:00</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script src="<?= $base_url ?>js/stars.js"></script>

    
    <style>
    .contact-methods {
        padding: 80px 0;
    }
    
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }
    
    .contact-card {
        background: white;
        padding: 40px 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .contact-card:hover {
        transform: translateY(-5px);
    }
    
    .contact-icon {
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
    
    .whatsapp .contact-icon {
        background: #25D366;
    }
    
    .email .contact-icon {
        background: #EA4335;
    }
    
    .phone .contact-icon {
        background: #3498db;
    }
    
    .contact-card h3 {
        margin-bottom: 10px;
        color: var(--text-dark);
    }
    
    .contact-card p {
        color: var(--text-light);
        margin-bottom: 20px;
    }
    
    .contact-info {
        margin-bottom: 25px;
    }
    
    .contact-info strong {
        display: block;
        color: var(--text-dark);
        margin-bottom: 5px;
        font-size: 1.1rem;
    }
    
    .contact-info span {
        display: block;
        color: var(--text-light);
        font-size: 0.9rem;
        margin-bottom: 3px;
    }
    
    .contact-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
        color: white;
    }
    
    .whatsapp .contact-btn {
        background: #25D366;
    }
    
    .email .contact-btn {
        background: #EA4335;
    }
    
    .phone .contact-btn {
        background: #3498db;
    }
    
    .contact-btn:hover {
        transform: scale(1.05);
        color: white;
    }
    
    .social-media {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .social-media h2 {
        text-align: center;
        margin-bottom: 15px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .social-media p {
        text-align: center;
        color: var(--text-light);
        margin-bottom: 50px;
        font-size: 1.1rem;
    }
    
    .social-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }
    
    .social-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .social-card:hover {
        transform: translateY(-5px);
    }
    
    .social-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 25px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .social-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .instagram .social-icon {
        background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
    }
    
    .facebook .social-icon {
        background: #1877F2;
    }
    
    .tiktok .social-icon {
        background: #000000;
    }
    
    .youtube .social-icon {
        background: #FF0000;
    }
    
    .social-info h3 {
        margin-bottom: 5px;
        color: var(--text-dark);
    }
    
    .social-info span {
        color: var(--text-light);
        font-size: 0.9rem;
    }
    
    .social-content {
        padding: 25px;
    }
    
    .social-content p {
        color: var(--text-light);
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .social-stats {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .stat {
        text-align: center;
    }
    
    .stat strong {
        display: block;
        color: var(--text-dark);
        font-size: 1.2rem;
    }
    
    .stat span {
        color: var(--text-light);
        font-size: 0.8rem;
    }
    
    .social-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        text-decoration: none;
        font-weight: 600;
        color: white;
        transition: var(--transition);
    }
    
    .instagram .social-btn {
        background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
    }
    
    .facebook .social-btn {
        background: #1877F2;
    }
    
    .tiktok .social-btn {
        background: #000000;
    }
    
    .youtube .social-btn {
        background: #FF0000;
    }
    
    .social-btn:hover {
        opacity: 0.9;
        color: white;
    }
    
    .contact-form-section {
        padding: 80px 0;
    }
    
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .form-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        padding: 40px;
        text-align: center;
    }
    
    .form-header h2 {
        margin-bottom: 10px;
        font-size: 2rem;
    }
    
    .contact-form {
        padding: 40px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 6px;
        font-size: 1rem;
        transition: var(--transition);
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }
    
    .submit-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 auto;
    }
    
    .submit-btn:hover {
        background: var(--primary-dark);
    }
    
    .business-hours {
        padding: 80px 0;
        background: var(--secondary-color);
    }
    
    .business-hours h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        color: var(--text-dark);
    }
    
    .hours-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .hours-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .hours-card h3 {
        text-align: center;
        margin-bottom: 25px;
        color: var(--text-dark);
        font-size: 1.3rem;
    }
    
    .day {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .day:last-child {
        border-bottom: none;
    }
    
    .day span {
        color: var(--text-light);
    }
    
    .day strong {
        color: var(--text-dark);
    }
    
    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
        
        .social-grid {
            grid-template-columns: 1fr;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .hours-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    
    <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Simulate form submission
        const submitBtn = this.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            alert('¡Mensaje enviado correctamente! Te responderemos pronto.');
            this.reset();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 2000);
    });
    </script>
</body>
</html>