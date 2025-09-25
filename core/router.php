<?php
// core/Router.php

class Router {
    public function dispatch($url) {
        // Remove barras extras e filtra a URL
        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);

        // Separa em partes -> /controller/metodo/param1/param2
        $parts = explode('/', $url);

        // Controller
        $controllerName = !empty($parts[0]) ? ucfirst($parts[0]) . 'Controller' : 'HomeController';

        // Método
        $method = $parts[1] ?? 'index';

        // Parâmetros (restante do array)
        $params = array_slice($parts, 2);

        // Verifica se o controller existe
        if (!class_exists($controllerName)) {
            http_response_code(404);
            echo "Controller <b>$controllerName</b> não encontrado.";
            exit;
        }

        $controller = new $controllerName();

        // Verifica se o método existe no controller
        if (!method_exists($controller, $method)) {
            http_response_code(404);
            echo "Método <b>$method</b> não encontrado em $controllerName.";
            exit;
        }

        // Chama o método com os parâmetros
        call_user_func_array([$controller, $method], $params);
    }
}
