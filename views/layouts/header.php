<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión | Clave 3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        :root {
            --nav-height: 70px;
        }

        body.logged-in {
            padding-top: var(--nav-height);
        }

        /* --- ESTILOS GLOBALES DEFINITIVOS PARA MODO OSCURO --- */

        /* 1. Fondos Generales */
        body {
            background-color: var(--bs-secondary-bg);
        }

        .content-area {
            background-color: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            padding: 2.5rem;
            border-radius: .5rem;
        }

        /* 2. Reglas específicas para cuando el modo oscuro está activo */
        [data-bs-theme="dark"] {

            /* Arregla cabeceras, pies y listas que se quedaban blancas */
            .card-header,
            .card-footer,
            .list-group-item {
                background-color: var(--bs-tertiary-bg) !important;
            }

            /* Arregla las tablas */
            .table-light {
                --bs-table-bg: var(--bs-tertiary-bg);
            }

            /* Arregla el avatar de clientes */
            .avatar {
                background-color: var(--bs-primary-bg-subtle);
                color: var(--bs-primary-text-emphasis);
            }

            /* Arregla componentes personalizados */
            .suggestion-card,
            .report-card-small {
                background-color: var(--bs-tertiary-bg);
                border-color: var(--bs-border-color-translucent);
            }
        }

        /* 3. Estilos base para los componentes (funcionan en ambos modos) */
        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
            background-color: var(--bs-primary-bg-subtle);
            color: var(--bs-primary-text-emphasis);
        }

        .input-group-text {
            background-color: var(--bs-tertiary-bg);
            border-right: none;
        }

        /* --- FIN DE ESTILOS GLOBALES --- */

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .navbar {
            animation: slideDown 0.5s ease-out;
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

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
            left: 0;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--bs-primary) !important;
        }

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

        /* --- REGLA AÑADIDA PARA CORREGIR LA ALINEACIÓN --- */
        .client-card .list-group-item {
            background-color: transparent !important;
        }
    </style>
</head>

<body class="<?php echo isset($_SESSION['user_id']) ? 'logged-in' : ''; ?>">

    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm fixed-top" style="height: var(--nav-height);">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold fs-4 px-2" href="/sistemagestion/dashboard">Clave 3</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
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
                        <li class="nav-item ms-lg-3"><a class="nav-link text-danger" href="/sistemagestion/logout"><i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión</a></li>
                        <li class="nav-item ms-lg-2">
                            <button class="nav-link px-3" id="theme-toggler" type="button" style="border: none; background: none;">
                                <i class="bi bi-moon-stars-fill"></i>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container-fluid mt-4">
            <div class="content-area">
            <?php endif; ?>