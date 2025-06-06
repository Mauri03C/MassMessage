<?php 
// Establecer la variable $data['active_nav'] para el resaltado en el navbar
$data['active_nav'] = 'dashboard'; 
require_once APPROOT . '/views/includes/header.php'; 
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="fas fa-tachometer-alt me-2"></i>Bienvenido al Panel de Control</h1>
                </div>
                <div class="card-body">
                    <p class="lead">Has iniciado sesión correctamente, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>.</p>
                    <p>Desde aquí podrás gestionar tus mensajes masivos y otras configuraciones de la aplicación.</p>
                    <hr>
                    <p class="mb-0">
                        <a href="<?php echo BASEURL; ?>/messages/create" class="btn btn-success me-2">
                            <i class="fas fa-plus me-1"></i> Crear Nuevo Mensaje
                        </a>
                        <a href="<?php echo BASEURL; ?>/messages" class="btn btn-info">
                            <i class="fas fa-history me-1"></i> Ver Historial de Mensajes
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Aquí puedes agregar más contenido para el dashboard -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Estadísticas Rápidas</h5>
                </div>
                <div class="card-body">
                    <p>Total de mensajes enviados: <!-- Aquí iría una variable PHP con el dato -->XX</p>
                    <p>Contactos registrados: <!-- Aquí iría una variable PHP con el dato -->YY</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Accesos Directos</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASEURL; ?>/users/profile">Mi Perfil</a></li>
                        <li><a href="#">Configuración de Cuenta</a></li>
                        <!-- Agrega más enlaces según sea necesario -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>