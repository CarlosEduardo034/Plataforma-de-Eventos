<?php
class PrincipalController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $usuarioNome = $_SESSION['usuario_nome'] ?? null;
        $usuarioRole = $_SESSION['usuario_role'] ?? 'comum';

        $this->view('principal/index', compact('usuarioNome', 'usuarioRole'));
    }
}
