<h2>Editar Modalidade</h2>

<form method="POST" action="<?= BASE_URL ?>modalidade/atualizar/<?= $modalidade['id'] ?>">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($modalidade['nome']) ?>" required>

    <label>Descrição:</label>
    <textarea name="descricao"><?= htmlspecialchars($modalidade['descricao']) ?></textarea>

    <label>Limite de inscrições:</label>
    <input type="number" name="limite_inscricoes" value="<?= $modalidade['limite_inscricoes'] ?>">

    <label>Taxa de inscrição:</label>
    <input type="number" step="0.01" name="taxa_inscricao" value="<?= $modalidade['taxa_inscricao'] ?>">

    <button type="submit">Salvar alterações</button>
</form>

<a href="<?= BASE_URL ?>gestor/evento/<?= $modalidade['evento_id'] ?>">Voltar</a>
