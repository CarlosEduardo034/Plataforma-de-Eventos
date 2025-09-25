<h1>Login Usuário</h1>

<?php if (!empty($erro)) echo "<p style='color:red;'>$erro</p>"; ?>

<form action="" method="POST">
    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br>

    <label>Senha:</label>
    <input type="password" name="senha" required><br>

    <button type="submit">Entrar</button>
</form>

<p><a href="<?= BASE_URL ?>principal">Voltar para página principal</a></p>
