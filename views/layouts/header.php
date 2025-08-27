<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión | Clave 3</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Estilos personalizados */
        :root {
            --nav-height: 70px;
        }
        body {
            background-color: #f4f6f9;
        }
        body.logged-in {
            padding-top: var(--nav-height); /* Espacio para la navbar fija */
        }
        
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .navbar {
            animation: slideDown 0.5s ease-out;
        }

        .content-area {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: .5rem;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        }
        
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--bs-primary);
            transition: all 0.3s ease-out;
        }
        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
            left: 0;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--bs-primary) !important;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
    </style>
</head>
<body class="<?php echo isset($_SESSION['user_id']) ? 'logged-in' : ''; ?>">

<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top" style="height: var(--nav-height);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-4 px-2" href="/sistemagestion/dashboard">
            Clave 3
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link px-3" href="/sistemagestion/dashboard">Inicio</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="/sistemagestion/clients">Clientes</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="/sistemagestion/orders">Pedidos</a></li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrador'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown">Admin</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/sistemagestion/reports">Reportes</a></li>
                            <li><a class="dropdown-item" href="/sistemagestion/admin/products">Productos</a></li>
                            <li><a class="dropdown-item" href="/sistemagestion/admin/settings">Ajustes</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="nav-item ms-lg-3">
                    <a class="nav-link text-danger" href="/sistemagestion/logout">
                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container-fluid mt-4">
    <div class="content-area">
<?php endif; ?>