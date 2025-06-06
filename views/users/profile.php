<?php 
// $data['active_nav'], $data['page_title'], $data['user'] son pasadas desde UsersController::profile()
require_once APPROOT . '/views/includes/header.php'; 
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($data['page_title'] ?? 'Mi Perfil'); ?></h1>
                </div>
                <div class="card-body">
                    <?php // Para mostrar mensajes de éxito/error después de actualizar el perfil ?>
                    <?php // flash('profile_success'); ?>
                    <?php // flash('profile_error'); ?>

                    <form action="<?php echo BASEURL; ?>/users/update" method="post">
                        <?php // Campo CSRF si lo implementas ?>
                        <!-- <input type="hidden" name="csrf_token" value="<?php // echo createCSRFToken(); ?>"> -->

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre Completo:</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($data['user']->name ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($data['user']->email ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual (dejar en blanco para no cambiar):</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="off">
                            <div class="form-text">Necesaria si deseas cambiar tu contraseña.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">Nueva Contraseña:</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="new-password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_new_password" class="form-label">Confirmar Nueva Contraseña:</label>
                                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" autocomplete="new-password">
                            </div>
                        </div>
                        
                        <hr>
                        <p class="text-muted small">Miembro desde: <?php echo htmlspecialchars(date('d/m/Y', strtotime($data['user']->created_at ?? time()))); ?></p>
                        
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo BASEURL; ?>/dashboard" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>