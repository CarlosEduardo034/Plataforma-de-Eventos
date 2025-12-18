<?php
class ModalidadeController extends Controller {

    private $modalidadeModel;

    public function __construct() {
        $this->modalidadeModel = new Modalidade();
    }
public function criar($eventoId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['usuario_id'])) {
        $this->redirect(BASE_URL . 'login');
        return;
    }

    $eventoModel = new Evento();
    $evento = $eventoModel->buscarPorId($eventoId);

    if (!$evento) {
        $_SESSION['mensagem_erro'] = 'Evento não encontrado.';
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    $status = $eventoModel->getStatus($evento);

    // 🔒 REGRA DE NEGÓCIO
    if (in_array($status, ['Cancelado', 'Encerrado', 'Em andamento'])) {
        $_SESSION['mensagem_erro'] = 'Não é possível adicionar modalidades neste evento.';
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $limite = intval($_POST['limite_inscricoes'] ?? 0);
    $taxa = floatval($_POST['taxa_inscricao'] ?? 0);

    if (empty($nome)) {
        $_SESSION['mensagem_erro'] = 'O nome da modalidade é obrigatório.';
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    $ok = $this->modalidadeModel->criar(
        $eventoId,
        $nome,
        $descricao,
        $limite,
        $taxa
    );

    if ($ok) {
        $_SESSION['mensagem_sucesso'] = 'Modalidade adicionada com sucesso.';
    } else {
        $_SESSION['mensagem_erro'] = 'Erro ao adicionar modalidade.';
    }

    $this->redirect(BASE_URL . 'gestor/dashboard');
}

    public function editar($id) {
        $modalidade = $this->modalidadeModel->buscarPorId($id);

        if (!$modalidade) {
            echo "Modalidade não encontrada.";
            return;
        }

        require __DIR__ . '/../views/gestor/editar_modalidade.php';
    }
public function atualizar($id) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $limite = intval($_POST['limite_inscricoes'] ?? 0);
    $taxa = floatval($_POST['taxa_inscricao'] ?? 0);

    if (empty($nome)) {
        $_SESSION['mensagem_erro'] = "O nome da modalidade é obrigatório.";
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    $ok = $this->modalidadeModel->atualizar($id, $nome, $descricao, $limite, $taxa);

    if (!$ok) {
        $_SESSION['mensagem_erro'] = "Erro ao atualizar modalidade.";
    } else {
        $_SESSION['mensagem_sucesso'] = "Modalidade atualizada com sucesso.";
    }

    // Redireciona para o dashboard do gestor (sem chamar rota inexistente)
    $this->redirect(BASE_URL . 'gestor/dashboard');
}

// --- Exclui modalidade ---
public function excluir($id) {
    $modalidade = $this->modalidadeModel->buscarPorId($id);

    if (!$modalidade) {
        $_SESSION['mensagem_erro'] = "Modalidade inexistente";
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    $ok = $this->modalidadeModel->excluir($id);

    if ($ok) {
        $_SESSION['mensagem_sucesso'] = "Modalidade excluída.";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao excluir modalidade.";
    }

    // Redireciona para o dashboard do gestor
    $this->redirect(BASE_URL . 'gestor/dashboard');
}
}
