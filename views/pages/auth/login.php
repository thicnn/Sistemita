<div id="loader">
    <div class="spinner"></div>
</div>

<div class="login-wrapper">
    <div class="login-panel-left">
        <div class="brand-container">
            <h1 class="brand-title">Clave 3</h1>
            <p class="brand-subtitle">Sistema de Gestión</p>
        </div>
    </div>

    <div class="login-panel-right">
        <div class="login-form-container">
            <h2 class="form-title">Iniciar Sesión</h2>
            <p class="form-subtitle">Bienvenido de nuevo. Por favor, ingresa tus credenciales.</p>
            <form action="/sistemagestion/login" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control form-control-lg" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control form-control-lg" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ===== ESTILOS GENERALES Y DEL LOADER (SIN CAMBIOS) ===== */
:root { --primary-color: #0d6efd; --primary-dark: #0a58ca; }
#loader {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: #fff; z-index: 10000; display: flex;
    justify-content: center; align-items: center;
    transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
}
#loader.hidden { opacity: 0; visibility: hidden; }
.spinner {
    width: 50px; height: 50px; border: 5px solid rgba(0, 0, 0, 0.1);
    border-top-color: var(--primary-color); border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ===== ESTILOS DEL LAYOUT (SIN CAMBIOS) ===== */
.login-wrapper {
    display: grid; grid-template-columns: 1fr 1fr; height: 100vh;
    opacity: 0; animation: fadeIn 0.8s ease-out 0.3s forwards;
}
@keyframes fadeIn { to { opacity: 1; } }

/* Panel Izquierdo */
.login-panel-left {
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    color: white; display: flex; justify-content: center;
    align-items: center; text-align: center;
}
.brand-container, .login-form-container {
    animation: slideInUp 0.8s ease-out 0.5s forwards; opacity: 0;
}
.brand-title { font-size: 4rem; font-weight: 700; }
.brand-subtitle { font-size: 1.2rem; opacity: 0.8; }

/* Panel Derecho */
.login-panel-right {
    display: flex; justify-content: center;
    align-items: center; padding: 2rem;
}
.login-form-container { width: 100%; max-width: 400px; }
.form-title { font-size: 2.5rem; font-weight: 600; margin-bottom: 0.5rem; }
.form-subtitle { color: #6c757d; margin-bottom: 2rem; }

@media (max-width: 768px) {
    .login-wrapper { grid-template-columns: 1fr; }
    .login-panel-left { display: none; }
}

@keyframes slideInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
window.addEventListener('load', function() {
    const loader = document.getElementById('loader');
    loader.classList.add('hidden');
});
</script>