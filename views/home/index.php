<?php require APPROOT . '/views/templates/header.php'; ?>

<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-lg-6" data-aos="fade-right">
            <h1 class="display-4 fw-bold mb-4"><?php echo $data['title']; ?></h1>
            <p class="lead mb-4"><?php echo $data['description']; ?></p>
            <p class="mb-4">Envía mensajes masivos a través de WhatsApp, SMS y correo electrónico de manera fácil y eficiente.</p>
            
            <?php if(!isset($_SESSION['user_id'])) : ?>
                <div class="d-grid gap-3 d-sm-flex">
                    <a href="<?php echo BASEURL; ?>/auth/register" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i>Comenzar
                    </a>
                    <a href="<?php echo BASEURL; ?>/auth/login" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </a>
                </div>
            <?php else : ?>
                <div class="d-grid gap-3 d-sm-flex">
                    <a href="<?php echo BASEURL; ?>/messages/create" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Mensajes
                    </a>
                    <a href="<?php echo BASEURL; ?>/messages" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-history me-2"></i>Ver Historial
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-6" data-aos="fade-left">
            <div class="row g-3">
                <div class="col-6">
                    <div class="card h-100 p-4 text-center">
                        <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                        <h3>WhatsApp</h3>
                        <p>Envía mensajes masivos a través de WhatsApp Business API.</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100 p-4 text-center">
                        <i class="fas fa-sms fa-3x text-primary mb-3"></i>
                        <h3>SMS</h3>
                        <p>Envía mensajes de texto a múltiples destinatarios.</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100 p-4 text-center">
                        <i class="fas fa-envelope fa-3x text-danger mb-3"></i>
                        <h3>Email</h3>
                        <p>Envía correos electrónicos masivos con archivos adjuntos.</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100 p-4 text-center">
                        <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                        <h3>Reportes</h3>
                        <p>Obtén estadísticas detalladas de tus envíos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="bg-light py-5" data-aos="fade-up">
    <div class="container">
        <h2 class="text-center mb-5">Características Principales</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 p-4">
                    <div class="text-primary mb-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h4>Gestión de Contactos</h4>
                    <p>Organiza y gestiona tus contactos de manera eficiente. Importa contactos desde Excel o CSV.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 p-4">
                    <div class="text-primary mb-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h4>Programación de Envíos</h4>
                    <p>Programa tus mensajes para ser enviados en el momento que desees.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 p-4">
                    <div class="text-primary mb-3">
                        <i class="fas fa-chart-pie fa-2x"></i>
                    </div>
                    <h4>Análisis Detallado</h4>
                    <p>Obtén reportes detallados sobre el estado y efectividad de tus envíos.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/templates/footer.php'; ?>