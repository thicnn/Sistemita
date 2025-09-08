<div class="row justify-content-center">
    <div class="col-lg-8 animated-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="mb-0">Editando Producto</h2>
            <a href="/sistemagestion/admin/products" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Cancelar y Volver
            </a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="/sistemagestion/admin/products/edit/<?php echo $product['id']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción (Nombre del Producto)</label>
                        <input type="text" id="descripcion" name="descripcion" class="form-control" value="<?php echo htmlspecialchars($product['descripcion']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="precio" name="precio" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['precio']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disponibilidad</label>
                        <div class="form-check form-switch p-3 border rounded bg-light">
                            <input class="form-check-input" type="checkbox" role="switch" id="disponible" name="disponible" value="1" <?php echo $product['disponible'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="disponible">
                                El producto está <strong id="availability-status"><?php echo $product['disponible'] ? 'Activo' : 'Inactivo'; ?></strong> (aparecerá en la creación de pedidos).
                            </label>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-repeat me-2"></i>Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sección de Gestión de Materiales -->
    <div class="col-lg-8 animated-card mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h4 class="mb-0 h5">Gestión de Materiales para este Producto</h4>
            </div>
            <div class="card-body p-4">
                <h5>Materiales Ya Vinculados</h5>
                <?php
                $product_id = $product['id'];
                $associated_materials = $data['associated_materials'] ?? [];
                $all_materials = $data['all_materials'] ?? [];

                if (empty($associated_materials)) : ?>
                    <p class="text-muted">Este producto aún no consume ningún material del inventario.</p>
                <?php else : ?>
                    <ul class="list-group mb-4">
                        <?php foreach ($associated_materials as $am) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="me-2"><?php echo htmlspecialchars($am['nombre']); ?></strong>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                        Consume: <?php echo htmlspecialchars($am['cantidad_consumida']); ?> <?php echo htmlspecialchars($am['unidad_medida']); ?>
                                    </span>
                                </div>
                                <form action="/sistemagestion/admin/products/remove_material/<?php echo $am['asociacion_id']; ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres quitar este material del producto?');">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Quitar material">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <hr class="my-4">

                <h5>Asociar Nuevo Material o Actualizar Cantidad</h5>
                <form action="/sistemagestion/admin/products/add_material/<?php echo $product_id; ?>" method="POST" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="material_id" class="form-label">Material</label>
                        <select name="material_id" id="material_id" class="form-select" required>
                            <option value="" disabled selected>Selecciona un material...</option>
                            <?php foreach ($all_materials as $material) : ?>
                                <option value="<?php echo $material['id']; ?>"><?php echo htmlspecialchars($material['nombre']); ?> (Stock: <?php echo $material['stock_actual'] . ' ' . $material['unidad_medida']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="cantidad_consumida" class="form-label">Cantidad a Consumir</label>
                        <input type="number" name="cantidad_consumida" id="cantidad_consumida" class="form-control" step="0.01" required placeholder="Ej: 1.5">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-link-45deg me-1"></i> Vincular
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para la animación */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animated-card {
        opacity: 0;
        animation: slideInUp 0.6s ease-out forwards;
    }


    .input-group-text {
        background-color: var(--bs-tertiary-bg);
        /* <-- ESTA ES LA SOLUCIÓN */
        border-right: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const disponibleSwitch = document.getElementById('disponible');
        const statusLabel = document.getElementById('availability-status');

        disponibleSwitch.addEventListener('change', function() {
            if (this.checked) {
                statusLabel.textContent = 'Activo';
                statusLabel.classList.add('text-success');
                statusLabel.classList.remove('text-danger');
            } else {
                statusLabel.textContent = 'Inactivo';
                statusLabel.classList.add('text-danger');
                statusLabel.classList.remove('text-success');
            }
        });

        // Añadir clase inicial al cargar la página
        if (disponibleSwitch.checked) {
            statusLabel.classList.add('text-success');
        } else {
            statusLabel.classList.add('text-danger');
        }
    });
</script>