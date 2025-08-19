<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión | Centro de Impresión</title>
    
    <style>
        /* --- Estilos Generales --- */
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f6f9; 
            margin: 0; 
            color: #333;
        }
        .main-container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        hr { border: 0; border-top: 1px solid #e0e0e0; margin: 25px 0; }

        /* --- Header y Navegación --- */
        header { 
            background-color: #fff; 
            padding: 15px 25px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 25px; 
            border-radius: 8px;
        }
        header h1 { margin: 0; font-size: 24px; }
        nav a { 
            margin-left: 20px; 
            text-decoration: none; 
            color: #007bff; 
            font-weight: 500;
            font-size: 16px;
            transition: color 0.2s;
        }
        nav a:hover { color: #0056b3; }
        nav a.logout { color: #dc3545; }
        nav a.logout:hover { color: #b02a37; }
        
        /* --- Área de Contenido Principal --- */
        .content-area { 
            background-color: #fff; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
        }

        /* --- Formularios --- */
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
        input[type="text"], input[type="email"], input[type="tel"], input[type="password"], input[type="number"], input[type="date"], textarea, select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            box-sizing: border-box; 
            font-size: 16px;
            transition: border-color 0.2s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }

        /* --- Botones --- */
        .button, button[type="submit"] {
            display: inline-block;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.2s;
        }
        .button:hover, button[type="submit"]:hover { background-color: #0056b3; }
        button:disabled { background-color: #ccc; cursor: not-allowed; }
        
        /* --- Tablas --- */
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .table th { background-color: #f8f9fa; font-weight: 600; }
    </style>
</head>
<body>

<div class="main-container">

    <?php 
    // Solo mostramos el header con la navegación SI el usuario ha iniciado sesión.
    if (isset($_SESSION['user_id'])): ?>
    <header>
        <h1>Centro de Impresión</h1>
        <nav>
            <a href="/sistemagestion/dashboard">Dashboard</a>
            <a href="/sistemagestion/clients">Clientes</a>
            <a href="/sistemagestion/orders">Pedidos</a>
            
            <?php // --- LÓGICA DE ROLES PARA ENLACES DE ADMIN ---
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrador'): ?>
                <a href="/sistemagestion/reports">Reportes</a>
                <a href="/sistemagestion/admin/products">Productos</a>
            <?php endif; ?>
            
            <a href="/sistemagestion/logout" class="logout">Cerrar Sesión</a>
        </nav>
    </header>
    <?php endif; ?>

    <?php // El contenedor principal del contenido SIEMPRE se muestra ?>
    <main class="content-area">