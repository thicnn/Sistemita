<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Materiales</h2>
    <a href="/sistemagestion/admin/materials/create" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-2"></i>Crear Nuevo Material
    </a>
</div>

<?php if (isset($_SESSION['flash_message'])) : ?>
    <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['flash_message']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<div class="card shadow-sm animated-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-end">Stock Actual</th>
                        <th class="text-end">Stock Mínimo</th>
                        <th>Unidad</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($materials)) : ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">No hay materiales registrados.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($materials as $material) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($material['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($material['descripcion']); ?></td>
                                <td class="text-end <?php echo $material['stock_actual'] <= $material['stock_minimo'] ? 'text-danger fw-bold' : ''; ?>">
                                    <?php echo number_format($material['stock_actual'], 2); ?>
                                </td>
                                <td class="text-end"><?php echo number_format($material['stock_minimo'], 2); ?></td>
                                <td><?php echo htmlspecialchars($material['unidad_medida']); ?></td>
                                <td class="text-center">
                                    <a href="/sistemagestion/admin/materials/edit/<?php echo $material['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form action="/sistemagestion/admin/materials/delete/<?php echo $material['id']; ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este material?');" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
