<?php
class ModalidadeController extends Controller {

    private $modalidadeModel;

    public function __construct() {
        $this->modalidadeModel = new Modalidade();
    }

    // --- Exibe formulário de edição ---
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
