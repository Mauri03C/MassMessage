<?php 
// Incluir el encabezado
require_once APPROOT . '/views/includes/header.php'; 
?>

<div class="login-page bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <!-- Logo y título -->
                        <div class="text-center mb-4">
                            <img src="<?php echo URLROOT; ?>/public/img/logo.png" alt="Logo" class="mb-3" style="max-height: 60px;">
                            <h2 class="h4 mb-2 text-primary">Bienvenido a <?php echo SITENAME; ?></h2>
                            <p class="text-muted">Ingresa tus credenciales para continuar</p>
                        </div>

                        <!-- Mensajes de error/success -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de login -->
                        <form action="<?php echo URLROOT; ?>/auth/login" method="post" class="needs-validation" novalidate>
                            <!-- Campo de correo electrónico -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input 
                                        type="email" 
                                        id="email"
                                        name="email" 
                                        class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                        value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>"
                                        placeholder="correo@ejemplo.com"
                                        required
                                        autofocus
                                    >
                                    <div class="invalid-feedback">
                                        <?php echo $data['email_err'] ?? 'Por favor ingresa un correo electrónico válido'; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo de contraseña -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <a href="<?php echo URLROOT; ?>/auth/forgot" class="small text-decoration-none">¿Olvidaste tu contraseña?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input 
                                        type="password" 
                                        id="password"
                                        name="password" 
                                        class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                        placeholder="••••••••"
                                        required
                                    >
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        <?php echo $data['password_err'] ?? 'Por favor ingresa tu contraseña'; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Recordar sesión -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Recordar sesión</label>
                            </div>

                            <!-- Botón de envío -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                                </button>
                            </div>
                        </form>

                        <!-- Enlaces adicionales -->
                        <div class="text-center mt-4">
                            <p class="mb-0">¿No tienes una cuenta? 
                                <a href="<?php echo URLROOT; ?>/auth/register" class="text-decoration-none">Regístrate</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Información de copyright -->
                <div class="text-center mt-4 text-muted">
                    <p class="small">&copy; <?php echo date('Y'); ?> <?php echo SITENAME; ?>. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para validación del formulario -->
<script>
// Validación de formulario del lado del cliente
(function () {
    'use strict'

    // Obtener todos los formularios que necesitan validación
    var forms = document.querySelectorAll('.needs-validation')

    // Validar campos al enviar el formulario
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
    })

    // Alternar visibilidad de la contraseña
    document.querySelectorAll('.toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
})()
</script>

<!-- Estilos personalizados -->
<style>
.login-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
.min-vh-100 {
    min-height: 100vh;
}
.card {
    border-radius: 10px;
    border: none;
    overflow: hidden;
}
.input-group-text {
    background-color: #f8f9fa;
}
.btn-primary {
    background-color: #4e73df;
    border: none;
    padding: 0.75rem;
}
.btn-primary:hover {
    background-color: #2e59d9;
}
.toggle-password {
    cursor: pointer;
}
</style>

<?php 
// Incluir el pie de página
require_once APPROOT . '/views/includes/footer.php'; 
?>