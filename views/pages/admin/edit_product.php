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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label">Precio de Venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="precio" name="precio" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['precio']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="costo" class="form-label">Costo Unitario</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="costo" name="costo" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['costo'] ?? '0.00'); ?>" required>
                            </div>
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