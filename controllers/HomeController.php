<?php
class HomeController extends Controller {
    public function __construct() {
    }

    public function index() {
        $data = [
            'title' => 'Bienvenido a ' . APPNAME,
            'description' => 'Sistema de envío masivo de mensajes'
        ];

        $this->view('home/index', $data);
    }
}