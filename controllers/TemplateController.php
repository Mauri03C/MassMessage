<?php
class TemplateController extends Controller {
    private $templateModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        $this->templateModel = $this->model('Template');
    }

    public function index() {
        $templates = $this->templateModel->getTemplatesByUser($_SESSION['user_id']);
        $data = ['templates' => $templates];
        $this->view('templates/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'user_id' => $_SESSION['user_id'],
                'name' => trim($_POST['name']),
                'type' => trim($_POST['type']),
                'subject' => trim($_POST['subject']),
                'content' => trim($_POST['content']),
                'errors' => []
            ];

            if (empty($data['name'])) {
                $data['errors']['name'] = 'El nombre es requerido';
            }
            if (empty($data['type'])) {
                $data['errors']['type'] = 'El tipo es requerido';
            }
            if (empty($data['subject'])) {
                $data['errors']['subject'] = 'El asunto es requerido';
            }
            if (empty($data['content'])) {
                $data['errors']['content'] = 'El contenido es requerido';
            }

            if (empty($data['errors'])) {
                if ($this->templateModel->addTemplate($data)) {
                    flash('template_success', 'Plantilla creada correctamente');
                    redirect('templates');
                } else {
                    die('Algo salió mal');
                }
            } else {
                $this->view('templates/create', $data);
            }
        } else {
            $data = [
                'name' => '',
                'type' => '',
                'subject' => '',
                'content' => '',
                'errors' => []
            ];
            $this->view('templates/create', $data);
        }
    }

    public function edit($id = null) {
        if ($id === null) {
            redirect('templates');
        }

        $template = $this->templateModel->getTemplateById($id);
        if (!$template || $template->user_id !== $_SESSION['user_id']) {
            flash('template_error', 'Plantilla no encontrada', 'alert alert-danger');
            redirect('templates');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'user_id' => $_SESSION['user_id'],
                'name' => trim($_POST['name']),
                'type' => trim($_POST['type']),
                'subject' => trim($_POST['subject']),
                'content' => trim($_POST['content']),
                'errors' => []
            ];

            if (empty($data['name'])) {
                $data['errors']['name'] = 'El nombre es requerido';
            }
            if (empty($data['type'])) {
                $data['errors']['type'] = 'El tipo es requerido';
            }
            if (empty($data['subject'])) {
                $data['errors']['subject'] = 'El asunto es requerido';
            }
            if (empty($data['content'])) {
                $data['errors']['content'] = 'El contenido es requerido';
            }

            if (empty($data['errors'])) {
                if ($this->templateModel->updateTemplate($data)) {
                    flash('template_success', 'Plantilla actualizada correctamente');
                    redirect('templates');
                } else {
                    die('Algo salió mal');
                }
            } else {
                $this->view('templates/edit', $data);
            }
        } else {
            $data = [
                'id' => $template->id,
                'name' => $template->name,
                'type' => $template->type,
                'subject' => $template->subject,
                'content' => $template->content,
                'errors' => []
            ];
            $this->view('templates/edit', $data);
        }
    }

    public function delete($id = null) {
        if ($id === null) {
            redirect('templates');
        }

        $template = $this->templateModel->getTemplateById($id);
        if (!$template || $template->user_id !== $_SESSION['user_id']) {
            flash('template_error', 'Plantilla no encontrada', 'alert alert-danger');
            redirect('templates');
        }

        if ($this->templateModel->deleteTemplate($id, $_SESSION['user_id'])) {
            flash('template_success', 'Plantilla eliminada correctamente');
        } else {
            flash('template_error', 'No se pudo eliminar la plantilla', 'alert alert-danger');
        }
        redirect('templates');
    }
}