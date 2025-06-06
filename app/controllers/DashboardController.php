<?php
class DashboardController extends Controller {
    public function __construct() {
        error_log('[DashboardController] Instanciado.'); // LOG
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            // Guardar la URL que el usuario intentaba visitar
            $_SESSION['redirect_url'] = $_GET['url'] ?? ''; // Captura la URL actual de la query string
            error_log('[DashboardController] Sesión user_id NO encontrada. Guardando redirect_url: ' . ($_SESSION['redirect_url'] ?? 'ninguna') . '. Redirigiendo a auth/login.'); // LOG
            redirect('auth/login');
        } else {
            error_log('[DashboardController] Sesión user_id ENCONTRADA: ' . $_SESSION['user_id']); // LOG
        }
    }

    public function index() {
        error_log('[DashboardController] Ejecutando método index.'); // LOG
        $this->view('dashboard/index');
    }
}