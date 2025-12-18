<?php
class Modalidade {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function criar($eventoId, $nome, $descricao, $limite, $taxa) {
    $sql = "INSERT INTO modalidades 
            (evento_id, nome, descricao, limite_inscricoes, taxa_inscricao)
            VALUES (:evento_id, :nome, :descricao, :limite, :taxa)";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        'evento_id' => $eventoId,
        'nome' => $nome,
        'descricao' => $descricao,
        'limite' => $limite,
        'taxa' => $taxa
    ]);
}

    public function listarPorEvento($evento_id) {
        $stmt = $this->db->prepare("SELECT * FROM modalidades WHERE evento_id = ? ORDER BY id ASC");
        $stmt->execute([$evento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM modalidades WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id, $nome, $descricao, $limite, $taxa) {
        $stmt = $this->db->prepare(
            "UPDATE modalidades SET nome=?, descricao=?, limite_inscricoes=?, taxa_inscricao=? WHERE id=?"
        );
        return $stmt->execute([$nome, $descricao, $limite, $taxa, $id]);
    }

    public function excluir($id) {
        $stmt = $this->db->prepare("DELETE FROM modalidades WHERE id=?");
        return $stmt->execute([$id]);
    }
}
