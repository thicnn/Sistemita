<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2 class="mb-0">Gestión de Clientes</h2>
    <div class="d-flex gap-2 flex-wrap">
        <form action="/sistemagestion/clients" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Buscar cliente..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="/sistemagestion/clients" class="btn btn-outline-secondary">Limpiar</a>
        </form>
        <a href="/sistemagestion/clients/create" class="btn btn-success">
            <i class="bi bi-person-plus-fill me-2"></i>Crear Nuevo Cliente
        </a>
    </div>
</div>

<?php if (empty($clients)): ?>
    <div class="text-center p-5 bg-light rounded animated-card">
        <p class="fs-4 text-muted">No se encontraron clientes.</p>
        <p>Intenta con otra búsqueda o crea un nuevo cliente para empezar.</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($clients as $index => $client): ?>
            <div class="col-md-6 col-lg-4 animated-card" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                <div class="card h-100 shadow-sm client-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar me-3">
                                <?php echo strtoupper(substr($client['nombre'], 0, 1)); ?>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">
                                    <a href="/sistemagestion/clients/show/<?php echo $client['id']; ?>" class="stretched-link text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($client['nombre']); ?>
                                    </a>
                                </h5>
                                <small class="text-muted"><?php echo $client['total_pedidos'] ?? 0; ?> pedido(s)</small>
                            </div>
                        </div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="bi bi-telephone-fill text-muted me-2"></i>Teléfono</span>
                                <span class="fw-light"><?php echo htmlspecialchars($client['telefono'] ?: 'N/A'); ?></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="bi bi-envelope-fill text-muted me-2"></i>Email</span>
                                <span class="fw-light"><?php echo htmlspecialchars($client['email'] ?: 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrador'): ?>
                    <div class="card-footer bg-white border-0 pt-0 text-end">
                        <a href="/sistemagestion/clients/edit/<?php echo $client['id']; ?>" class="btn btn-sm btn-outline-primary z-2 position-relative">Editar</a>
                        <button type="button" class="btn btn-sm btn-outline-danger z-2 position-relative"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmModal"
                                data-action="/sistemagestion/clients/delete/<?php echo $client['id']; ?>">
                            Eliminar
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-4" aria-label="Paginación de clientes">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page'=>''])); ?>">Anterior</a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page'=>''])); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page'=>''])); ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
<?php endif; ?>

<style>
    .client-card { transition: transform 0.2s ease-out, box-shadow 0.2s ease-out; }
    .client-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1) !important; }
    .avatar {
        width: 50px; height: 50px; border-radius: 50%;
        background-color: var(--bs-primary-bg-subtle);
        color: var(--bs-primary-text-emphasis);
        display: flex; justify-content: center; align-items: center;
        font-size: 1.5rem; font-weight: bold;
    }
    @keyframes slideInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animated-card { opacity: 0; animation: slideInUp 0.6s ease-out forwards; }
</style>