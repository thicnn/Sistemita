<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión | Centro de Impresión</title>

    <style>
        /* --- Paleta de Colores y Variables --- */
        :root {
            --primary-color: #0d6efd;
            --primary-hover: #0b5ed7;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --text-color: #212529;
            --body-bg: #f4f6f9;
            --white: #fff;
            --border-color: #dee2e6;
            --shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
            --shadow-md: 0 .5rem 1rem rgba(0,0,0,.15);
        }

        /* --- Estilos Generales y Tipografía --- */
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--body-bg); 
            margin: 0; 
            color: var(--text-color);
            font-size: 16px;
            line-height: 1.6;
        }
        .main-container { max-width: 1600px; margin: 20px auto; padding: 0 20px; }
        h2 { 
            font-size: 2rem; 
            border-bottom: 1px solid var(--border-color); 
            padding-bottom: 15px; 
            margin-bottom: 30px; 
            font-weight: 500; 
        }
        hr { border: 0; border-top: 1px solid var(--medium-gray); margin: 30px 0; }

        /* --- Header y Navegación --- */
        header { 
            background-color: var(--white); 
            padding: 15px 30px; 
            box-shadow: var(--shadow-sm); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
            border-radius: 12px;
        }
        header h1 { margin: 0; font-size: 26px; font-weight: 600; }
        nav a { 
            margin-left: 25px; 
            text-decoration: none; 
            color: var(--primary-color); 
            font-weight: 500;
            transition: color 0.2s;
            padding-bottom: 5px;
            border-bottom: 2px solid transparent;
        }
        nav a:hover { color: var(--primary-hover); border-bottom-color: var(--primary-hover); }
        nav a.logout { color: var(--danger-color); }
        nav a.logout:hover { border-bottom-color: transparent; }

        /* --- Área de Contenido Principal --- */
        .content-area { 
            background-color: var(--white); 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: var(--shadow-sm); 
        }

        /* --- Formularios --- */
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: #495057; }
        input, textarea, select { 
            width: 100%; 
            padding: 12px 15px; 
            border: 1px solid var(--border-color); 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-size: 16px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: var(--primary-color);
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
        }

        /* --- Botones --- */
        .button, button[type="submit"] {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease-in-out;
        }
        .button:hover, button[type="submit"]:hover { background-color: var(--primary-hover); box-shadow: 0 4px 10px rgba(0,0,0,0.1); transform: translateY(-2px); }
        button:disabled { background-color: var(--secondary-color); cursor: not-allowed; opacity: 0.7; transform: none; box-shadow: none; }

        /* --- Tablas --- */
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border-bottom: 1px solid var(--border-color); padding: 16px; text-align: left; }
        .table th { background-color: var(--light-gray); font-weight: 600; color: #495057; }
        .table tr:hover { background-color: #f1f1f1; }

        /* --- Media Queries para Responsividad --- */
        @media (max-width: 992px) {
            header { flex-direction: column; gap: 15px; }
        }
        @media (max-width: 768px) {
            nav { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 10px;}
            nav a { margin: 0 10px; }
            h2 { font-size: 24px; }
        }
    </style>
</head>
<body>

<div class="main-container">
    <?php if (isset($_SESSION['user_id'])): ?>
    <header>
        <h1>Centro de Impresión</h1>
        <nav>
            <a href="/sistemagestion/dashboard">Inicio</a>
            <a href="/sistemagestion/clients">Clientes</a>
            <a href="/sistemagestion/orders">Pedidos</a>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrador'): ?>
                <a href="/sistemagestion/reports">Reportes</a>
                <a href="/sistemagestion/admin/products">Productos</a>
                <a href="/sistemagestion/admin/settings">Ajustes</a>
            <?php endif; ?>
            <a href="/sistemagestion/logout" class="logout">Cerrar Sesión</a>
        </nav>
    </header>
    <?php endif; ?>
    <main class="content-area">