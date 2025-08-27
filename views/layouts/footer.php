<?php if (isset($_SESSION['user_id'])): ?>
    </div>
    </main><?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100"></div>

<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">¿Estás seguro? Esta acción no se puede deshacer.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="confirmForm" action="" method="POST">
                    <button type="submit" class="btn btn-danger">Sí, estoy seguro</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- LÓGICA PARA LOS MODALES DE CONFIRMACIÓN ---
        const confirmModal = document.getElementById('confirmModal');
        if (confirmModal) {
            confirmModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const actionUrl = button.getAttribute('data-action');
                const form = confirmModal.querySelector('#confirmForm');
                form.setAttribute('action', actionUrl);
            });
        }

        // --- LÓGICA PARA MOSTRAR NOTIFICACIONES TOAST ---
        <?php if (isset($_SESSION['toast'])): ?>
            const toastContainer = document.querySelector('.toast-container');
            const toastData = <?php echo json_encode($_SESSION['toast']); ?>;
            const toastHTML = `<div class="toast align-items-center text-bg-${toastData.type} border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">${toastData.message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
            toastContainer.innerHTML = toastHTML;
            const toastBootstrap = new bootstrap.Toast(toastContainer.querySelector('.toast'));
            toastBootstrap.show();
            <?php unset($_SESSION['toast']); ?>
        <?php endif; ?>

        // --- LÓGICA PARA EL MODO OSCURO ---
        const themeToggler = document.getElementById('theme-toggler');
        const htmlElement = document.documentElement;
        const currentTheme = localStorage.getItem('theme') || 'light';

        htmlElement.setAttribute('data-bs-theme', currentTheme);
        themeToggler.innerHTML = currentTheme === 'dark' ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-stars-fill"></i>';

        themeToggler.addEventListener('click', () => {
            const newTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            themeToggler.innerHTML = newTheme === 'dark' ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-stars-fill"></i>';
        });
    });
</script>
</body>

</html>