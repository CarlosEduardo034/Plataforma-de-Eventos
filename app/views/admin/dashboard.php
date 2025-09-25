<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>
    <h2>Bem-vindo, <?= htmlspecialchars($nome) ?>!</h2>

    <form method="POST" action="">
        <button type="submit" name="gerar_chave">Gerar chave para Gestor</button>
    </form>

    <?php if (!empty($novaChave)) : ?>
        <p>✅ Nova chave criada: <b><?= htmlspecialchars($novaChave) ?></b></p>
    <?php endif; ?>

    <p><a href="<?= BASE_URL ?>logout">Logout</a></p>

</body>
</html>
