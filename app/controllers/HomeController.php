<?php
class HomeController extends Controller {

    public function __construct() {
        // Aquí podrías cargar modelos si los necesitas para todas las acciones del Home
        // Ejemplo: $this->userModel = $this->model('User');
    }

    public function index() {
        // Verificar si la función isLoggedIn está disponible
        // Esta función fue definida en tu index.php
        if (function_exists('isLoggedIn')) {
            if (isLoggedIn()) {
                // Si el usuario está logueado, redirigir al dashboard
                // Asegúrate que la función redirect() esté disponible globalmente o defínela en Controller.php
                if (function_exists('redirect')) {
                    redirect('dashboard'); // Asume que tienes un DashboardController con un método index
                } else {
                    // Fallback si redirect no está definida, aunque debería estarlo desde index.php
                    header('Location: ' . URLROOT . '/dashboard');
                    exit();
                }
            } else {
                // Si el usuario no está logueado, redirigir a la página de login
                if (function_exists('redirect')) {
                    redirect('auth/login');
                } else {
                    header('Location: ' . URLROOT . '/auth/login');
                    exit();
                }
            }
        } else {
            // Fallback o error si isLoggedIn no está definida
            // Esto no debería suceder si index.php se carga correctamente
            die('Error crítico: La función de autenticación no está disponible.');
        }
    }

    public function notFound() {
        // Cargar una vista de error 404
        // Asegúrate de tener una vista views/error/404.php o similar
        // O simplemente muestra un mensaje
        // $this->view('error/404'); 
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Página No Encontrada</h1>";
        echo "<p>La página que buscas no existe.</p>";
        echo "<a href=\"" . URLROOT . "\">Volver al inicio</a>";
        // Considera crear una vista más elaborada para esto en views/pages/notFound.php o similar
        // y luego llamarla con $this->view('pages/notFound');
    }
}
