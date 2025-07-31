<?php
include('../database/connection.php');

// Verifica se o ID do jogo foi fornecido na URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Se não houver ID, redireciona para a página inicial ou mostra um erro
    header('Location: index.php');
    exit;
}

$game_id = intval($_GET['id']);

// Busca os detalhes do jogo, a nota média e a contagem de 'completado'
$sql = "
    SELECT 
        g.*, 
        AVG(r.rating) AS average_rating,
        (SELECT COUNT(*) FROM user_games ug WHERE ug.game_id = g.id AND ug.status = 'Playing') AS completed_count
    FROM 
        games g
    LEFT JOIN 
        ratings r ON g.id = r.rated_game_id
    WHERE 
        g.id = ?
    GROUP BY 
        g.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $game_id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();

// Se o jogo não for encontrado, redireciona
if (!$game) {
    header('Location: index.php');
    exit;
}

// Formata a nota média
$average_rating = $game['average_rating'] ? round($game['average_rating'], 1) : 'N/A';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Shelf - <?php echo htmlspecialchars($game['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/styles.css">
    <link rel="stylesheet" href="../Css/game-page.css">
</head>
<body>

    <!-- Cabeçalho -->
    <header>
        <div class="container">
            <div class="header-top">
                <h1><a href="index.php" style="text-decoration: none; color: inherit;">🎮 Game Shelf</a></h1>
            </div>
            <div class="header-bottom">
                <nav>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="#">Meus Jogos</a></li>
                        <li><a href="#">Explorar</a></li>
                        <li><a href="#">Comunidade</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <button class="login-btn" onclick="window.location.href='login.php'">Login</button>
                    <button class="register-btn" onclick="window.location.href='register.php'">Registrar</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Conteúdo da Página do Jogo -->
    <main>
        <div class="container">
            <div class="game-page-wrapper">
                
                <!-- Sidebar com a capa e ações -->
                <aside class="game-sidebar">
                    <img src="<?php echo htmlspecialchars($game['game_cover_url']); ?>" alt="Capa de <?php echo htmlspecialchars($game['title']); ?>" class="sidebar-cover-img">
                    <div class="sidebar-actions">
                        <button class="add-to-list-btn">Adicionar à Minha Lista</button>
                    </div>
                </aside>

                <!-- Conteúdo principal com detalhes -->
                <div class="game-main-content">
                    <div class="game-title-section">
                        <h2><?php echo htmlspecialchars($game['title']); ?></h2>
                        <p class="game-developer"><?php echo htmlspecialchars($game['developer']); ?></p>
                    </div>

                    <div class="game-stats-bar">
                        <div class="stat-block">
                            <div class="label">Nota Média:</div>
                            <div class="score"><?php echo $average_rating; ?></div>
                        </div>
                        <div class="stat-block">
                            <div class="label">Jogando agora:</div>
                            <div class="value"><?php echo $game['completed_count']; ?></div>
                        </div>
                         <div class="stat-block">
                            <div class="label">Gênero:</div>
                            <div class="value"><?php echo htmlspecialchars($game['genre']); ?></div>
                        </div>
                    </div>

                    <div class="game-synopsis">
                        <h3>Sinopse</h3>
                        <p><?php echo nl2br(htmlspecialchars($game['description'])); ?></p>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Game Shelf. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>
