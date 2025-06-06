</main>
    
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo APPNAME; ?></h5>
                    <p>Sistema de envío masivo de mensajes</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-links">
                        <a href="#" class="text-light me-3" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <small>&copy; <?php echo date('Y'); ?> <?php echo APPNAME; ?>. Todos los derechos reservados.</small>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="<?php echo BASEURL; ?>/assets/js/main.js"></script>
    <script>
        // Inicializar AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Mostrar/ocultar botón de ir arriba
        window.addEventListener('scroll', function() {
            const goTopBtn = document.querySelector('.go-top');
            if (window.scrollY > 300) {
                goTopBtn.classList.add('show');
            } else {
                goTopBtn.classList.remove('show');
            }
        });
        
        // Manejar notificaciones flash
        <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '<?php echo addslashes($_SESSION['success']); ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($_SESSION['error']); ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>