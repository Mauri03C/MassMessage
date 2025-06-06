<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="h4 mb-3">Iniciar Sesión</h2>
                            <p class="text-muted">Ingresa tus credenciales</p>
                        </div>

                        <?php flash('error'); ?>

                        <form action="<?php echo URLROOT; ?>/auth/login" method="post">
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo $data['email'] ?? ''; ?>"
                                       required>
                                <?php if (!empty($data['email_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                       required>
                                <?php if (!empty($data['password_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>