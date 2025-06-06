<?php
class AuthController extends Controller {
    private $userModel;
    private $loginAttempts = 5; // Número máximo de intentos de inicio de sesión
    private $lockoutTime = 300; // Tiempo de bloqueo en segundos (5 minutos)

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function login() {
        // Redirigir si ya está autenticado
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        $data = [
            'title' => 'Iniciar Sesión',
            'email' => '',
            'password' => '',
            'remember' => false,
            'email_err' => '',
            'password_err' => '',
            'login_attempts' => $_SESSION['login_attempts'] ?? 0,
            'lockout_time' => $_SESSION['lockout_time'] ?? 0
        ];

        // Verificar si el usuario está bloqueado temporalmente
        if ($data['login_attempts'] >= $this->loginAttempts) {
            $timeLeft = $data['lockout_time'] - time();
            if ($timeLeft > 0) {
                $minutes = ceil($timeLeft / 60);
                setFlash('error', 'Demasiados intentos fallidos. Por favor, intente nuevamente en ' . $minutes . ' minutos.');
                $this->view('auth/login', $data);
                return;
            } else {
                // Reiniciar contador si ha pasado el tiempo de bloqueo
                unset($_SESSION['login_attempts']);
                unset($_SESSION['lockout_time']);
                $data['login_attempts'] = 0;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar datos POST
            $_POST = filter_input_array(INPUT_POST, [
                'email' => FILTER_SANITIZE_EMAIL,
                'password' => FILTER_SANITIZE_STRING,
                'remember' => FILTER_VALIDATE_BOOLEAN
            ]);

            $data['email'] = trim($_POST['email'] ?? '');
            $data['password'] = $_POST['password'] ?? '';
            $data['remember'] = $_POST['remember'] ?? false;

            // Validar email
            if (empty($data['email'])) {
                $data['email_err'] = 'Por favor ingrese su correo electrónico';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Formato de correo electrónico inválido';
            } elseif (!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Credenciales incorrectas';
            }

            // Validar contraseña
            if (empty($data['password'])) {
                $data['password_err'] = 'Por favor ingrese su contraseña';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'La contraseña debe tener al menos 6 caracteres';
            }

            // Si no hay errores, intentar login
            if (empty($data['email_err']) && empty($data['password_err'])) {
                $user = $this->userModel->login($data['email'], $data['password']);
                
                if ($user) {
                    // Restablecer contador de intentos fallidos
                    unset($_SESSION['login_attempts']);
                    unset($_SESSION['lockout_time']);
                    
                    // Crear sesión
                    $this->createUserSession($user);
                    
                    // Recordar sesión si se solicitó
                    if ($data['remember']) {
                        $this->rememberMe($user->id);
                    }

                    // Registrar inicio de sesión
                    $this->userModel->logActivity($user->id, 'login', 'Inicio de sesión exitoso');
                    
                    // ***** INICIO DE CAMBIO TEMPORAL PARA DIAGNÓSTICO *****
                    error_log('[AuthController] DIAGNÓSTICO: Forzando redirección a dashboard.');
                    unset($_SESSION['redirect_url']); // Limpiar por si acaso
                    redirect('dashboard');
                    // ***** FIN DE CAMBIO TEMPORAL PARA DIAGNÓSTICO *****
                    
                } else {
                    // Incrementar contador de intentos fallidos
                    $data['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                    $_SESSION['login_attempts'] = $data['login_attempts'];
                    
                    // Establecer tiempo de bloqueo si se supera el límite
                    if ($data['login_attempts'] >= $this->loginAttempts) {
                        $_SESSION['lockout_time'] = time() + $this->lockoutTime;
                        $data['lockout_time'] = $_SESSION['lockout_time'];
                        setFlash('error', 'Demasiados intentos fallidos. Por favor, intente nuevamente en 5 minutos.');
                    } else {
                        $data['password_err'] = 'Credenciales incorrectas. Intentos restantes: ' . ($this->loginAttempts - $data['login_attempts']);
                    }
                }
            }
        }

        // Cargar vista con errores si los hay
        $this->view('auth/login', $data);
    }

    public function logout() {
        if (isLoggedIn()) {
            // Registrar actividad
            $this->userModel->logActivity($_SESSION['user_id'], 'logout', 'Cierre de sesión');
            
            // Eliminar cookie de "recordarme" si existe
            if (isset($_COOKIE['remember_token'])) {
                $this->forgetMe();
            }
            
            // Destruir la sesión
            session_unset();
            session_destroy();
            
            // Redirigir al login
            redirect('auth/login');
        }
    }

    private function createUserSession($user) {
        // Regenerar ID de sesión para prevenir fijación de sesión
        session_regenerate_id(true);
        
        // Establecer variables de sesión
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['last_activity'] = time();
        
        // Obtener y guardar el rol del usuario
        $roles = $this->userModel->getUserRoles($user->id);
        if (!empty($roles)) {
            $_SESSION['user_role'] = $roles[0]->name;
        }
        
        // Regenerar token CSRF
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function rememberMe($userId) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (86400 * 30); // 30 días
        
        // Guardar token en la base de datos
        if ($this->userModel->storeRememberToken($userId, $token, $expires)) {
            setcookie(
                'remember_token',
                $token,
                $expires,
                '/',
                '',
                isset($_SERVER['HTTPS']), // Secure
                true // HttpOnly
            );
        }
    }

    private function forgetMe() {
        if (isset($_COOKIE['remember_token'])) {
            $this->userModel->deleteRememberToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }

    public function checkSession() {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            // 30 minutos de inactividad
            $this->logout();
        } else {
            $_SESSION['last_activity'] = time();
        }
    }

    public function checkCSRFToken($token) {
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die('Token CSRF inválido');
        }
    }
}