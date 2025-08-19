<div class="login-container">
    <h2>Iniciar Sesión</h2>
    <p>Bienvenido al Sistema de Gestión</p>
    <form action="/sistemagestion/login" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Entrar</button>
    </form>
</div>
<style>
    body { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
    .main-container { padding: 0; }
    .login-container { max-width: 400px; width: 100%; }
    .login-container h2 { text-align: center; font-weight: 500; font-size: 2rem; }
    .login-container p { text-align: center; color: var(--dark-gray); margin-bottom: 25px;}
</style>