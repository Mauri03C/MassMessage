<?php
// Habilitar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Debug: Mostrar información de la URL
if (isset($_GET['url'])) {
    error_log("URL solicitada: " . $_GET['url']);
}

// Cargar configuración primero
require_once 'config/config.php';
require_once 'app/helpers/flash_helper.php';

// Iniciar la sesión DESPUÉS de la configuración y helpers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Luego definir constantes solo si no están definidas
defined('APPROOT') or define('APPROOT', dirname(__DIR__));
defined('URLROOT') or define('URLROOT', 'http://' . $_SERVER['HTTP_HOST'] . '/MassMessage');
defined('SITENAME') or define('SITENAME', 'MassMessage');

// Cargar archivos principales
require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';

// Función para verificar si el usuario está autenticado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para redireccionar
function redirect($page) {
    $url = URLROOT . '/' . ltrim($page, '/');
    header('Location: ' . $url);
    exit();
}

// Obtener la URL solicitada
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$current_path = str_replace($script_name, '', $request_uri);

// Debug: Mostrar información de la ruta
error_log("Ruta actual: " . $current_path);

// Páginas públicas que no requieren autenticación
$public_pages = ['/auth/login', '/auth/forgot', '/auth/reset', '/errors/404', '/errors/500'];

// Verificar si la ruta actual es pública
$is_public = in_array($current_path, $public_pages) || $current_path === '/';

// Lógica de redirección
if (!isLoggedIn() && !$is_public) {
    $_SESSION['redirect_url'] = $current_path;
    redirect('auth/login');
    exit();
}

if (isLoggedIn() && ($current_path === '/auth/login' || $current_path === '/')) {
    redirect('dashboard');
    exit();
}

// Iniciar la aplicación
try {
    $app = new App();
} catch (Exception $e) {
    error_log('Error en la aplicación: ' . $e->getMessage());
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        die('Error: ' . $e->getMessage());
    }
    redirect('errors/500');
}