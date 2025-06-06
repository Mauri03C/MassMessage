<?php
class DashboardController extends Controller {
    public function __construct() {
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
        }
    }

    public function index() {
        $this->view('dashboard/index');
    }
}