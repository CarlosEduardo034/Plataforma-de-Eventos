<?php

class UsuarioController extends Controller {
    private $usuarioModel;

public function __construct() {
    $this->usuarioModel = new Usuario();
}

public function cadastro() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $senhaConfirm = $_POST['senha_confirm'] ?? '';

        // Campos obrigatórios
        if (!$nome || !$email || !$senha || !$senhaConfirm) {
            $erro = 'Preencha todos os campos.';
            $this->view('usuario/cadastro', compact('erro', 'nome', 'email'));
            return;
        }

        // Validação do email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Email inválido.';
            $this->view('usuario/cadastro', compact('erro', 'nome', 'email'));
            return;
        }

        // Checar se email já existe
        if ($this->usuarioModel->buscarPorEmail($email)) {
            $erro = 'Email já cadastrado.';
            $this->view('usuario/cadastro', compact('erro', 'nome', 'email'));
            return;
        }

        // Validação da senha
        if (strlen($senha) < 6 || !preg_match('/[a-zA-Z]/', $senha) || !preg_match('/\d/', $senha)) {
            $erro = 'Senha deve ter pelo menos 6 caracteres, incluindo letras e números.';
            $this->view('usuario/cadastro', compact('erro', 'nome', 'email'));
            return;
        }

        // Confirmação de senha
        if ($senha !== $senhaConfirm) {
            $erro = 'Senhas não conferem.';
            $this->view('usuario/cadastro', compact('erro', 'nome', 'email'));
            return;
        }

        // Criar usuário comum
        $usuario = $this->usuarioModel->criar($nome, $email, $senha, 'comum');

        session_start();
        $_SESSION['usuario_id'] = $this->usuarioModel->buscarPorEmail($email)['id'];
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_role'] = 'comum';

        $this->redirect(BASE_URL . 'principal');
    } else {
        $this->view('usuario/cadastro');
    }
}

public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if ($usuario && $usuario['role'] === 'comum' && password_verify($senha, $usuario['senha'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_role'] = $usuario['role'];

            $this->redirect(BASE_URL . 'principal');
        } else {
            $erro = 'Credenciais inválidas';
            $this->view('usuario/login', compact('erro', 'email'));
        }
    } else {
        $this->view('usuario/login');
    }
}

public function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_destroy();
    $this->redirect(BASE_URL . 'usuario/login');
}

public function perfil() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_role'] !== 'comum') {
        $this->redirect(BASE_URL . 'usuario/login');
    }

    $usuario = $this->usuarioModel->buscarPorId($_SESSION['usuario_id']);
    $this->view('usuario/perfil', compact('usuario'));
}

public function excluirConta() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id'])) {
        $this->redirect(BASE_URL . 'usuario/login');
        return;
    }

    $usuarioId = $_SESSION['usuario_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->usuarioModel->excluir($usuarioId);

        session_destroy();
        $this->redirect(BASE_URL . 'principal');
    } else {
        $this->view('usuario/excluirConta');
    }
}

public function editarConta() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: " . BASE_URL . "usuario/login");
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->buscarPorId($_SESSION['usuario_id']);
    $erro = '';
    $sucesso = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senhaAtual = $_POST['senha_atual'];

        if (empty($nome) || empty($email) || empty($senhaAtual)) {
            $erro = "Preencha todos os campos.";
        } elseif (!$usuarioModel->verificarSenha($_SESSION['usuario_id'], $senhaAtual)) {
            $erro = "Senha atual incorreta.";
        } else {
            if ($usuarioModel->atualizarConta($_SESSION['usuario_id'], $nome, $email)) {
                $_SESSION['usuario_nome'] = $nome;
                $this->redirect(BASE_URL . 'usuario/perfil');
                exit; 
            } else {
                $erro = "Erro ao atualizar os dados. Tente novamente.";
            }
        }
    }

    $this->view('usuario/editarConta', [
        'usuario' => $usuario,
        'erro' => $erro,
        'sucesso' => $sucesso
    ]);
}



}
