<?php
class EventoController extends Controller {
    private $eventoModel;

    public function __construct() {
        $this->eventoModel = new Evento();
    }

    public function criar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'gestor/dashboard');
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $titulo      = trim($_POST['titulo'] ?? '');
        $organizador = trim($_POST['organizador'] ?? '');
        $data_inicio = trim($_POST['data_inicio'] ?? '');
        $hora_inicio = trim($_POST['hora_inicio'] ?? '');
        $data_fim    = trim($_POST['data_fim'] ?? '');
        $hora_fim    = trim($_POST['hora_fim'] ?? '');
        $inscricao_inicio = trim($_POST['inscricao_inicio'] ?? '');
        $inscricao_fim    = trim($_POST['inscricao_fim'] ?? '');
        $local       = trim($_POST['local'] ?? '');
        $descricao   = trim($_POST['descricao'] ?? '');
        $modalidades = $_POST['modalidades'] ?? [];

        // Validação básica
        if (empty($titulo) || empty($organizador) || empty($data_inicio) || empty($hora_inicio) ||
            empty($data_fim) || empty($hora_fim) || empty($local)) {
            $erro = 'Preencha todos os campos obrigatórios.';
            require __DIR__ . '/../views/gestor/dashboard.php';
            return;
        }

        $criador_id = $_SESSION['usuario_id'] ?? null;
        if (!$criador_id) {
            $erro = 'Erro: usuário não autenticado.';
            require __DIR__ . '/../views/gestor/dashboard.php';
            return;
        }

        // Validação de datas/hora
        try {
            $agora = new DateTime();
            $inicioEvento = new DateTime("$data_inicio $hora_inicio");
            $fimEvento    = new DateTime("$data_fim $hora_fim");

            if ($inicioEvento < $agora) {
                throw new Exception("A data e hora de início do evento não pode ser anterior à data atual.");
            }
            if ($fimEvento <= $inicioEvento) {
                throw new Exception("A data e hora de término deve ser posterior à de início.");
            }

            $inicioInscricao = !empty($inscricao_inicio) ? new DateTime($inscricao_inicio) : null;
            $fimInscricao = !empty($inscricao_fim) ? new DateTime($inscricao_fim) : null;

            if ($inicioInscricao) {
                if ($inicioInscricao < $agora) {
                    throw new Exception("A abertura das inscrições não pode ser anterior à data atual.");
                }
                if ($inicioInscricao > $inicioEvento) {
                    throw new Exception("A abertura das inscrições não pode ser posterior ao início do evento.");
                }
            }

            if ($fimInscricao) {
                if ($inicioInscricao && $fimInscricao <= $inicioInscricao) {
                    throw new Exception("O encerramento das inscrições deve ser posterior à abertura.");
                }
                if ($fimInscricao > $fimEvento) {
                    throw new Exception("O encerramento das inscrições não pode ser posterior ao término do evento.");
                }
            }
        } catch (Exception $e) {
            $erro = $e->getMessage();
            require __DIR__ . '/../views/gestor/dashboard.php';
            return;
        }

        // Cria evento
        $eventoId = $this->eventoModel->criar(
            $titulo,
            $data_inicio,
            $hora_inicio,
            $data_fim,
            $hora_fim,
            $local,
            $descricao,
            $organizador,
            $criador_id,
            $inicioInscricao ? $inicioInscricao->format('Y-m-d H:i:s') : null,
            $fimInscricao ? $fimInscricao->format('Y-m-d H:i:s') : null
        );

        if (!$eventoId) {
            $erro = "Erro ao criar evento. Tente novamente.";
            require __DIR__ . '/../views/gestor/dashboard.php';
            return;
        }

        if (!empty($modalidades)) {
            $this->eventoModel->criarModalidades($eventoId, $modalidades);
        }

        // Redireciona para dashboard
        $redirect = $_SESSION['usuario_role'] === 'admin' ? 
            BASE_URL . 'admin/dashboardGestorAdmin' : 
            BASE_URL . 'gestor/dashboard';

        $this->redirect($redirect);
    }

    public function editarForm($id) {
    $evento = $this->eventoModel->buscarPorId($id);

    if (!$evento) {
        $erro = "Evento não encontrado.";
        require __DIR__ . '/../views/gestor/dashboard.php';
        return;
    }

    require __DIR__ . '/../views/gestor/editar_evento.php';
}

public function atualizar($id) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect(BASE_URL . 'gestor/dashboard');
        return;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $titulo      = trim($_POST['titulo'] ?? '');
    $organizador = trim($_POST['organizador'] ?? '');
    $data_inicio = trim($_POST['data_inicio'] ?? '');
    $hora_inicio = trim($_POST['hora_inicio'] ?? '');
    $data_fim    = trim($_POST['data_fim'] ?? '');
    $hora_fim    = trim($_POST['hora_fim'] ?? '');
    $inscricao_inicio = trim($_POST['inscricao_inicio'] ?? '');
    $inscricao_fim    = trim($_POST['inscricao_fim'] ?? '');
    $local       = trim($_POST['local'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');

    try {
        $agora = new DateTime();
        $inicioEvento = new DateTime("$data_inicio $hora_inicio");
        $fimEvento    = new DateTime("$data_fim $hora_fim");

        if ($fimEvento <= $inicioEvento) { 
            throw new Exception("A data e hora de término deve ser posterior à de início.");
        }

        $inicioInscricao = !empty($inscricao_inicio) ? new DateTime($inscricao_inicio) : null;
        $fimInscricao = !empty($inscricao_fim) ? new DateTime($inscricao_fim) : null;

        if ($inicioInscricao) {
            if ($inicioInscricao > $inicioEvento) {
                throw new Exception("A abertura das inscrições não pode ser posterior ao início do evento.");
            }
        }

        if ($fimInscricao) {
            if ($inicioInscricao && $fimInscricao <= $inicioInscricao) {
                throw new Exception("O encerramento das inscrições deve ser posterior à abertura.");
            }
            if ($fimInscricao > $fimEvento) {
                throw new Exception("O encerramento das inscrições não pode ser posterior ao término do evento.");
            }
        }
    } catch (Exception $e) {
        $erro = $e->getMessage();
        $evento = $this->eventoModel->buscarPorId($id);
        require __DIR__ . '/../views/gestor/editar_evento.php';
        return;
    }

    $ok = $this->eventoModel->atualizar(
        $id,
        $titulo,
        $data_inicio,
        $hora_inicio,
        $data_fim,
        $hora_fim,
        $local,
        $descricao,
        $organizador,
        $inicioInscricao ? $inicioInscricao->format('Y-m-d H:i:s') : null,
        $fimInscricao ? $fimInscricao->format('Y-m-d H:i:s') : null
    );

    if (!$ok) {
        $erro = "Erro ao atualizar evento.";
        $evento = $this->eventoModel->buscarPorId($id);
        require __DIR__ . '/../views/gestor/editar_evento.php';
        return;
    }

    $this->redirect(BASE_URL . 'gestor/dashboard');
}

public function editar($id) {
    $evento = $this->eventoModel->buscarPorId($id);

    if (!$evento) {
        echo "Evento não encontrado!";
        return;
    }

    $evento['status'] = $this->eventoModel->getStatus($evento);

    require __DIR__ . '/../views/gestor/editar_evento.php';
}



}
