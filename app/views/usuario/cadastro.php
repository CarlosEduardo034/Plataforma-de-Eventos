<h1>Cadastro de Usuário</h1>

<?php if (!empty($erro)) echo "<p style='color:red;'>$erro</p>"; ?>

<form action="<?= BASE_URL ?>usuario/cadastro" method="POST">
    <label>Nome:</label>
    <input type="text" name="nome" required><br>

    <label>Email:</label>
    <input type="email" name="email" required><br>

    <label>Senha:</label>
    <input type="password" name="senha" required><br>

    <label>Confirme a senha:</label>
    <input type="password" name="senha_confirm" required><br>

    <button type="submit">Cadastrar</button>
</form>


<p><a href="<?= BASE_URL ?>principal">Voltar para página principal</a></p>
