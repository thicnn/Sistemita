<?php
// Suponemos que estos datos vienen del AdminController
$meta_ventas = $data['meta_ventas_mensual'] ?? 0;
$meta_pedidos = $data['meta_pedidos_mensual'] ?? 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Ajustes Generales</h2>
</div>

<?php if (isset($_SESSION['flash_message'])) : ?>
    <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['flash_message']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<div class="row g-4">
    <!-- Columna de Metas -->
    <div class="col-lg-6 animated-card">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bullseye me-2"></i>Metas y Objetivos</h5>
            </div>
            <div class="card-body">
                <form action="/sistemagestion/admin/settings/goals" method="POST">
                    <div class="mb-3">
                        <label for="meta_ventas" class="form-label">Meta de Ventas Mensual ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="meta_ventas" name="meta_ventas_mensual" value="<?php echo htmlspecialchars($meta_ventas); ?>" step="100">
                        </div>
                        <div class="form-text">Establece un objetivo de ingresos mensuales para el seguimiento en el dashboard.</div>
                    </div>
                    <div class="mb-3">
                        <label for="meta_pedidos" class="form-label">Meta de Pedidos Mensual</label>
                         <div class="input-group">
                            <span class="input-group-text">#</span>
                            <input type="number" class="form-control" id="meta_pedidos" name="meta_pedidos_mensual" value="<?php echo htmlspecialchars($meta_pedidos); ?>" step="10">
                        </div>
                        <div class="form-text">Establece un objetivo de cantidad de pedidos mensuales.</div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Guardar Metas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Columna de borrado de datos -->
    <div class="col-lg-6 animated-card" style="animation-delay: 0.1s;">
        <div class="card shadow-sm h-100 border-danger">
             <div class="card-header bg-danger-subtle text-danger-emphasis">
                <h5 class="mb-0"><i class="bi bi-trash3-fill me-2"></i>Zona de Peligro</h5>
            </div>
            <div class="card-body">
                <p>Las siguientes acciones son irreversibles. Úsalas con precaución.</p>
                <div class="d-grid gap-2">
                     <form action="/sistemagestion/admin/delete-data" method="POST" onsubmit="return confirm('¿Estás ABSOLUTAMENTE SEGURO de que quieres borrar TODOS LOS CLIENTES?');">
                        <input type="hidden" name="type" value="clients">
                        <button type="submit" class="btn btn-outline-danger w-100">Borrar Todos los Clientes</button>
                    </form>
                    <form action="/sistemagestion/admin/delete-data" method="POST" onsubmit="return confirm('¿Estás ABSOLUTAMENTE SEGURO de que quieres borrar TODOS LOS PEDIDOS?');">
                        <input type="hidden" name="type" value="orders">
                        <button type="submit" class="btn btn-outline-danger w-100">Borrar Todos los Pedidos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
