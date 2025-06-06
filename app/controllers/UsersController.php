<?php

class UsersController extends Controller {

    public function __construct() {
        // Asegurar que el usuario esté logueado para acceder a cualquier método de este controlador
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_url'] = $_GET['url'] ?? 'dashboard';
            redirect('auth/login');
        }
        
        // Aquí podrías cargar el modelo de usuario si lo tienes
        // $this->userModel = $this->model('User'); 
    }

    /**
     * Muestra la página del perfil del usuario.
     */
    public function profile() {
        // Simular la carga de datos del usuario (más adelante vendrían de la BD)
        // $userData = $this->userModel->findById($_SESSION['user_id']);
        
        // Datos simulados por ahora:
        $userData = (
            (object)[
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? 'Usuario Ejemplo',
                'email' => $_SESSION['user_email'] ?? 'usuario@ejemplo.com',
                'created_at' => date('Y-m-d H:i:s') // O una fecha real de la BD
            ]
        );

        $data = [
            'page_title' => 'Mi Perfil',
            'active_nav' => 'profile', // Para el resaltado en el navbar (si tienes un enlace directo)
            'user' => $userData
        ];

        $this->view('users/profile', $data);
    }

    /**
     * Procesa la actualización de los datos del perfil.
     * Este método se llamaría con POST desde el formulario de perfil.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lógica para validar y actualizar datos del usuario
            // ...

            // Ejemplo:
            // $updateData = [
            //     'name' => trim($_POST['name']),
            //     'email' => trim($_POST['email'])
            // ];
            // if ($this->userModel->updateProfile($_SESSION['user_id'], $updateData)) {
            //     // Actualizar datos en sesión si es necesario
            //     $_SESSION['user_name'] = $updateData['name'];
            //     $_SESSION['user_email'] = $updateData['email'];
            //     flash('profile_success', 'Perfil actualizado correctamente.');
            // } else {
            //     flash('profile_error', 'No se pudo actualizar el perfil.');
            // }
            // redirect('users/profile');
            echo "Lógica de actualización de perfil pendiente.";

        } else {
            redirect('users/profile');
        }
    }

    // Otros métodos relacionados con usuarios podrían ir aquí (listar usuarios para admin, etc.)

}
