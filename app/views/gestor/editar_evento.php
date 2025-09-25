<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Evento</title>
</head>
<body>
    <h1>Editar Evento</h1>

    <p><a href="<?= BASE_URL ?>gestor/dashboard">Voltar ao Dashboard</a></p>

    <?php if (!empty($erro)): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>evento/atualizar/<?= $evento['id'] ?>">
        <label>Título:</label><br>
        <input type="text" name="titulo" 
               value="<?= htmlspecialchars($evento['titulo']) ?>" required><br>

        <label>Organizador:</label><br>
        <input type="text" name="organizador" 
               value="<?= htmlspecialchars($evento['organizador']) ?>" required><br>

        <label>Data de Início:</label>
        <input type="date" name="data_inicio" 
               value="<?= htmlspecialchars($evento['data_inicio']) ?>" required>

        <label>Horário de Início:</label>
        <input type="time" name="hora_inicio" 
               value="<?= htmlspecialchars($evento['hora_inicio']) ?>" required><br>

        <label>Data de Término:</label>
        <input type="date" name="data_fim" 
               value="<?= htmlspecialchars($evento['data_fim']) ?>" required>

        <label>Horário de Término:</label>
        <input type="time" name="hora_fim" 
               value="<?= htmlspecialchars($evento['hora_fim']) ?>" required><br>

        <label>Abertura das inscrições:</label>
        <input type="datetime-local" name="inscricao_inicio" 
               value="<?= $evento['inscricao_inicio'] ? date('Y-m-d\TH:i', strtotime($evento['inscricao_inicio'])) : '' ?>"><br>

        <label>Encerramento das inscrições:</label>
        <input type="datetime-local" name="inscricao_fim" 
               value="<?= $evento['inscricao_fim'] ? date('Y-m-d\TH:i', strtotime($evento['inscricao_fim'])) : '' ?>"><br>

        <label>Local:</label><br>
        <input type="text" name="local" 
               value="<?= htmlspecialchars($evento['local']) ?>" required><br>

        <label>Descrição:</label><br>
        <textarea name="descricao"><?= htmlspecialchars($evento['descricao']) ?></textarea><br>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>
