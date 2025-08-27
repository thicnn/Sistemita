<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2 class="mb-0">Gestionar Productos</h2>
    <a href="/sistemagestion/admin/products/create" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Crear Nuevo Producto</a>
</div>

<div class="card shadow-sm animated-card">
    <div class="card-header bg-light">
        <form action="/sistemagestion/admin/products" method="GET" class="d-flex flex-wrap gap-3">
            <div class="flex-grow-1">
                <input type="text" id="search-box" name="search" class="form-control" placeholder="Buscar por descripción..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="w-auto">
                <select name="tipo" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos los Tipos</option>
                    <option value="Impresion" <?php echo ($_GET['tipo'] ?? '') == 'Impresion' ? 'selected' : ''; ?>>Impresión</option>
                    <option value="Fotocopia" <?php echo ($_GET['tipo'] ?? '') == 'Fotocopia' ? 'selected' : ''; ?>>Fotocopia</option>
                    <option value="Servicio" <?php echo ($_GET['tipo'] ?? '') == 'Servicio' ? 'selected' : ''; ?>>Servicio</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="/sistemagestion/admin/products" class="btn btn-outline-secondary">Limpiar</a>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Disponibilidad</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="products-table-body">
                    </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const productsTableBody = document.getElementById('products-table-body');
    const searchBox = document.getElementById('search-box');
    let initialProducts = <?php echo json_encode($products); ?>;

    function renderTable(products) {
        productsTableBody.innerHTML = '';
        if (products.length === 0) {
            productsTableBody.innerHTML = '<tr><td colspan="6" class="text-center p-4 text-muted">No se encontraron productos.</td></tr>';
            return;
        }
        products.forEach(product => {
            const availabilityBadge = product.disponible == 1 
                ? '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Activo</span>' 
                : '<span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">Inactivo</span>';
            
            const row = `
            <tr>
                <td>${product.tipo}</td>
                <td>${product.categoria}</td>
                <td>${product.descripcion}</td>
                <td class="text-end">$${parseFloat(product.precio).toFixed(2)}</td>
                <td class="text-center">${availabilityBadge}</td>
                <td class="text-center">
                    <a href="/sistemagestion/admin/products/edit/${product.id}" class="btn btn-sm btn-outline-primary">Editar</a>
                    <form action="/sistemagestion/admin/products/delete/${product.id}" method="POST" onsubmit="return confirm('¿Estás seguro?');" class="d-inline">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>`;
            productsTableBody.innerHTML += row;
        });
    }

    searchBox.addEventListener('keyup', async function() {
        const searchTerm = searchBox.value.toLowerCase();
        const tipoFiltro = document.querySelector('select[name="tipo"]').value;
        const response = await fetch(`/sistemagestion/admin/products?ajax=1&search=${searchTerm}&tipo=${tipoFiltro}`);
        const filteredProducts = await response.json();
        renderTable(filteredProducts);
    });

    renderTable(initialProducts); // Carga inicial
</script>