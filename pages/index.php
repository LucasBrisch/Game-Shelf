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
    <link rel="stylesheet" href="../Css/addtolist-modal.css">
</head>
<body>

    <!-- Cabeçalho -->
    <?php include('../PhP/header.php');
    renderHeader();
    ?>
    



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

    // MODAL: Função para abrir e preencher o modal
    function openGameModal(gameId) {
        const modal = document.getElementById('gameDetailsModal');
        // Limpa dados antigos
        document.getElementById('modalGameImage').src = '';
        document.getElementById('modalGameTitle').textContent = '';
        document.getElementById('modalGameGenreStudio').textContent = '';
        document.getElementById('modalGameRating').innerHTML = '';
        document.getElementById('modalGameDescription').textContent = '';

        fetch(`../apis/get_game_details.php?id=${gameId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('modalGameTitle').textContent = data.error;
                    return;
                }
                
                // Armazena dados globalmente para o botão de adicionar
                currentModalGameId = gameId;
                currentModalGameTitle = data.title;
                currentModalGameImage = data.game_cover_url;
                
                document.getElementById('modalGameImage').src = data.game_cover_url;
                document.getElementById('modalGameTitle').textContent = data.title;
                document.getElementById('modalGameGenreStudio').textContent = `${data.genre} • ${data.developer}`;
                document.getElementById('modalGameDescription').textContent = data.description;
                let ratingHtml = '';
                ratingHtml = `<span style='font-size:2.2em;font-weight:bold;color:#2e7d32;'>${data.average_rating}</span><span style='font-size:1em;color:#888;'> / 10</span>`;
                
                document.getElementById('modalGameRating').innerHTML = ratingHtml;
                modal.style.display = 'flex';
            })
            .catch(() => {
                document.getElementById('modalGameTitle').textContent = 'Erro ao carregar detalhes.';
            });
    }

    // LOGICA DO BOTAO DE DETALHES
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-details-btn')) {
            const card = e.target.closest('.game-card');
            if (card) {
                openGameModal(card.dataset.gameId);
            }
        }
        if (e.target.id === 'closeModalBtn') {
            document.getElementById('gameDetailsModal').style.display = 'none';
            // Limpa variáveis globais
            currentModalGameId = null;
            currentModalGameTitle = '';
            currentModalGameImage = '';
        }
        // Fecha modal ao clicar fora do conteúdo
        if (e.target.id === 'gameDetailsModal') {
            document.getElementById('gameDetailsModal').style.display = 'none';
            // Limpa variáveis globais
            currentModalGameId = null;
            currentModalGameTitle = '';
            currentModalGameImage = '';
        }
    });
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
                        
                    </div>
                </div>
            </div>
            <p id="modalGameDescription" class="modal-description"></p>
            <button class="add-to-list-btn" id="modalAddToListBtn" onclick="addFromModal()">Adicionar à minha lista</button>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Indica se o usuário está logado (para o JavaScript)
        const userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        
        // Variáveis globais para armazenar dados do jogo atual no modal
        let currentModalGameId = null;
        let currentModalGameTitle = '';
        let currentModalGameImage = '';
        
        // Função para adicionar jogo à lista a partir do modal
        function addFromModal() {
            if (currentModalGameId) {
                checkLoginAndAddToList(currentModalGameId, currentModalGameTitle, currentModalGameImage);
            }
        }
    </script>
    <script src="../js/addtolist.js"></script>
    
</body>
</html>
