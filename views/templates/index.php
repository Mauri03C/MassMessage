<?php
// Habilitar visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir constantes
define('APPROOT', dirname(__DIR__));
define('URLROOT', 'http://' . $_SERVER['HTTP_HOST'] . '/MassMessage');
define('BASEURL', URLROOT);

// Cargar archivos principales
require_once 'config/config.php';
require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';

// Función de redirección
function redirect($page) {
    header('Location: ' . URLROOT . '/' . ltrim($page, '/'));
    exit();
}

// Obtener ruta actual
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$current_path = str_replace($script_name, '', $request_uri);

// Páginas públicas
$public_pages = ['/auth/login', '/auth/forgot', '/auth/reset', '/errors/404', '/errors/500'];

// Verificar autenticación
$is_public = in_array($current_path, $public_pages) || $current_path === '/';

if (!isset($_SESSION['user_id']) && !$is_public) {
    $_SESSION['redirect_url'] = $current_path;
    redirect('auth/login');
}

if (isset($_SESSION['user_id']) && ($current_path === '/auth/login' || $current_path === '/')) {
    redirect('dashboard');
}

// Iniciar aplicación
try {
    $app = new App();
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        die('Error: ' . $e->getMessage());
    }
    redirect('errors/500');
}