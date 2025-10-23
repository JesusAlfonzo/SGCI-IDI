<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} | Acceso al Sistema</title>

    {{-- Estilos de AdminLTE/Bootstrap y Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('vendor/almasaeed2010/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <style>
        /* Estilos Base y Fondo Opacado (Modo Claro) */
        body {
            /* Fondo gris claro para contraste */
            background-color: #dee2e6; 
            background-image: linear-gradient(135deg, #e9ecef 0%, #ced4da 100%); 
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: #333; 
            font-family: 'Source Sans Pro', sans-serif;
        }
        .intro-card {
            background-color: #ffffff; 
            color: #333;
            /* Aumentamos ligeramente el padding horizontal y lo mantenemos vertical */
            padding: 60px 70px; 
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2); 
            max-width: 480px;
            width: 90%;
            text-align: center;
            animation: slideInDown 0.8s ease-out;
            border: 1px solid #c5c5c5; 
        }
        
        /* Ajustes de Espaciado y Estilo */
        
        /* 1. Logo y Título más juntos */
        .logo-block {
            /* Contenedor flexible para el logo y el título */
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Reducimos el margen inferior de todo el bloque */
            margin-bottom: 25px; 
        }
        .logo-icon {
            color: #007bff; /* Cambiado a azul para más modernidad */
            /* Quitamos padding y lo separamos del texto de abajo */
            padding: 0; 
            margin-bottom: 5px; /* Reducido a 5px para acercarlo al título */
            transition: color 0.3s;
        }
        .intro-card h1 {
            color: #007bff; 
            font-weight: 700;
            letter-spacing: 0.5px;
            /* Eliminamos el margen inferior, ya lo maneja .logo-block */
            margin-bottom: 0; 
        }
        
        /* 2. Más Separación entre elementos (texto descriptivo) */
        .text-descriptivo {
            /* Aumentamos el margen superior e inferior para separar el texto */
            margin-top: 2.5rem !important; 
            margin-bottom: 2.5rem !important;
            padding: 0 15px;
        }
        
        /* 3. Reestilización del Botón (Minimalista y sin Negritas) */
        .btn-acceso {
            font-size: 1.1rem;
            /* Padding ajustado para minimalismo */
            padding: 10px 30px; 
            border-radius: 5px; /* Bordes más cuadrados */
            font-weight: normal; /* Eliminamos negritas */
            margin-top: 15px; /* Separación del texto descriptivo */
            
            /* Estilo Moderno y Plano */
            background-color: #b9d4ff; /* Azul más vibrante */
            border-color: #3b82f6;
            box-shadow: none; /* Quitamos la sombra del botón */
            transition: all 0.2s ease-in-out;
        }
        .btn-acceso:hover {
            background-color: #e1e9ff; /* Azul oscuro al pasar el mouse */
            border-color: #1e3a8a;
        }

        /* 4. Footer con más espacio */
        .text-footer {
            margin-top: 35px; /* Más margen superior */
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

    <div class="intro-card">
        
        <div class="logo-block">
            {{-- Icono de Marca --}}
            <i class="fas fa-rocket fa-4x logo-icon"></i>
            {{-- Título --}}
            <h1>{{ strtoupper(config('app.name', 'APP')) }}</h1>
        </div>
        
        <small class="text-secondary d-block font-weight-light">PORTAL ADMINISTRATIVO</small>
        
        {{-- Texto descriptivo con más espacio --}}
        <p class="text-sm text-muted text-descriptivo"> 
            Bienvenido. Inicia sesión para acceder a las herramientas y reportes del sistema. Tu seguridad es importante.
        </p>

        {{-- Botón de Acceso Minimalista --}}
        <a href="{{ route('login') }}" class="btn btn-primary btn-block btn-acceso">
            <i class="fas fa-sign-in-alt mr-2"></i> Acceder al Sistema
        </a>
        
        <div class="text-footer">
            <a href="#" class="text-sm text-secondary">¿Tienes problemas para acceder? Contáctanos.</a>
        </div>
    </div>

</body>
</html>