<?php
class App {
    protected $controller = 'Home'; // Controlador por defecto
    protected $method = 'index';    // Método por defecto
    protected $params = [];         // Parámetros por defecto
    protected $controllerPaths = [
        'app/controllers/',
        'controllers/'
    ];

    public function __construct() {
        $url = $this->parseURL();
        
        // Establecer el controlador desde la URL
        // Asegurarse de que $url[0] existe y no es nulo antes de usarlo
        if (!empty($url[0])) {
            $controllerName = ucfirst($url[0]);
            unset($url[0]);
        } else {
            $controllerName = $this->controller; // Usar controlador por defecto si no hay nada en la URL
        }
        error_log('[App.php] Intentando cargar controlador: ' . $controllerName);

        // Buscar el controlador en las rutas especificadas
        $controllerFound = false;
        foreach ($this->controllerPaths as $path) {
            // Modificación para usar ruta absoluta con APPROOT
            $controllerFile = APPROOT . '/' . $path . $controllerName . 'Controller.php';
            error_log('[App.php] Buscando archivo controlador (ruta absoluta): ' . $controllerFile); // LOG MODIFICADO
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerClass = $controllerName . 'Controller';
                if (class_exists($controllerClass)) {
                    $this->controller = new $controllerClass();
                    $controllerFound = true;
                    error_log('[App.php] Controlador cargado: ' . $controllerClass);
                    break;
                } else {
                    // Log específico si el archivo existe pero la clase no
                    error_log('[App.php] Archivo controlador ENCONTRADO: ' . $controllerFile . ', PERO la clase NO EXISTE: ' . $controllerClass);
                }
            } else {
                error_log('[App.php] Archivo controlador NO ENCONTRADO en ruta absoluta: ' . $controllerFile);
            }
        }

        // Si no se encontró el controlador, cargar el controlador de error o Home
        if (!$controllerFound) {
            error_log('[App.php] Controlador NO ENCONTRADO: ' . $controllerName . 'Controller.php. Cargando HomeController->notFound.');
            // Intentar cargar HomeController si no se encontró el controlador específico
            // o un controlador de Errores dedicado.
            require_once 'app/controllers/HomeController.php'; // Asegúrate que esta ruta es correcta
            $this->controller = new HomeController();
            $this->method = 'notFound'; // Asumiendo que HomeController tiene un método notFound
        }

        // Establecer el método desde la URL
        // Asegurarse de que $url[1] existe y no es nulo
        if (!empty($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            } else {
                // Si el método no existe, podrías redirigir a notFound o manejarlo
                error_log('[App.php] Método NO ENCONTRADO: ' . $url[1] . ' en ' . get_class($this->controller) . '. Usando notFound.');
                $this->method = 'notFound'; // O alguna otra página de error/método por defecto
            }
        } else {
            error_log('[App.php] No se especificó método en URL. Usando método por defecto: ' . $this->method . ' en ' . get_class($this->controller));
        }

        // Obtener los parámetros restantes
        $this->params = $url ? array_values($url) : [];

        // Llamar al controlador y método con los parámetros
        error_log('[App.php] Llamando a: ' . get_class($this->controller) . '->' . $this->method . ' con params: ' . implode(', ', $this->params));
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return []; // Devolver un array vacío si 'url' no está seteado
    }
}