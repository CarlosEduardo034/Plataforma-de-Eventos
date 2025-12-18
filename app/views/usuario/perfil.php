<h1>Perfil do Usuário</h1>

<p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>

<a href="<?= BASE_URL ?>usuario/editarConta">
    <button>Editar Conta</button>
</a>

<a href="<?= BASE_URL ?>usuario/excluirConta">
    <button style="color:red;">Excluir Conta</button>
</a>

<p><a href="<?= BASE_URL ?>principal">Voltar</a></p>
