<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página Principal</title>
</head>
<body>
    <?php if ($usuarioNome): ?>
        <h1>Bem-vindo, <?= htmlspecialchars($usuarioNome) ?>!</h1>
        <p>Você está logado como <?= htmlspecialchars($usuarioRole) ?>.</p>

        <?php if ($usuarioRole === 'gestor'): ?>
            <p>Aqui você pode gerenciar seus eventos.</p>
            <a href="<?= BASE_URL ?>gestor/dashboard">
                <button>Ir para o Dashboard</button>
            </a>
            <p><a href="<?= BASE_URL ?>gestor/perfil">Perfil</a></p>
        <?php endif; ?>

        <?php if ($usuarioRole === 'comum'): ?>
            <p><a href="<?= BASE_URL ?>usuario/perfil">Perfil</a></p>
        <?php endif; ?>

        <?php if ($usuarioRole === 'comum' || $usuarioRole === 'gestor'): ?>
            <p><a href="<?= BASE_URL ?>logout">Logout</a></p>
        <?php endif; ?>


    <?php else: ?>
        <h1>Bem-vindo à plataforma!</h1>
        <p>Escolha uma opção no menu ou faça login:</p>
        <ul>
            <li><a href="<?= BASE_URL ?>admin/login">Login Admin</a></li>
            <li><a href="<?= BASE_URL ?>gestor/login">Login Gestor</a></li>
            <li><a href="<?= BASE_URL ?>gestor/cadastro">Cadastro Gestor</a></li>
            <li><a href="<?= BASE_URL ?>usuario/login">Login Usuário</a></li>
            <li><a href="<?= BASE_URL ?>usuario/cadastro">Cadastro Usuário</a></li>
        </ul>
    <?php endif; ?>
</body>
</html>
