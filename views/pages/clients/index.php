<h2>Gestión de Clientes</h2>

<div class="toolbar">
    <a href="/sistemagestion/clients/create" class="button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Crear Nuevo Cliente
    </a>
    <form action="/sistemagestion/clients" method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Buscar por Nombre, Teléfono o Email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <button type="submit">Filtrar</button>
        <a href="/sistemagestion/clients" class="button button-secondary">Limpiar</a>
    </form>
</div>

<div class="table-container">
    <table class="table client-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="4" class="empty-message">No se encontraron clientes con los filtros aplicados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td>
                            <a href="/sistemagestion/clients/show/<?php echo $client['id']; ?>" class="client-name">
                                <?php echo htmlspecialchars($client['nombre']); ?>
                                <span class="order-count"><?php echo $client['total_pedidos'] ?? 0; ?> pedido(s)</span>
                            </a>
                        </td>
                        <td>
                            <div class="sensitive-data-wrapper">
                                <span class="sensitive-data">••••••••</span>
                                <button class="toggle-visibility" data-content="<?php echo htmlspecialchars($client['telefono']); ?>" title="Mostrar/Ocultar">
                                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="sensitive-data-wrapper">
                                <span class="sensitive-data">••••••••</span>
                                <button class="toggle-visibility" data-content="<?php echo htmlspecialchars($client['email']); ?>" title="Mostrar/Ocultar">
                                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                </button>
                            </div>
                        </td>
                        <td class="actions-cell">
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrador'): ?>
                                <div class="action-buttons">
                                    <a href="/sistemagestion/clients/edit/<?php echo $client['id']; ?>" class="action-btn edit">Editar</a>
                                    <form action="/sistemagestion/clients/delete/<?php echo $client['id']; ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este cliente?');">
                                        <button type="submit" class="action-btn delete">Eliminar</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span>N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px; }
    .toolbar .button { display: flex; align-items: center; gap: 8px; }
    .filter-form { display: flex; gap: 10px; align-items: center; }
    .filter-form input { padding: 10px 15px; font-size: 14px; min-width: 250px; }
    .filter-form button, .filter-form a { padding: 11px 20px; font-size: 14px; }
    .button.button-secondary { background-color: var(--secondary-color); }
    .button.button-secondary:hover { background-color: #5a6268; }
    
    .table-container { overflow-x: auto; }
    
    .client-table .client-name { font-weight: 500; text-decoration: none; color: var(--primary-color); }
    .client-table .client-name:hover { text-decoration: underline; }
    .client-table .order-count { display: block; font-size: 0.85em; color: var(--dark-gray); }
    
    .sensitive-data-wrapper { display: flex; align-items: center; gap: 8px; }
    .toggle-visibility { background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; }
    .icon-eye { width: 22px; height: 22px; fill: var(--dark-gray); transition: fill 0.2s, transform 0.2s; }
    .toggle-visibility:hover .icon-eye { fill: var(--primary-color); transform: scale(1.1); }

    .actions-cell { text-align: center; }
    .action-buttons { display: flex; justify-content: center; align-items: center; gap: 10px; }
    .action-btn { 
        padding: 8px 14px; text-decoration: none; border-radius: 8px; color: white; 
        border: none; cursor: pointer; font-size: 14px; font-weight: 500;
        transition: all 0.2s ease-in-out;
    }
    .action-btn:hover { opacity: 0.85; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .action-btn.edit { background-color: var(--secondary-color); }
    .action-btn.delete { background-color: var(--danger-color); }
    
    .empty-message { text-align: center; padding: 40px; color: var(--dark-gray); }
</style>

<script>
document.querySelectorAll('.toggle-visibility').forEach(button => {
    button.addEventListener('click', event => {
        const currentButton = event.currentTarget;
        // La clave es buscar el span DENTRO del mismo contenedor padre.
        const wrapper = currentButton.parentElement;
        const span = wrapper.querySelector('.sensitive-data');
        
        const isHidden = span.textContent.includes('•');
        
        const eyeOffIcon = `<svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.44-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>`;
        const eyeIcon = `<svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>`;
        
        if (isHidden) {
            span.textContent = currentButton.dataset.content;
            currentButton.innerHTML = eyeOffIcon;
        } else {
            span.textContent = '••••••••';
            currentButton.innerHTML = eyeIcon;
        }
    });
});
</script>