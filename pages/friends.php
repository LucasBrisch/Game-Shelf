<?php
include('../database/connection.php');
include('../PhP/session.php');

// Require login to access friends page
requireLogin();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Amigos - Game Shelf</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/styles.css">
    <link rel="stylesheet" href="../Css/chat-modal.css">
    <style>
        .friends-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .friends-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .friends-header h2 {
            font-size: 2rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .friends-header p {
            color: var(--text-secondary);
        }

        .friends-list {
            display: grid;
            gap: 1rem;
        }

        .friend-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .friend-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .friend-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .friend-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 20px;
        }

        .friend-details h3 {
            margin: 0 0 0.25rem 0;
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .friend-details p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .chat-btn {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chat-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .no-friends {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }

        .no-friends h3 {
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php 
    include('../PhP/header.php');
    renderHeader();
    ?>

    <!-- Main Content -->
    <main>
        <div class="friends-container">
            <div class="friends-header">
                <h2>💬 Meus Amigos</h2>
                <p>Converse com seus amigos sobre jogos e compartilhe experiências!</p>
            </div>

            <div class="friends-list" id="friendsList">
                <div class="loading">Carregando amigos...</div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Game Shelf. Todos os direitos reservados.</p>
            <div>
                <a href="#">Termos de Serviço</a>
                <a href="#">Política de Privacidade</a>
            </div>
        </div>
    </footer>

    <!-- Chat Modal -->
    <script src="../js/chat.js"></script>
    
    <script>
        // Load friends list
        async function loadFriends() {
            try {
                const response = await fetch('../apis/get_friends.php');
                const data = await response.json();
                
                const friendsList = document.getElementById('friendsList');
                
                if (data.success) {
                    if (data.friends.length === 0) {
                        friendsList.innerHTML = `
                            <div class="no-friends">
                                <h3>😔 Você ainda não tem amigos</h3>
                                <p>Adicione amigos para começar a conversar!</p>
                            </div>
                        `;
                    } else {
                        // Helper function to escape HTML
                        function escapeHtml(text) {
                            const div = document.createElement('div');
                            div.textContent = text;
                            return div.innerHTML;
                        }
                        
                        friendsList.innerHTML = data.friends.map(friend => {
                            const escapedName = escapeHtml(friend.full_name);
                            const escapedUsername = escapeHtml(friend.username);
                            const firstLetter = escapedName.charAt(0).toUpperCase();
                            
                            return `
                                <div class="friend-card">
                                    <div class="friend-info">
                                        <div class="friend-avatar">${firstLetter}</div>
                                        <div class="friend-details">
                                            <h3>${escapedName}</h3>
                                            <p>@${escapedUsername}</p>
                                        </div>
                                    </div>
                                    <button class="chat-btn" data-friend-id="${friend.id}" data-friend-name="${escapedName}">
                                        💬 Conversar
                                    </button>
                                </div>
                            `;
                        }).join('');
                        
                        // Add event listeners to chat buttons
                        document.querySelectorAll('.chat-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const friendId = parseInt(this.dataset.friendId);
                                const friendName = this.dataset.friendName;
                                openChatModal(friendId, friendName);
                            });
                        });
                    }
                } else {
                    friendsList.innerHTML = `
                        <div class="no-friends">
                            <h3>❌ Erro ao carregar amigos</h3>
                            <p>${data.error || 'Tente novamente mais tarde.'}</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading friends:', error);
                document.getElementById('friendsList').innerHTML = `
                    <div class="no-friends">
                        <h3>❌ Erro de conexão</h3>
                        <p>Não foi possível carregar seus amigos.</p>
                    </div>
                `;
            }
        }

        // Load friends on page load
        loadFriends();
    </script>
</body>
</html>
