<?php
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function login() {
        // Si el usuario ya está autenticado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar el formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'email_err' => '',
                'password_err' => ''
            ];

            // Validar email
            if (empty($data['email'])) {
                $data['email_err'] = 'Por favor ingrese su correo electrónico';
            } elseif (!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Credenciales incorrectas';
            }

            // Validar contraseña
            if (empty($data['password'])) {
                $data['password_err'] = 'Por favor ingrese su contraseña';
            }

            // Si no hay errores, intentar iniciar sesión
            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                
                if ($loggedInUser) {
                    // Verificar si el usuario está activo
                    if ($loggedInUser->status !== 'active') {
                        flash('error', 'Su cuenta está inactiva. Por favor, contacte al administrador.', 'alert alert-danger');
                        $this->view('auth/login', $data);
                        return;
                    }
                    
                    // Crear la sesión
                    $this->createUserSession($loggedInUser);
                    
                    // Registrar actividad
                    $this->userModel->logActivity($loggedInUser->id, 'login', 'Inicio de sesión exitoso');
                    
                    // Redirigir al dashboard
                    redirect('dashboard');
                } else {
                    $data['password_err'] = 'Credenciales incorrectas';
                    $this->view('auth/login', $data);
                }
            } else {
                // Cargar vista con errores
                $this->view('auth/login', $data);
            }
        } else {
            // Cargar vista de inicio de sesión
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];
            $this->view('auth/login', $data);
        }
    }

    public function logout() {
        if (isLoggedIn()) {
            // Registrar actividad
            $this->userModel->logActivity($_SESSION['user_id'], 'logout', 'Cierre de sesión');
            
            // Destruir la sesión
            unset($_SESSION['user_id']);
            unset($_SESSION['user_email']);
            unset($_SESSION['user_name']);
            unset($_SESSION['user_role']);
            session_destroy();
            
            // Redirigir al login
            redirect('auth/login');
        }
    }

    private function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        
        // Obtener y guardar el rol del usuario
        $roles = $this->userModel->getUserRoles($user->id);
        if (!empty($roles)) {
            $_SESSION['user_role'] = $roles[0]->name; // Tomamos el primer rol
        }
    }
}
