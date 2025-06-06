<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 500 - Error del Servidor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #dc3545;
        }
        .error-details {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8d7da;
            border-radius: 5px;
            color: #721c24;
            font-family: monospace;
            white-space: pre-wrap;
            display: none; /* Ocultar por defecto, solo mostrar en desarrollo */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container text-center">
            <div class="error-code">500</div>
            <h1>¡Ups! Algo salió mal</h1>
            <p class="lead">Lo sentimos, ha ocurrido un error en el servidor.</p>
            <p>Nuestro equipo ha sido notificado y está trabajando para solucionarlo.</p>
            <a href="/MassMessage" class="btn btn-primary mt-3">Volver al inicio</a>
            
            <?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'development'): ?>
            <div class="error-details mt-4 text-start">
                <h5>Detalles del error:</h5>
                <?php 
                $error = error_get_last();
                if ($error) {
                    echo "<strong>Tipo:</strong> " . $error['type'] . "\n";
                    echo "<strong>Mensaje:</strong> " . $error['message'] . "\n";
                    echo "<strong>Archivo:</strong> " . $error['file'] . "\n";
                    echo "<strong>Línea:</strong> " . $error['line'] . "\n";
                } else {
                    echo "No se pudo obtener información detallada del error.";
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
