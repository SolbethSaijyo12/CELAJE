<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /CELAJE/login.php');
    exit;
}
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - CELAJE</title>
    <link rel="stylesheet" href="/CELAJE/css/celaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container" style="max-width: 1200px; margin: 40px auto; padding: 20px;">
        <section class="orders-section">
            <h1 style="font-size: 2.5rem; margin-bottom: 30px;">Mis Pedidos</h1>
            
            <div class="filters" style="display: flex; gap: 15px; margin-bottom: 25px;">
                <select id="statusFilter" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    <option value="all">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="procesando">Procesando</option>
                    <option value="enviado">Enviado</option>
                    <option value="entregado">Entregado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
                
                <input type="text" id="searchOrders" placeholder="Buscar pedidos..." 
                       style="padding: 10px; border-radius: 8px; border: 1px solid #ddd; flex-grow: 1;">
            </div>
            
            <div id="ordersContainer" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <p>Cargando pedidos...</p>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('statusFilter');
        const searchInput = document.getElementById('searchOrders');
        
        function loadOrders() {
            const status = statusFilter.value;
            const search = searchInput.value;
            
            fetch(`/CELAJE/ajax/get_user_orders.php?status=${status}&search=${search}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('ordersContainer');
                    
                    if (data.success && data.orders.length > 0) {
                        let html = '<div class="orders-list">';
                        
                        data.orders.forEach(order => {
                            html += `
                            <div class="order-card" style="border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                    <div>
                                        <strong>Pedido #${order.id}</strong>
                                        <p>${order.date}</p>
                                    </div>
                                    <div>
                                        <span class="status-badge" style="background: ${getStatusColor(order.status)}; 
                                            color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem;">
                                            ${order.status}
                                        </span>
                                        <div style="text-align: right; margin-top: 5px; font-size: 1.2rem;">
                                            $${order.total}
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                    <div>
                                        <h4>Dirección de Envío</h4>
                                        <p>${order.shipping_address}</p>
                                    </div>
                                    <div>
                                        <h4>Método de Pago</h4>
                                        <p>${order.payment_method}</p>
                                    </div>
                                </div>
                                
                                <div class="order-actions" style="display: flex; justify-content: space-between;">
                                    <a href="/CELAJE/orden_detalle.php?id=${order.id}" class="btn-secondary" 
                                       style="padding: 8px 15px;">
                                        Ver Detalles
                                    </a>
                                    
                                    ${order.status === 'pendiente' ? `
                                    <button class="btn-cancel" data-order="${order.id}" 
                                            style="background: #f8f9fa; border: 1px solid #dc3545; color: #dc3545; 
                                            padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                                        Cancelar Pedido
                                    </button>
                                    ` : ''}
                                </div>
                            </div>
                            `;
                        });
                        
                        html += '</div>';
                        container.innerHTML = html;
                        
                        // Agregar eventos a botones de cancelar
                        document.querySelectorAll('.btn-cancel').forEach(button => {
                            button.addEventListener('click', function() {
                                const orderId = this.dataset.order;
                                if (confirm('¿Estás seguro de cancelar este pedido?')) {
                                    cancelOrder(orderId);
                                }
                            });
                        });
                    } else {
                        container.innerHTML = '<p>No se encontraron pedidos.</p>';
                    }
                });
        }
        
        function getStatusColor(status) {
            const colors = {
                'pendiente': '#f59e0b',
                'procesando': '#3b82f6',
                'enviado': '#8b5cf6',
                'entregado': '#10b981',
                'cancelado': '#dc3545'
            };
            return colors[status.toLowerCase()] || '#6c757d';
        }
        
        function cancelOrder(orderId) {
            fetch(`/CELAJE/ajax/cancel_order.php?id=${orderId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pedido cancelado exitosamente');
                    loadOrders();
                } else {
                    alert(data.error || 'Error al cancelar pedido');
                }
            });
        }
        
        // Cargar pedidos iniciales
        loadOrders();
        
        // Filtros
        statusFilter.addEventListener('change', loadOrders);
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(loadOrders, 300);
        });
    });
    </script>
</body>
</html>