<?php
class Controller {

    protected function view($path, $data = []) {
        extract($data);

        $viewFile = __DIR__ . '/../app/views/' . $path . '.php';
        if (!file_exists($viewFile)) {
            throw new Exception("View {$viewFile} não encontrada.");
        }

        require $viewFile;
    }

    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
}
