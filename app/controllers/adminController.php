<?php
class AdminController extends Controller {
private $usuarioModel;
public function __construct() {
    $this->usuarioModel = new Usuario();
}
public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if ($usuario && $usuario['role'] === 'admin' && password_verify($senha, $usuario['senha'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_role'] = $usuario['role'];

            $this->redirect(BASE_URL . 'admin/dashboard');
        } else {
            $this->view('admin/login', ['erro' => 'Credenciais inválidas']);
        }
    } else {
        $this->view('admin/login');
    }
}

public function dashboard() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['usuario_role']) || $_SESSION['usuario_role'] !== 'admin') {
        $this->redirect(BASE_URL . 'admin/login');
    }

    $nome = $_SESSION['usuario_nome'];
    $chaveModel = new ChaveGestor();

    $novaChave = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gerar_chave'])) {
        $novaChave = $chaveModel->gerarChave();

        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['novaChave'] = $novaChave;

        $this->redirect(BASE_URL . 'admin/dashboard');
    }

    $novaChave = $_SESSION['novaChave'] ?? null;
    unset($_SESSION['novaChave']); 

    $this->view('admin/dashboard', compact('nome', 'novaChave'));
}

public function dashboardGestorAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_role']) || $_SESSION['usuario_role'] !== 'admin') {
        $this->redirect(BASE_URL . 'admin/login');
        return;
    }

    $this->view('admin/dashboardGestorAdmin');
}


public function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_destroy();
    $this->redirect(BASE_URL . 'admin/login');
}

}
