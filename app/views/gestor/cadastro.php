<?php
// app/views/gestor/cadastro.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Gestor</title>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Gestor</h2>

        <?php if (!empty($erro)): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nome ?? '') ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            <br>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>
            <br>
            <label for="senha_confirm">Confirmar Senha:</label>
            <input type="password" name="senha_confirm" id="senha_confirm" required>
            <br>
            <label for="chave">Chave de acesso:</label>
            <input type="text" name="chave" id="chave" value="<?= htmlspecialchars($chave ?? '') ?>" required>
            <br>
            <input type="submit" value="Cadastrar">
        </form>

        <p>Já possui cadastro? <a href="<?= BASE_URL ?>gestor/login">Faça login aqui</a></p>
        <p><a href="<?= BASE_URL ?>">Voltar para a página inicial</a></p>
    </div>
</body>
</html>
