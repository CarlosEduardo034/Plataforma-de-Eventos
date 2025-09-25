<?php
class Evento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function criar($titulo, $data_inicio, $hora_inicio, $data_fim, $hora_fim, $local, $descricao, $organizador, $criador_id, $inscricao_inicio = null, $inscricao_fim = null) {
    $sql = "INSERT INTO eventos 
            (titulo, data_inicio, hora_inicio, data_fim, hora_fim, local, descricao, organizador, criador_id, inscricao_inicio, inscricao_fim) 
            VALUES 
            (:titulo, :data_inicio, :hora_inicio, :data_fim, :hora_fim, :local, :descricao, :organizador, :criador_id, :inscricao_inicio, :inscricao_fim)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':data_inicio', $data_inicio);
    $stmt->bindParam(':hora_inicio', $hora_inicio);
    $stmt->bindParam(':data_fim', $data_fim);
    $stmt->bindParam(':hora_fim', $hora_fim);
    $stmt->bindParam(':local', $local);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':organizador', $organizador);
    $stmt->bindParam(':criador_id', $criador_id, PDO::PARAM_INT);
    $stmt->bindParam(':inscricao_inicio', $inscricao_inicio);
    $stmt->bindParam(':inscricao_fim', $inscricao_fim);

    if ($stmt->execute()) {
        return $this->db->lastInsertId();
    }

    return false;
}

    public function criarModalidades($eventoId, $modalidades) {
        $stmt = $this->db->prepare(
            "INSERT INTO modalidades (evento_id, nome, descricao, limite_inscricoes, taxa_inscricao) 
             VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($modalidades as $mod) {
            $nome = $mod['nome'] ?? '';
            $descricao = $mod['descricao'] ?? '';
            $limite = (int)($mod['limite'] ?? 0);
            $taxa = (float)($mod['taxa_inscricao'] ?? 0.00);
            if ($nome) {
                $stmt->execute([$eventoId, $nome, $descricao, $limite, $taxa]);
            }
        }
    }

    public function listarPorCriador($criadorId) {
    $stmt = $this->db->prepare("
        SELECT id, titulo, organizador, data_inicio, hora_inicio, data_fim, hora_fim,
               local, descricao, inscricao_inicio, inscricao_fim, cancelado
        FROM eventos 
        WHERE criador_id = ?
    ");
    $stmt->execute([$criadorId]);
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($eventos as &$evento) {
        // Adiciona modalidades
        $stmtMod = $this->db->prepare("SELECT * FROM modalidades WHERE evento_id = ?");
        $stmtMod->execute([$evento['id']]);
        $evento['modalidades'] = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

        // Adiciona status
        $evento['status'] = $this->getStatus($evento);
    }

    return $eventos;
}


public function buscarPorId($id) {
    $stmt = $this->db->prepare("SELECT * FROM eventos WHERE id = ?");
    $stmt->execute([$id]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($evento) {
        $evento['status'] = $this->getStatus($evento);
    }

    return $evento;
}

public function atualizar($id, $titulo, $data_inicio, $hora_inicio, $data_fim, $hora_fim, $local, $descricao, $organizador, $inscricao_inicio = null, $inscricao_fim = null) {
    $sql = "UPDATE eventos 
            SET titulo = :titulo, data_inicio = :data_inicio, hora_inicio = :hora_inicio, 
                data_fim = :data_fim, hora_fim = :hora_fim, local = :local, descricao = :descricao, 
                organizador = :organizador, inscricao_inicio = :inscricao_inicio, inscricao_fim = :inscricao_fim
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':data_inicio', $data_inicio);
    $stmt->bindParam(':hora_inicio', $hora_inicio);
    $stmt->bindParam(':data_fim', $data_fim);
    $stmt->bindParam(':hora_fim', $hora_fim);
    $stmt->bindParam(':local', $local);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':organizador', $organizador);
    $stmt->bindParam(':inscricao_inicio', $inscricao_inicio);
    $stmt->bindParam(':inscricao_fim', $inscricao_fim);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
}

public function getStatus($evento) {
    if (!empty($evento['cancelado']) && $evento['cancelado'] == 1) {
        return "Cancelado";
    }

    $agora = new DateTime();
    $inicioEvento = new DateTime($evento['data_inicio'] . ' ' . $evento['hora_inicio']);
    $fimEvento    = new DateTime($evento['data_fim'] . ' ' . $evento['hora_fim']);
    $inscInicio   = !empty($evento['inscricao_inicio']) ? new DateTime($evento['inscricao_inicio']) : null;
    $inscFim      = !empty($evento['inscricao_fim']) ? new DateTime($evento['inscricao_fim']) : null;

    if ($agora > $fimEvento) {
        return "Evento encerrado";
    }

    if ($agora >= $inicioEvento && $agora <= $fimEvento) {
        return "Evento em andamento";
    }

    if ($inscInicio && $inscFim && $agora >= $inscInicio && $agora <= $inscFim) {
        return "Inscrições abertas";
    }

    if ($inscFim && $agora > $inscFim && $agora < $inicioEvento) {
        return "Inscrições encerradas";
    }

    if ($inscInicio && $agora < $inscInicio) {
        return "Irá abrir";
    }

    return "Status indefinido";
}

// Evento.php
public function cancelar($id) {
    $sql = "UPDATE eventos SET cancelado = 1 WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

}
