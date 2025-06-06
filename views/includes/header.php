<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASEURL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASEURL; ?>">
                <i class="fas fa-paper-plane me-2"></i><?php echo APPNAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($data['active_nav']) && $data['active_nav'] == 'dashboard') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>/dashboard">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($data['active_nav']) && $data['active_nav'] == 'new_message') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>/messages/create">
                                <i class="fas fa-plus me-1"></i>Nuevo Mensaje
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($data['active_nav']) && $data['active_nav'] == 'history') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>/messages">
                                <i class="fas fa-history me-1"></i>Historial
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUserLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUserLink">
                                <li><a class="dropdown-item" href="<?php echo BASEURL; ?>/users/profile">Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASEURL; ?>/auth/logout">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($data['active_nav']) && $data['active_nav'] == 'login') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>/auth/login">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($data['active_nav']) && $data['active_nav'] == 'register') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>/auth/register">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="py-4"> 
        <!-- El área de contenido principal comienza aquí -->