<?php
// Configuración de la aplicación
define('APPNAME', 'MassMessage');
define('BASEURL', 'http://localhost/MassMessage');
define('APPROOT', dirname(dirname(__FILE__)));

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
// NOTA: Reemplaza 'tu_contraseña_segura' con tu contraseña real
define('DB_PASS', 'tu_contraseña_segura');
define('DB_NAME', 'massmessage');

// Configuración de Twilio
define('TWILIO_ACCOUNT_SID', 'tu_account_sid');
define('TWILIO_AUTH_TOKEN', 'tu_auth_token');
define('TWILIO_PHONE_NUMBER', 'tu_numero_twilio');
define('TWILIO_WHATSAPP_NUMBER', 'tu_numero_whatsapp');

// Configuración de correo electrónico SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu_email@gmail.com');
// NOTA: Usa una contraseña de aplicación si usas Gmail con verificación en dos pasos
define('SMTP_PASS', 'tu_contraseña');

// Configuración de Google API
define('GOOGLE_CLIENT_ID', 'tu_client_id');
define('GOOGLE_CLIENT_SECRET', 'tu_client_secret');

// Configuración de la aplicación
error_reporting(E_ALL);
ini_set('display_errors', 0); // Cambiar a 0 en producción
ini_set('log_errors', 1);
ini_set('error_log', APPROOT . '/logs/error.log');

// Configuración de sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_only_cookies', 1);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');