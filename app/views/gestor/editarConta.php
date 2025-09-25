<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Conta</title>
</head>
<body>
    <h1>Editar Conta</h1>

    <?php if (!empty($erro)): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <?php if (!empty($sucesso)): ?>
        <p style="color:green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>gestor/editarConta">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required><br><br>

        <label>Senha atual (obrigatória para confirmar alteração):</label><br>
        <input type="password" name="senha_atual" required><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>

    <p><a href="<?= BASE_URL ?>gestor/perfil">Voltar ao Perfil</a></p>
</body>
</html>
