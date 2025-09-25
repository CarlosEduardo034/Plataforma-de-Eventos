<?php
class Usuario {
    private $db;

    public function __construct() {
        // Usa a conexão PDO do Database
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Cria um usuário no banco
     * @param string $nome
     * @param string $email
     * @param string $senha
     * @param string $role (comum, gestor ou admin)
     * @return bool
     */
    public function criar($nome, $email, $senha, $role = 'comum') {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nome, email, senha, role) VALUES (?, ?, ?, ?)"
        );

        return $stmt->execute([$nome, $email, $hash, $role]);
    }

    /**
     * Busca um usuário pelo email
     * @param string $email
     * @return array|null
     */
    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário pelo ID
     * @param int $id
     * @return array|null
     */
     public function buscarPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica se uma combinação email + senha está correta
     * @param string $email
     * @param string $senha
     * @return array|false
     */
    public function verificarLogin($email, $senha) {
        $usuario = $this->buscarPorEmail($email);
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }
    public function verificarChave($chave) {
        $stmt = $this->db->prepare("SELECT * FROM chaves_gestor WHERE chave = ? AND usada = 0");
        $stmt->execute([$chave]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    public function emailExiste($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0; // Retorna true se já existe
    }

    /**
     * Gera uma chave temporária (para criação de gestores)
     * @return string
     */
    public function gerarChaveGestor() {
        return bin2hex(random_bytes(4)); // 8 caracteres hexadecimais
    }

    public function excluir($id) {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

public function atualizarConta($id, $nome, $email) {
    $stmt = $this->db->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
    return $stmt->execute([$nome, $email, $id]);
}

public function verificarSenha($id, $senha) {
    $stmt = $this->db->prepare("SELECT senha FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $hash = $stmt->fetchColumn();

    if ($hash && password_verify($senha, $hash)) {
        return true;
    }
    return false;
}



}
