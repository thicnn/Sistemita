<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="animated-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Registrar Error de Impresión</h2>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="/sistemagestion/errors/create" method="POST">
                        <div class="mb-3">
                            <label for="tipo_error" class="form-label">Tipo de Error</label>
                            <select id="tipo_error" name="tipo_error" class="form-select" required>
                                <option value="" disabled selected>Seleccione un tipo de error</option>
                                <?php foreach ($errorOptions as $descripcion => $costo): ?>
                                    <option value="<?php echo htmlspecialchars($descripcion); ?>">
                                        <?php echo htmlspecialchars($descripcion) . " ($" . number_format($costo, 2) . " por copia)"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad" class="form-label">Cantidad de Copias de Error</label>
                            <input type="number" id="cantidad" name="cantidad" class="form-control" required min="1">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Registrar Error
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
