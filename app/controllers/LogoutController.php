<?php
class LogoutController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();

        // Redireciona para a página principal
        $this->redirect(BASE_URL . 'principal');
    }
}
