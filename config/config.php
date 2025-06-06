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
// Configuración de errores
// ============================================
if (!is_dir(APPROOT . '/logs')) {
    mkdir(APPROOT . '/logs', 0755, true);
}

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', APPROOT . '/logs/php_errors.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', APPROOT . '/logs/php_errors.log');
}

// ============================================
// Configuración de sesión segura
// ============================================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 86400); // 24 horas
ini_set('session.cookie_lifetime', 0); // Hasta que se cierre el navegador

// ============================================
// Configuración de zona horaria (Perú)
// ============================================
date_default_timezone_set('America/Lima');

// ============================================
// Configuración de carga máxima de archivos
// ============================================
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '12M');
ini_set('max_execution_time', 300); // 5 minutos
ini_set('max_input_time', 300);     // 5 minutos

// ============================================
// Constantes de la aplicación
// ============================================
define('MAX_LOGIN_ATTEMPTS', 5);
define('PASSWORD_RESET_EXPIRY', 3600); // 1 hora
define('TOKEN_LENGTH', 32);

// ============================================
// Configuración de CORS (si es necesario)
// ============================================
header('Access-Control-Allow-Origin: ' . BASEURL);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Manejar solicitudes OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
} 