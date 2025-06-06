</main> <!-- Cierra el <main> abierto en header.php -->

    <!-- Bootstrap Bundle JS (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Script para inicializar AOS y cualquier otro script personalizado -->
    <script>
        AOS.init();
    </script>

    <?php 
    // Mostrar mensajes flash de SweetAlert2 si existen
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        echo "<script>
            Swal.fire({
                icon: '{$message['type']}',
                title: '{$message['title']}',
                text: '{$message['text']}',
                timer: {$message['timer']},
                showConfirmButton: {$message['showConfirmButton']}
            });
        </script>";
    }
    ?>
</body>
</html>