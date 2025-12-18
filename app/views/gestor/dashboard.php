<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard do Gestor</title>
</head>
<body>
    <h1>Dashboard do Gestor</h1>
    
    <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?>!</p>
    <p><a href="<?= BASE_URL ?>principal">Voltar para página principal</a></p>

    <?php if (!empty($erro)) echo "<p style='color:red;'>$erro</p>"; ?>

    <h2>Criar Novo Evento</h2>
    <form method="POST" action="<?= BASE_URL ?>evento/criar" id="form-evento">
        <label>Título:</label><br>
        <input type="text" name="titulo" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required><br>

        <label>Organizador:</label>
        <input type="text" name="organizador" value="<?= htmlspecialchars($_POST['organizador'] ?? '') ?>" required><br>

        <label>Data de Início:</label>
        <input type="date" name="data_inicio" value="<?= htmlspecialchars($_POST['data_inicio'] ?? '') ?>" required>

        <label>Horário de Início:</label>
        <input type="time" name="hora_inicio" value="<?= htmlspecialchars($_POST['hora_inicio'] ?? '') ?>" required><br>

        <label>Data de Término: </label>
        <input type="date" name="data_fim" value="<?= htmlspecialchars($_POST['data_fim'] ?? '') ?>" required>

        <label>Horário de Término:</label>
        <input type="time" name="hora_fim" value="<?= htmlspecialchars($_POST['hora_fim'] ?? '') ?>" required><br>

        <label>Abertura das inscrições:</label>
        <input type="datetime-local" name="inscricao_inicio" value="<?= htmlspecialchars($_POST['inscricao_inicio'] ?? '') ?>"> <br>

        <label>Encerramento das inscrições:</label>
        <input type="datetime-local" name="inscricao_fim" value="<?= htmlspecialchars($_POST['inscricao_fim'] ?? '') ?>"> <br>

        <label>Local:</label><br>
        <input type="text" name="local" value="<?= htmlspecialchars($_POST['local'] ?? '') ?>" required><br>

        <label>Descrição:</label><br>
        <textarea name="descricao"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea><br>

        <h3>Adicionar Modalidade</h3>
        <div id="form-modalidade">
            <label>Nome:</label><br>
            <input type="text" id="modalidade-nome"><br>

            <label>Descrição:</label><br>
            <textarea id="modalidade-descricao"></textarea><br>

            <label>Limite de Inscrições:</label><br>
            <input type="number" id="modalidade-limite" min="0"><br>

            <label>Taxa de inscrição:</label><br>
            <input type="number" id="modalidade-taxa" min="0" step="0.01"><br>

            <button type="button" onclick="adicionarModalidade()">Adicionar Modalidade</button>
        </div>

        <h3>Modalidades adicionadas</h3>
        <table border="1" id="modalidades-tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Limite</th>
                    <th>Taxa</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Modalidades adicionadas aparecerão aqui -->
            </tbody>
        </table>

        <button type="submit">Criar Evento</button>
    </form>

    <h2>Meus Eventos</h2>

