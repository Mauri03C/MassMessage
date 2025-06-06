<?php
// ============================================
// Configuración del entorno
// ============================================
define('ENVIRONMENT', 'development'); // 'production' o 'development'

// ============================================
// Configuración de la aplicación
// ============================================
define('APPNAME', 'MassMessage');
define('BASEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/MassMessage');
define('APPROOT', dirname(dirname(__FILE__)));
define('SITENAME', 'MassMessage');

// ============================================
// Configuración de sesión segura (debe ser lo primero después de las constantes básicas)
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    // Configuración de cookies seguras
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $httponly = true;
    $samesite = 'Lax';
    
    // Configuración de cookies de sesión
    session_set_cookie_params([
        'lifetime' => 0, // Hasta que se cierre el navegador
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);
    
    // Configuración de sesión
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_secure', $secure ? '1' : '0');
    ini_set('session.cookie_samesite', $samesite);
    ini_set('session.gc_maxlifetime', '86400'); // 24 horas
    ini_set('session.cookie_lifetime', '0');
    
    // Iniciar la sesión
    session_start();
}

// ============================================
// Configuración de errores
// ============================================
if (!is_dir(APPROOT . '/logs')) {
    mkdir(APPROOT . '/logs', 0755, true);
}

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
    ini_set('error_log', APPROOT . '/logs/php_errors.log');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', APPROOT . '/logs/php_errors.log');
}

// ============================================
// Configuración de la base de datos
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Usuario por defecto de XAMPP
define('DB_PASS', '');     // Contraseña por defecto de XAMPP (vacía)
define('DB_NAME', 'massmessage');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// Configuración de Twilio (opcional)
// ============================================
define('TWILIO_ACCOUNT_SID', '');
define('TWILIO_AUTH_TOKEN', '');
define('TWILIO_PHONE_NUMBER', '');
define('TWILIO_WHATSAPP_NUMBER', '');

// ============================================
// Configuración de correo electrónico SMTP
// ============================================
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu_email@gmail.com'); // Reemplazar con tu email
define('SMTP_PASS', ''); // Usar contraseña de aplicación para Gmail
define('SMTP_FROM_EMAIL', 'noreply@tudominio.com');
define('SMTP_FROM_NAME', 'MassMessage');

// ============================================
// Configuración de Google API (opcional)
// ============================================
define('GOOGLE_CLIENT_ID', '');
define('GOOGLE_CLIENT_SECRET', '');
define('GOOGLE_REDIRECT_URI', BASEURL . '/auth/google/callback');

// ============================================
// Configuración de zona horaria (Perú)
// ============================================
date_default_timezone_set('America/Lima');

// ============================================
// Configuración de carga máxima de archivos
// ============================================
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '12M');
ini_set('max_execution_time', '300'); // 5 minutos
ini_set('max_input_time', '300');     // 5 minutos

// ============================================
// Constantes de la aplicación
// ============================================
define('MAX_LOGIN_ATTEMPTS', 5);
define('PASSWORD_RESET_EXPIRY', 3600); // 1 hora
define('TOKEN_LENGTH', 32);
define('CSRF_TOKEN_NAME', 'csrf_token');
define('REMEMBER_ME_COOKIE', 'remember_me');
define('REMEMBER_ME_EXPIRY', 2592000); // 30 días

// ============================================
// Configuración de CORS
// ============================================
header('Access-Control-Allow-Origin: ' . BASEURL);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 3600');

// Si es una petición OPTIONS, terminar la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================
// Funciones de ayuda
// ============================================

/**
 * Genera un token CSRF si no existe
 * @return string Token CSRF
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verifica un token CSRF
 * @param string $token Token a verificar
 * @return bool True si el token es válido
 */
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}