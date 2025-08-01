<?php
session_start();
include('../database/connection.php');
include_once('../PhP/session.php');

// Verifica se está logado
requireLogin();

// Verifica se foi passado um username na URL
$target_username = isset($_GET['username']) ? $_GET['username'] : null;

if ($target_username) {
    // Busca os dados do usuário pelo username
    $user_sql = "SELECT id, username FROM users WHERE username = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param('s', $target_username);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    
    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $user_id = $user_data['id'];
        $username = $user_data['username'];
    } else {
        // Usuário não encontrado, redireciona ou mostra erro
        header('Location: index.php');
        exit();
    }
} else {
    // Se não foi passado username, usa o usuário logado
    $user_data = getUserData();
    $user_id = $user_data['id'];
    $username = $user_data['username'];
}

$sql = "
    SELECT games.id, games.title, games.genre, games.release_date, games.developer, games.game_cover_url, user_games.status, user_games.personal_rating, user_games.added_at
    FROM games
    INNER JOIN user_games ON games.id = user_games.game_id
    WHERE user_games.user_id = ?
    ORDER BY user_games.added_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$games = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $games[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Shelf - Lista de <?php echo htmlspecialchars($username); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/styles.css">
    <link rel="stylesheet" href="../Css/gamelist.css">
</head>
<body>

<?php
    include('../PhP/header.php');
    renderHeader('my-games');
?>

<main>
    <div class="container">
        <div class="gamelist-header">
            <h1>Lista de <?php echo htmlspecialchars($username); ?></h1>
            <p class="gamelist-subtitle"><?php echo count($games); ?> jogo(s) na sua lista</p>
        </div>

        <?php if (empty($games)): ?>
            <div class="empty-state">
                <div class="empty-icon">🎮</div>
                <h2>Sua lista está vazia</h2>
                <p>Que tal começar adicionando alguns jogos à sua coleção?</p>
                <a href="games.php" class="add-games-btn">Explorar Jogos</a>
            </div>
        <?php else: ?>
            <div class="gamelist-filters">
                <button class="filter-btn active" data-filter="all">Todos (<?php echo count($games); ?>)</button>
                <button class="filter-btn" data-filter="Playing">Jogando (<?php echo count(array_filter($games, function($g) { return $g['status'] == 'Playing'; })); ?>)</button>
                <button class="filter-btn" data-filter="Completed">Completos (<?php echo count(array_filter($games, function($g) { return $g['status'] == 'Completed'; })); ?>)</button>
                <button class="filter-btn" data-filter="Wishlist">Lista de Desejos (<?php echo count(array_filter($games, function($g) { return $g['status'] == 'Wishlist'; })); ?>)</button>
                <button class="filter-btn" data-filter="Dropped">Abandonados (<?php echo count(array_filter($games, function($g) { return $g['status'] == 'Dropped'; })); ?>)</button>
            </div>

            <div class="gamelist-table">
                <div class="table-header">
                    <div class="header-cell cover-header">Capa</div>
                    <div class="header-cell title-header">Título</div>
                    <div class="header-cell genre-header">Gênero</div>
                    <div class="header-cell developer-header">Desenvolvedor</div>
                    <div class="header-cell status-header">Status</div>
                    <div class="header-cell rating-header">Nota</div>
                    <div class="header-cell date-header">Adicionado</div>

                    <?php if($_SESSION['username'] == $username) { ?>
                    <div class="header-cell actions-header">Ações</div>
                    <?php } ?>
                </div>

                <?php foreach ($games as $game): ?>
                    <div class="table-row" data-status="<?php echo $game['status']; ?>">
                        <div class="table-cell cover-cell">
                            <img src="<?php echo htmlspecialchars($game['game_cover_url']); ?>" 
                                 alt="Capa de <?php echo htmlspecialchars($game['title']); ?>" 
                                 class="game-cover">
                        </div>
                        <div class="table-cell title-cell">
                            <a href="Gamepage.php?id=<?php echo $game['id']; ?>" class="game-title-link">
                                <?php echo htmlspecialchars($game['title']); ?>
                            </a>
                            <span class="release-date"><?php echo date('Y', strtotime($game['release_date'])); ?></span>
                        </div>
                        <div class="table-cell genre-cell">
                            <?php echo htmlspecialchars($game['genre']); ?>
                        </div>
                        <div class="table-cell developer-cell">
                            <?php echo htmlspecialchars($game['developer']); ?>
                        </div>
                        <div class="table-cell status-cell">
                            <span class="status-badge status-<?php echo strtolower($game['status']); ?>">
                                <?php 
                                    $status_map = [
                                        'Playing' => 'Jogando',
                                        'Completed' => 'Completo',
                                        'Wishlist' => 'Lista de Desejos',
                                        'Dropped' => 'Abandonado'
                                    ];
                                    echo $status_map[$game['status']] ?? $game['status']; 
                                ?>
                            </span>
                        </div>
                        <div class="table-cell rating-cell">
                            <?php if ($game['personal_rating']): ?>
                                <div class="rating-display">
                                    <span class="rating-stars">★</span>
                                    <span class="rating-value"><?php echo $game['personal_rating']; ?>/10</span>
                                </div>
                            <?php else: ?>
                                <span class="no-rating">-</span>
                            <?php endif; ?>
                        </div>
                        <div class="table-cell date-cell">
                            <?php echo date('d/m/Y', strtotime($game['added_at'])); ?>
                        </div>

                            <?php if($_SESSION['username'] == $username) { ?>

                        <div class="table-cell actions-cell">
                            <button class="action-btn edit-btn" onclick="editGame(<?php echo $game['id']; ?>)">
                                ✏️
                            </button>
                            <button class="action-btn remove-btn" onclick="removeGame(<?php echo $game['id']; ?>)">
                                🗑️
                            </button>
                        </div>
                        <?php } ?>
                                

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Game Shelf. Todos os direitos reservados.</p>
    </div>
</footer>

<script>
    

    // Filtros
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const tableRows = document.querySelectorAll('.table-row');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Atualiza botões ativos
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Filtra jogos
                tableRows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    if (filter === 'all' || status === filter) {
                        row.style.display = 'grid';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    });

    function editGame(gameId) {
        // Implementar edição de jogo
        alert('Funcionalidade de edição em desenvolvimento');
    }

    function removeGame(gameId) {
        if (confirm('Tem certeza que deseja remover este jogo da sua lista?')) {
            // Implementar remoção
            alert('Funcionalidade de remoção em desenvolvimento');
        }
    }
</script>

</body>
</html>