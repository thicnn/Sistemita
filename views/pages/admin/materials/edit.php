<?php
// Suponemos que la variable $material se pasa a esta vista desde el controlador
$material = $data['material'] ?? null;
if (!$material) {
    echo "<div class='alert alert-danger'>Error: No se encontró el material.</div>";
    return;
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8 animated-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Editar Material</h2>
            <a href="/sistemagestion/admin/materials" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="/sistemagestion/admin/materials/update/<?php echo $material['id']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Material</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($material['nombre']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($material['descripcion']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stock_actual" class="form-label">Stock Actual</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                                <input type="number" id="stock_actual" name="stock_actual" class="form-control" step="0.01" value="<?php echo htmlspecialchars($material['stock_actual']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock_minimo" class="form-label">Stock Mínimo de Alerta</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                <input type="number" id="stock_minimo" name="stock_minimo" class="form-control" step="0.01" value="<?php echo htmlspecialchars($material['stock_minimo']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                        <input type="text" id="unidad_medida" name="unidad_medida" class="form-control" placeholder="Ej: hojas, ml, unidades, metros" value="<?php echo htmlspecialchars($material['unidad_medida']); ?>" required>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save-fill me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
