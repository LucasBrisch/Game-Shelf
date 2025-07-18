<?php
include('../database/connection.php');

$sql = "
    SELECT g.*, AVG(r.rating) AS average_rating
    FROM games g
    LEFT JOIN ratings r ON g.id = r.rated_game_id
    GROUP BY g.id
    ORDER BY average_rating DESC
";

$result = $conn->query($sql);

$requests = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = (int)$row['id'];

        $requests[$id] = [
            'id' => $id,
            'title' => $row['title'],
            'description' => $row['description'],
            'game_cover_url' => $row['game_cover_url'],
            'release_date' => $row['release_date'],
            'developer' => $row['developer'],
            'genre' => $row['genre'],
            // Pode ser NULL se não tiver avaliação
            'average_rating' => is_null($row['average_rating']) ? null : round(floatval($row['average_rating']), 2)
        ];
    }
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Shelf - Sua Estante de Jogos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/styles.css">
</head>
<body>

    <!-- Cabeçalho -->
    <header>
        <div class="container">
            <div class="header-top">
                <h1>🎮 Game Shelf</h1>
            </div>
            <div class="header-bottom">
                <nav>
                    <ul>
                        <li><a href="#">Início</a></li>
                        <li><a href="#">Meus Jogos</a></li>
                        <li><a href="#">Explorar</a></li>
                        <li><a href="#">Comunidade</a></li>
                    </ul>
                </nav>
                <div class="search-bar">
                    <span class="search-icon">&#128269;</span> <!-- Ícone de lupa -->
                    <input type="search" placeholder="Pesquisar jogos, amigos...">
                </div>
                <div class="auth-buttons">
                    <button class="login-btn">Login</button>
                    <button class="register-btn">Registrar</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main>
        <div class="container">
            <section class="hero-section">
                <h2>Organize, Avalie e Compartilhe Seus Jogos!</h2>
                <p>Game Shelf é sua estante virtual para acompanhar o progresso, registrar avaliações e compartilhar opiniões com outros jogadores.</p>
            </section>

            <!-- Seção de Jogos Em Destaque -->
            <section>
                <h3 class="section-title">Jogos em Destaque</h3>
                <div class="game-grid" id="jogos">
            </section>



<script> 
function renderTable(filtered) {
    const jogos = document.getElementById('jogos');
    jogos.innerHTML = '';

    if (filtered.length === 0) {
        jogos.innerHTML = `Nenhum jogo encontrado.`;
        return;
    }

    filtered.slice(0, 4).forEach(req => {
        const div = document.createElement('div');
        div.className = 'game-card';
        div.dataset.gameId = req.id;

        div.innerHTML = `
            <img src="${req.game_cover_url}" alt="capa do jogo ${req.title}">
            <div class="game-card-content">
                <h4>${req.title}</h4>
                <p class="genre-studio">${req.genre} • ${req.developer}</p>
                <div class="rating">
                    <p>Nota: ${req.average_rating}</p>
                </div>
                <p class="description">${req.description}</p>
                <button class="view-details-btn">Ver Detalhes</button>
            </div>
        `;

        jogos.appendChild(div);
    });
}

    const jogosData = <?php echo json_encode(array_values($requests)); ?>;
    renderTable(jogosData);

</script>           
            <!-- Seção Sobre o Game Shelf -->
            <section class="about-section">
                <h3>Sobre o Game Shelf</h3>
                <p>
                    Inspirado no MyAnimeList, o Game Shelf é a sua plataforma definitiva para gerenciar sua coleção de jogos.
                    Acompanhe o progresso de cada título, desde o status "Planejando Jogar" até "Completado".
                    Registre suas avaliações, escreva resenhas detalhadas e descubra novos jogos com base nas opiniões da comunidade.
                    Conecte-se com amigos, veja o que eles estão jogando e compartilhe suas paixões por games em um ambiente intuitivo e divertido.
                </p>
            </section>
        </div>
    </main>

    <!-- Rodapé -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Game Shelf. Todos os direitos reservados.</p>
            <div>
                <a href="#">Termos de Serviço</a>
                <a href="#">Política de Privacidade</a>
            </div>
        </div>
    </footer>

    <!-- Modal de Detalhes do Jogo -->
    <div id="gameDetailsModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close-btn" id="closeModalBtn">&times;</button>
            <div class="modal-header">
                <img id="modalGameImage" src="" alt="Capa do Jogo">
                <div>
                    <h3 id="modalGameTitle" class="modal-title"></h3>
                    <p id="modalGameGenreStudio" class="modal-genre-studio"></p>
                    <div id="modalGameRating" class="modal-rating">
                        <!-- Estrelas serão preenchidas via JS -->
                    </div>
                </div>
            </div>
            <p id="modalGameDescription" class="modal-description"></p>
            <button class="add-to-list-btn">Adicionar à minha lista</button> <!-- Novo botão adicionado aqui -->
            <!-- Mais detalhes podem ser adicionados aqui -->
        </div>
    </div>

    
    
</body>
</html>
