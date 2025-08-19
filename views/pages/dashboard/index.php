<div class="dashboard-header">
    <div>
        <h2>Bienvenido a tu Centro de Control, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        <p>Este es el resumen de tu actividad para hoy.</p>
    </div>
    <div class="datetime-widget">
        <div id="time">12:00:00</div>
        <div id="date">Lunes, 1 de Enero</div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="main-content">
        <h3>Cola de Producción del Día</h3>
        <div class="production-queues">
            <div class="queue-card in-progress">
                <h4>En Curso</h4>
                <p class="order-count"><?php echo count($pedidosPorEstado['En Curso'] ?? []); ?></p>
                <small>Pedidos activos</small>
            </div>
            <div class="queue-card ready">
                <h4>Listos para Retirar</h4>
                <p class="order-count"><?php echo count($pedidosPorEstado['Listo para Retirar'] ?? []); ?></p>
                <small>Esperando al cliente</small>
            </div>
        </div>

        <div class="suggestions-section">
            <h3>Sugerencias del Día</h3>
            <div class="suggestions-grid">
                <a href="/sistemagestion/admin/products" class="suggestion-card">
                    <strong>Administrar Catálogo</strong>
                    <p>Revisa tus productos y actualiza precios.</p>
                </a>
                <a href="/sistemagestion/reports" class="suggestion-card">
                    <strong>Revisar Contadores</strong>
                    <p>Lleva el control de stock y producción.</p>
                </a>
                <div class="suggestion-card">
                    <strong>Hojas de Prueba</strong>
                    <p>Recuerda imprimir pruebas en todas las máquinas.</p>
                </div>
                 <a href="/sistemagestion/clients" class="suggestion-card">
                    <strong>Contactar Clientes</strong>
                    <p>Revisa los pedidos listos y avisa a tus clientes.</p>
                </a>
            </div>
        </div>
    </div>
    <aside class="sidebar">
        <h3>Top 5 Clientes (Últimos 3 Meses)</h3>
        <ul class="top-clients-list">
            <?php if (empty($topClients)): ?>
                <li>No hay datos suficientes.</li>
            <?php else: ?>
                <?php foreach($topClients as $client): ?>
                    <li>
                        <span><?php echo htmlspecialchars($client['nombre']); ?></span>
                        <strong><?php echo $client['total_pedidos']; ?> pedidos</strong>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </aside>
</div>

<style>
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .datetime-widget { text-align: right; }
    #time { font-size: 2.5rem; font-weight: 500; }
    #date { font-size: 1rem; color: var(--dark-gray); }

    .dashboard-grid { display: grid; grid-template-columns: 3fr 1fr; gap: 30px; }
    @media (max-width: 992px) { .dashboard-grid { grid-template-columns: 1fr; } }

    .production-queues { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
    .queue-card { padding: 20px; border-radius: 12px; color: var(--white); }
    .queue-card.in-progress { background: linear-gradient(45deg, #ffc107, #ff9800); }
    .queue-card.ready { background: linear-gradient(45deg, #28a745, #20c997); }
    .queue-card h4 { margin: 0; font-size: 1.2rem; opacity: 0.9; }
    .queue-card .order-count { font-size: 3rem; font-weight: 600; margin: 10px 0; }
    .queue-card small { opacity: 0.8; }

    .suggestions-section { margin-top: 40px; }
    .suggestions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
    .suggestion-card { display: block; background-color: var(--light-gray); padding: 20px; border-radius: 8px; text-decoration: none; color: inherit; border: 1px solid var(--medium-gray); transition: all 0.2s; }
    .suggestion-card:hover { border-color: var(--primary-color); transform: translateY(-3px); box-shadow: var(--shadow-sm); }
    .suggestion-card strong { display: block; margin-bottom: 5px; }
    .suggestion-card p { font-size: 0.9em; color: var(--dark-gray); margin: 0; }

    .sidebar h3 { font-size: 1.2rem; }
    .top-clients-list { list-style: none; padding: 0; margin: 0; }
    .top-clients-list li { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--medium-gray); }
    .top-clients-list li:last-child { border-bottom: none; }
    .top-clients-list li strong { background-color: var(--primary-color); color: var(--white); padding: 3px 8px; border-radius: 5px; font-size: 0.8em; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeElement = document.getElementById('time');
    const dateElement = document.getElementById('date');

    // Función para actualizar la hora cada segundo
    function updateTime() {
        const now = new Date();
        timeElement.textContent = now.toLocaleTimeString('es-ES');
    }

    // Función para poner la fecha en español
    function updateDate() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateElement.textContent = now.toLocaleDateString('es-ES', options);
    }

    updateTime();
    updateDate();
    setInterval(updateTime, 1000);
});
</script>