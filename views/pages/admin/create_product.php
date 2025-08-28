<div class="row justify-content-center">
    <div class="col-lg-8 animated-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="mb-0">Crear Nuevo Producto</h2>
            <a href="/sistemagestion/admin/products" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="/sistemagestion/admin/products/create" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo-producto" class="form-label">Tipo de Producto</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                                <select name="tipo" id="tipo-producto" class="form-select" required>
                                    <option value="" selected disabled>Seleccionar...</option>
                                    <option value="Impresion">Impresión</option>
                                    <option value="Fotocopia">Fotocopia</option>
                                    <option value="Servicio">Servicio</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3" id="campos-extra">
                            <label for="categoria" class="form-label">Categoría</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-bookmark-fill"></i></span>
                                <input type="text" id="categoria" name="categoria" class="form-control" placeholder="Ej: Blanco y Negro, Color">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción / Nombre</label>
                        <input type="text" id="descripcion" name="descripcion" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="precio" name="precio" class="form-control" step="0.01" required>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save-fill me-2"></i>Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para la animación y el formulario */
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
        const tipoSelect = document.getElementById('tipo-producto');
        const camposExtra = document.getElementById('campos-extra');

        function toggleCategoria() {
            // Si se selecciona 'Servicio', se oculta el campo de categoría
            if (tipoSelect.value === 'Servicio') {
                camposExtra.style.display = 'none';
            } else {
                camposExtra.style.display = 'block';
            }
        }

        // Ejecutar la función al cargar la página y cada vez que cambie el selector
        toggleCategoria();
        tipoSelect.addEventListener('change', toggleCategoria);
    });
</script>