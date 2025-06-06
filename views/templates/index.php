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
    $url = URLROOT . '/' . ltrim($page, '/');
    header('Location: ' . $url);
    exit();
}

// Obtener la ruta solicitada
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$current_path = str_replace($script_name, '', $request_uri);

// Debug - Ver la ruta actual
error_log("Ruta actual: " . $current_path);

// Páginas públicas
$public_pages = ['/auth/login', '/auth/forgot', '/auth/reset', '/errors/404', '/errors/500'];

// Verificar si la ruta actual es pública
$is_public = in_array($current_path, $public_pages) || $current_path === '/';

// Debug
error_log("¿Es pública? " . ($is_public ? 'Sí' : 'No'));
error_log("¿Está autenticado? " . (isset($_SESSION['user_id']) ? 'Sí' : 'No'));

// Lógica de redirección
if (!isset($_SESSION['user_id']) && !$is_public) {
    $_SESSION['redirect_url'] = $current_path;
    error_log("Redirigiendo a login desde: " . $current_path);
    redirect('auth/login');
    exit();
}

if (isset($_SESSION['user_id']) && ($current_path === '/auth/login' || $current_path === '/')) {
    error_log("Redirigiendo a dashboard desde: " . $current_path);
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