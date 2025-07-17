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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
            const gameDetailsModal = document.getElementById('gameDetailsModal');
            const closeModalBtn = document.getElementById('closeModalBtn');

            const modalGameImage = document.getElementById('modalGameImage');
            const modalGameTitle = document.getElementById('modalGameTitle');
            const modalGameGenreStudio = document.getElementById('modalGameGenreStudio');
            const modalGameRating = document.getElementById('modalGameRating');
            const modalGameDescription = document.getElementById('modalGameDescription');

            // Dados de exemplo para os jogos (em um cenário real, viriam de um banco de dados)
            const gamesData = {
                1: {
                    title: 'The Witcher 3: Wild Hunt',
                    genreStudio: 'RPG de Ação • CD Projekt Red',
                    rating: 4.5,
                    description: 'Um épico RPG de mundo aberto com uma narrativa rica, escolhas morais impactantes e um vasto mundo a ser explorado. Siga Geralt de Rívia em sua busca por Ciri, enquanto enfrenta monstros e intrigas políticas.',
                    image: 'https://placehold.co/400x250/374151/ffffff?text=Jogo+1'
                },
                2: {
                    title: 'Cyberpunk 2077',
                    genreStudio: 'RPG de Ação • CD Projekt Red',
                    rating: 4.0,
                    description: 'Mergulhe em Night City, uma megacidade futurista obcecada por poder, glamour e modificações corporais. Jogue como V, um mercenário fora da lei em busca de um implante único que é a chave para a imortalidade.',
                    image: 'https://placehold.co/400x250/374151/ffffff?text=Jogo+2'
                },
                3: {
                    title: 'Stardew Valley',
                    genreStudio: 'Simulação • ConcernedApe',
                    rating: 5.0,
                    description: 'Comece uma nova vida no campo, restaurando uma fazenda abandonada e se tornando parte da comunidade local. Cultive, crie animais, pesque, minere e faça amizade com os habitantes da cidade.',
                    image: 'https://placehold.co/400x250/374151/ffffff?text=Jogo+3'
                },
                4: {
                    title: 'Elden Ring',
                    genreStudio: 'RPG de Ação • FromSoftware',
                    rating: 5.0,
                    description: 'Um vasto mundo de fantasia sombria, repleto de perigos e descobertas, aguarda os Sem-Luz. Crie seu próprio personagem e explore as Terras Intermédias, enfrentando desafios brutais e descobrindo segredos antigos.',
                    image: 'https://placehold.co/400x250/374151/ffffff?text=Jogo+4'
                }
            };

            // Função para preencher e exibir o modal
            const openModal = (gameId) => {
                const game = gamesData[gameId];
                if (game) {
                    modalGameImage.src = game.image;
                    modalGameTitle.textContent = game.title;
                    modalGameGenreStudio.textContent = game.genreStudio;
                    modalGameDescription.textContent = game.description;

                    // Preencher estrelas
                    modalGameRating.innerHTML = ''; // Limpa estrelas anteriores
                    const fullStars = Math.floor(game.rating);
                    const hasHalfStar = game.rating % 1 !== 0;

                    for (let i = 0; i < fullStars; i++) {
                        const star = document.createElement('span');
                        star.classList.add('star');
                        star.textContent = '⭐';
                        modalGameRating.appendChild(star);
                    }
                    if (hasHalfStar) {
                        const halfStar = document.createElement('span');
                        halfStar.classList.add('star');
                        halfStar.textContent = '½'; // Ou outro caractere para meia estrela
                        modalGameRating.appendChild(halfStar);
                    }
                    const scoreText = document.createElement('span');
                    scoreText.classList.add('score');
                    scoreText.textContent = `(${game.rating}/5)`;
                    modalGameRating.appendChild(scoreText);

                    gameDetailsModal.style.display = 'flex'; // Exibe o modal
                }
            };

            // Adiciona event listener para cada botão "Ver Detalhes"
            viewDetailsButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    const gameCard = event.target.closest('.game-card');
                    const gameId = gameCard.dataset.gameId;
                    openModal(gameId);
                });
            });

            // Adiciona event listener para o botão de fechar o modal
            closeModalBtn.addEventListener('click', () => {
                gameDetailsModal.style.display = 'none'; // Esconde o modal
            });

            // Fecha o modal ao clicar fora dele
            gameDetailsModal.addEventListener('click', (event) => {
                if (event.target === gameDetailsModal) {
                    gameDetailsModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
