<?php
class GestorController extends Controller {
    private $usuarioModel;
    private $eventoModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->eventoModel  = new Evento();
    }

    public function cadastro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $senhaConfirm = $_POST['senha_confirm'] ?? '';
            $chave = trim($_POST['chave'] ?? '');

            if (!$nome || !$email || !$senha || !$senhaConfirm || !$chave) {
                $erro = 'Preencha todos os campos.';
                $this->view('gestor/cadastro', compact('erro', 'nome', 'email', 'chave'));
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = 'Email inválido.';
                $this->view('gestor/cadastro', compact('erro', 'nome', 'email', 'chave'));
                return;
            }

            if ($this->usuarioModel->buscarPorEmail($email)) {
                $erro = 'Email já cadastrado.';
                $this->view('gestor/cadastro', compact('erro', 'nome', 'email', 'chave'));
                return;
            }

            if (strlen($senha) < 6 || !preg_match('/[a-zA-Z]/', $senha) || !preg_match('/\d/', $senha)) {
                $erro = 'Senha deve ter pelo menos 6 caracteres, incluindo letras e números.';
                $this->view('gestor/cadastro', compact('erro', 'nome', 'email', 'chave'));
                return;
            }

            if ($senha !== $senhaConfirm) {
                $erro = 'Senhas não conferem.';
                $this->view('gestor/cadastro', compact('erro', 'nome', 'email', 'chave'));
                return;
            }

            $chaveModel = new ChaveGestor();
            if (!$chaveModel->validarChave($chave)) {
                $erro = 'Chave de acesso inválida.';
                $this->view('gestor/cadastro', compact('erro', 'nome', 'email', 'chave'));
                return;
            }

            $this->usuarioModel->criar($nome, $email, $senha, 'gestor');
            $chaveModel->marcarComoUsada($chave);

            $usuarioCriado = $this->usuarioModel->buscarPorEmail($email);
            session_start();
            $_SESSION['usuario_id'] = $usuarioCriado['id'];
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_role'] = 'gestor';

            $this->redirect(BASE_URL . 'principal');
        } else {
            $this->view('gestor/cadastro');
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            $usuario = $this->usuarioModel->buscarPorEmail($email);

            if ($usuario && $usuario['role'] === 'gestor' && password_verify($senha, $usuario['senha'])) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_role'] = $usuario['role'];

                $this->redirect(BASE_URL . 'principal');
            } else {
                $erro = 'Credenciais inválidas';
                $this->view('gestor/login', compact('erro', 'email'));
            }
        } else {
            $this->view('gestor/login');
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        $this->redirect(BASE_URL . 'gestor/login');
    }

public function dashboard() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['usuario_role']) || $_SESSION['usuario_role'] !== 'gestor') {
        $this->redirect(BASE_URL . 'gestor/login');
        return;
    }

    $eventos = $this->eventoModel->listarPorCriador($_SESSION['usuario_id'] ?? 0);

    $modalidadeModel = new Modalidade(); // <- Adicione isso

    foreach ($eventos as &$evento) {

        // Status
        $evento['status'] = $this->eventoModel->getStatus($evento);

        // 🔥 Buscar modalidades desse evento
        $evento['modalidades'] = $modalidadeModel->listarPorEvento($evento['id']);
    }

    $usuarioNome = $_SESSION['usuario_nome'] ?? '';

    $this->view('gestor/dashboard', compact('eventos', 'usuarioNome'));
}


public function cancelarEvento($id) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['usuario_id'])) {
        $this->redirect(BASE_URL . 'login');
        return;
    }

    $evento = $this->eventoModel->buscarPorId($id);

    if (!$evento) {
        $_SESSION['mensagem_erro'] = 'Evento não encontrado.';
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    // Verifica status
    $status = $this->eventoModel->getStatus($evento);

    if (in_array($status, ['Cancelado', 'Encerrado', 'Em andamento'])) {
        $_SESSION['mensagem_erro'] = 'Este evento não pode mais ser cancelado.';
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    // Cancela evento
    $ok = $this->eventoModel->cancelar($id);

    if ($ok) {
        $_SESSION['mensagem_sucesso'] = 'Evento cancelado com sucesso.';
    } else {
        $_SESSION['mensagem_erro'] = 'Não foi possível cancelar o evento.';
    }

    $this->redirect(BASE_URL . 'gestor/dashboard');
}

public function excluirEvento($id) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['usuario_id'])) {
        $this->redirect(BASE_URL . 'login');
        return;
    }

    $evento = $this->eventoModel->buscarPorId($id);

    if (!$evento) {
        $_SESSION['mensagem_erro'] = 'Evento não encontrado.';
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    $status = $this->eventoModel->getStatus($evento);

    // 🔒 REGRA DE NEGÓCIO
    if ($status !== 'Cancelado') {
        $_SESSION['mensagem_erro'] = 'Apenas eventos cancelados podem ser excluídos.';
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    // Exclui o evento
    $ok = $this->eventoModel->excluir($id);

    if ($ok) {
        $_SESSION['mensagem_sucesso'] = 'Evento excluído definitivamente.';
    } else {
        $_SESSION['mensagem_erro'] = 'Erro ao excluir evento.';
    }

    $this->redirect(BASE_URL . 'gestor/dashboard');
}


public function perfil() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_role'] !== 'gestor') {
        $this->redirect(BASE_URL . 'gestor/login');
    }

    $usuario = $this->usuarioModel->buscarPorId($_SESSION['usuario_id']);
    $this->view('gestor/perfil', compact('usuario'));
}

public function excluirConta() {
        if (session_status() === PHP_SESSION_NONE) session_start();
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
            header("Location: " . BASE_URL . "gestor/login");
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
                    $this->redirect(BASE_URL . 'gestor/perfil');
                    exit;
                } else {
                    $erro = "Erro ao atualizar os dados. Tente novamente.";
                }
            }
        }

        $this->view('gestor/editarConta', [
            'usuario' => $usuario,
            'erro' => $erro,
            'sucesso' => $sucesso
        ]);
}
}