<?php if (!empty($eventos)): ?>
    <?php foreach ($eventos as $evento): ?>
        <?php
            $eventoBloqueado = in_array(
    $evento['status'],
  ['Cancelado', 'Encerrado', 'Em andamento']);
        ?>

        <div class="evento-container" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
            <h2>Status atual: <?= htmlspecialchars($evento['status']) ?></h2>
            <p><strong>Organizador:</strong> <?= htmlspecialchars($evento['organizador']) ?></p>
            <p><strong>Data de Início:</strong> <?= htmlspecialchars($evento['data_inicio']) ?> <?= htmlspecialchars($evento['hora_inicio']) ?></p>
            <p><strong>Data de Fim:</strong> <?= htmlspecialchars($evento['data_fim']) ?> <?= htmlspecialchars($evento['hora_fim']) ?></p>
            <p><strong>Abertura das Inscrições:</strong> <?= htmlspecialchars($evento['inscricao_inicio']) ?: 'Não definido' ?></p>
            <p><strong>Encerramento das Inscrições:</strong> <?= htmlspecialchars($evento['inscricao_fim']) ?: 'Não definido' ?></p>
            <p><strong>Local:</strong> <?= htmlspecialchars($evento['local']) ?></p>
            <p><strong>Descrição:</strong> <?= htmlspecialchars($evento['descricao']) ?></p>

            <?php if (!$eventoBloqueado): ?>
                <a href="<?= BASE_URL ?>evento/editarForm/<?= $evento['id'] ?>">
                    <button type="button">Editar</button>
                </a>

                <a href="<?= BASE_URL ?>gestor/cancelarEvento/<?= $evento['id'] ?>"
                    onclick="return confirm('Tem certeza que deseja cancelar este evento?');">
                    <button type="button">Cancelar Evento</button>
                </a>
            <?php endif; ?>
            <?php if ($evento['status'] === 'Cancelado'): ?>
                <a href="<?= BASE_URL ?>gestor/excluirEvento/<?= $evento['id'] ?>"
                onclick="return confirm('ATENÇÃO: esta ação é definitiva. Deseja excluir este evento?');">
                    <button type="button" style="color:red;">
                        Excluir Evento
                    </button>
                </a>
            <?php endif; ?>


            <div id="modalidades-<?= $evento['id'] ?>" style="display:block; margin-top:10px;">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Inscrições atuais</th>
                            <th>Limite de inscrições</th>
                            <th>Taxa de inscrição</th>
                            <?php if (!$eventoBloqueado): ?>
                                <th>Editar</th>
                                <th>Excluir</th>
                            <?php endif; ?> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($evento['modalidades'])): ?>
                            <?php foreach ($evento['modalidades'] as $mod): ?>
                                <tr>
                                    <td><?= htmlspecialchars($mod['nome']) ?></td>
                                    <td><?= htmlspecialchars($mod['descricao']) ?></td>
                                    <td>0</td>
                                    <td><?= htmlspecialchars($mod['limite_inscricoes']) ?></td>
                                    <td><?= htmlspecialchars($mod['taxa_inscricao']) ?></td>
                                    <?php if (!$eventoBloqueado): ?>
                                        <td>
                                            <a href="<?= BASE_URL ?>modalidade/editar/<?= $mod['id'] ?>">Editar</a>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>modalidade/excluir/<?= $mod['id'] ?>"
                                            onclick="return confirm('Deseja realmente excluir esta modalidade?');">
                                            Excluir
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">Nenhuma modalidade cadastrada</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Você ainda não criou nenhum evento.</p>
<?php endif; ?>

    <script>
        let modalidades = [];

        function adicionarModalidade() {
            const nome = document.getElementById('modalidade-nome').value.trim();
            const descricao = document.getElementById('modalidade-descricao').value.trim();
            const limite = document.getElementById('modalidade-limite').value;
            const taxa = document.getElementById('modalidade-taxa').value;

            if (!nome) {
                alert('Preencha o nome da modalidade.');
                return;
            }

            // Adiciona na lista de modalidades
            const modalidade = { nome, descricao, limite, taxa };
            modalidades.push(modalidade);
            atualizarTabela();

            // Limpa os campos do form de modalidade
            document.getElementById('modalidade-nome').value = '';
            document.getElementById('modalidade-descricao').value = '';
            document.getElementById('modalidade-limite').value = '';
            document.getElementById('modalidade-taxa').value = '';
        }

        function atualizarTabela() {
            const tbody = document.getElementById('modalidades-tabela').querySelector('tbody');
            tbody.innerHTML = '';

            modalidades.forEach((m, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${m.nome}</td>
                    <td>${m.descricao}</td>
                    <td>${m.limite}</td>
                    <td>${m.taxa}</td>
                    <td>
                        <button type="button" onclick="removerModalidade(${index})">Excluir</button>
                        <input type="hidden" name="modalidades[${index}][nome]" value="${m.nome}">
                        <input type="hidden" name="modalidades[${index}][descricao]" value="${m.descricao}">
                        <input type="hidden" name="modalidades[${index}][limite]" value="${m.limite}">
                        <input type="hidden" name="modalidades[${index}][taxa]" value="${m.taxa}">
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function removerModalidade(index) {
            modalidades.splice(index, 1);
            atualizarTabela();
        }

        function toggleModalidades(eventoId) {
            const div = document.getElementById('modalidades-' + eventoId);
            if (div.style.display === 'none' || div.style.display === '') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }
    </script>
</body>
</html>
