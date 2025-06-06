<?php

class MessagesController extends Controller {

    public function __construct() {
        // Aquí podrías cargar modelos si los necesitas, ej: $this->messageModel = $this->model('Message');
        // También, asegurar que el usuario esté logueado si es una sección protegida
        if (!isset($_SESSION['user_id'])) {
            // Guardar la URL que el usuario intentaba visitar para redirigir después del login
            $_SESSION['redirect_url'] = $_GET['url'] ?? 'dashboard'; 
            redirect('auth/login');
        }
    }

    /**
     * Muestra el formulario para crear un nuevo mensaje.
     */
    public function create() {
        // Datos que se pasarán a la vista
        $data = [
            'page_title' => 'Crear Nuevo Mensaje',
            'active_nav' => 'new_message' // Para resaltar en el navbar
            // Puedes añadir más datos aquí, como listas para selectores, etc.
        ];

        // Cargar la vista del formulario de creación
        $this->view('messages/create', $data);
    }

    /**
     * Procesa el envío del formulario de creación de mensajes.
     * Este método se llamaría cuando el formulario haga POST a messages/store (o similar)
     */
    public function store() {
        // Lógica para procesar el formulario POST
        // Validar datos, guardar en la BD, etc.
        // Por ahora, solo redirigimos o mostramos un mensaje
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar formulario
            // ...

            // Ejemplo de redirección con mensaje flash (necesitarías un helper para flash messages)
            // flash('message_created', 'Mensaje enviado exitosamente');
            // redirect('messages');
            echo "Formulario enviado (lógica de store() pendiente).";
        } else {
            // Si no es POST, redirigir a create o mostrar error
            redirect('messages/create');
        }
    }

    /**
     * Muestra el historial de mensajes.
     */
    public function index() {
        // Aquí, más adelante, cargarías los mensajes desde la base de datos
        // Ejemplo: $messages = $this->messageModel->getAllMessagesForUser($_SESSION['user_id']);
        $messages = []; // Por ahora, un array vacío para evitar el error

        $data = [
            'page_title' => 'Historial de Mensajes',
            'active_nav' => 'history',
            'messages' => $messages // Pasar los mensajes a la vista
        ];
        $this->view('messages/index', $data); 
    }

    // Puedes añadir más métodos como edit, update, delete, show, etc.

}
