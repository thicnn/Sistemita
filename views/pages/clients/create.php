<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="animated-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Crear Nuevo Cliente</h2>
                <a href="/sistemagestion/clients" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Volver a la Lista
                </a>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="/sistemagestion/clients/create" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre Completo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" id="nombre" name="nombre" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="tel" id="telefono" name="telefono" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" id="email" name="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notas" class="form-label">Notas (Requerimientos habituales)</label>
                            <textarea id="notas" name="notas" rows="4" class="form-control"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save-fill me-2"></i>Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animated-card {
        opacity: 0;
        animation: slideInUp 0.6s ease-out forwards;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }
    .form-control {
        border-left: none;
    }
    .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb),.25);
        border-left: 1px solid var(--bs-primary);
    }
    .input-group:focus-within .input-group-text {
        border-color: var(--bs-primary);
        border-right: 1px solid var(--bs-primary);
    }
</style>