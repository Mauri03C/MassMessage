<?php
// Habilitar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Iniciar la sesión
session_start();

// Definir constantes de la aplicación
define('APPROOT', dirname(__DIR__));
define('URLROOT', 'http://'.$_SERVER['HTTP_HOST'].'/MassMessage');

// Cargar archivos principales
require_once 'config/config.php';
require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';

// Verificar si el usuario está autenticado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para redireccionar
function redirect($page) {
    header('location: ' . URLROOT . '/' . $page);
    exit();
}

// Redirigir al login si no está autenticado
if (!isLoggedIn() && !isset($_GET['url']) || (isset($_GET['url']) && strpos($_GET['url'], 'auth/') === 0)) {
    redirect('auth/login');
}

// Iniciar la aplicación
$app = new App();