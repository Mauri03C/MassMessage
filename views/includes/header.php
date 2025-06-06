<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME ?? 'MassMessage'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo URLROOT; ?>">MassMessage</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="<?php echo URLROOT; ?>/auth/logout">Cerrar Sesión</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container mt-4">