<?php
class Modalidade {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Cria uma modalidade vinculada a um evento
    public function criar($evento_id, $nome, $descricao, $limite_inscricoes, $taxa_inscricao) {
        $stmt = $this->db->prepare(
            "INSERT INTO modalidades (evento_id, nome, descricao, limite_inscricoes, taxa_inscricao) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$evento_id, $nome, $descricao, $limite_inscricoes, $taxa_inscricao]);
    }

    // Retorna todas as modalidades de um evento
    public function listarPorEvento($evento_id) {
        $stmt = $this->db->prepare("SELECT * FROM modalidades WHERE evento_id = ? ORDER BY id ASC");
        $stmt->execute([$evento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
