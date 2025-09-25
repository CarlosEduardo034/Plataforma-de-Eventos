<?php
class ChaveGestor {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function gerarChave() {
        $chave = bin2hex(random_bytes(16)); // 32 caracteres aleatórios
        $stmt = $this->db->prepare("INSERT INTO chaves_gestor (chave) VALUES (?)");
        $stmt->execute([$chave]);
        return $chave;
    }

    public function validarChave($chave) {
        $stmt = $this->db->prepare("SELECT * FROM chaves_gestor WHERE chave = ? AND usada = 0");
        $stmt->execute([$chave]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function marcarComoUsada($chave) {
        $stmt = $this->db->prepare("UPDATE chaves_gestor SET usada = 1 WHERE chave = ?");
        return $stmt->execute([$chave]);
    }
}
