// Modal para adicionar jogo à lista
function openAddToListModal(gameId, gameTitle, gameImage = '') {
    // Verifica se o modal já existe, se não, cria
    if (!document.getElementById('addToListModal')) {
        createAddToListModal();
    }
    
    // Define os dados do jogo no modal
    document.getElementById('modalGameId').value = gameId;
    document.getElementById('modalGameTitle').textContent = gameTitle;
    
    // Define a imagem do jogo
    const gameImageElement = document.getElementById('modalGameImage');
    if (gameImage && gameImage !== '') {
        gameImageElement.src = gameImage;
        gameImageElement.style.display = 'block';
    } else {
        gameImageElement.style.display = 'none';
    }
    
    // Reseta o formulário
    document.getElementById('gameStatus').value = 'Wishlist';
    document.getElementById('gameRating').value = '';
    
    // Mostra o modal
    document.getElementById('addToListModal').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Previne scroll
}

// Cria o modal dinamicamente
function createAddToListModal() {
    const modalHTML = `
        <div id="addToListModal" class="modal-overlay">
            <div class="modal-content add-to-list-modal">
                <button class="modal-close-btn" onclick="closeAddToListModal()">&times;</button>
                
                <div class="modal-header">
                    <div class="game-info">
                        <img id="modalGameImage" src="" alt="Capa do jogo" class="modal-game-cover">
                        <div class="game-details">
                            <h2>Adicionar à Lista</h2>
                            <p class="modal-game-title" id="modalGameTitle"></p>
                        </div>
                    </div>
                </div>
                
                <form id="addToListForm" onsubmit="submitAddToList(event)">
                    <input type="hidden" id="modalGameId" value="">
                    
                    <div class="form-group">
                        <label for="gameStatus">Como você quer adicionar este jogo?</label>
                        <select id="gameStatus" required>
                            <option value="Wishlist">📋 Lista de Desejos</option>
                            <option value="Playing">🎮 Jogando Atualmente</option>
                            <option value="Completed">✅ Zerado/Completo</option>
                            <option value="Dropped">❌ Abandonei</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="gameRating">Sua nota (opcional)</label>
                        <div class="rating-input-wrapper">
                            <input type="number" id="gameRating" min="0" max="10" step="0.1" placeholder="0 - 10">
                            <span class="rating-help">Deixe vazio se não quiser avaliar ainda</span>
                        </div>
                    </div>
                    
                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="closeAddToListModal()">Cancelar</button>
                        <button type="submit" class="btn-submit">Adicionar à Lista</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Fecha o modal
function closeAddToListModal() {
    const modal = document.getElementById('addToListModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restaura scroll
    }
}

// Submete o formulário
function submitAddToList(event) {
    event.preventDefault();
    
    const gameId = document.getElementById('modalGameId').value;
    const status = document.getElementById('gameStatus').value;
    const rating = document.getElementById('gameRating').value;
    const gameTitle = document.getElementById('modalGameTitle').textContent;
    
    // Desabilita o botão para evitar cliques duplos
    const submitBtn = document.querySelector('.btn-submit');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adicionando...';
    
    // Prepara os dados
    const formData = new URLSearchParams({
        'game_id': gameId,
        'status': status
    });
    
    if (rating && rating !== '') {
        formData.append('personal_rating', rating);
    }
    
    // Faz a requisição
    fetch('../apis/addgame_tolist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message);
            closeAddToListModal();
            updateAddButton(data.action, data.status);
        } else {
            showErrorMessage(data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showErrorMessage('Erro ao conectar com o servidor. Tente novamente.');
    })
    .finally(() => {
        // Restaura o botão
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

// Atualiza o botão após adicionar
function updateAddButton(action, status) {
    const button = document.querySelector('.add-to-list-btn');
    if (button) {
        const statusMap = {
            'Playing': '🎮 Jogando',
            'Completed': '✅ Zerado',
            'Wishlist': '📋 Na Lista',
            'Dropped': '❌ Abandonado'
        };
        
        button.textContent = statusMap[status] || 'Na sua lista';
        button.classList.add('in-list');
    }
}

// Mostra mensagem de sucesso
function showSuccessMessage(message) {
    showNotification(message, 'success');
}

// Mostra mensagem de erro
function showErrorMessage(message) {
    showNotification(message, 'error');
}

// Sistema de notificações
function showNotification(message, type = 'info') {
    // Remove notificação anterior se existir
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Remove automaticamente após 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Fecha modal ao clicar fora dele
document.addEventListener('click', function(event) {
    const modal = document.getElementById('addToListModal');
    if (modal && event.target === modal) {
        closeAddToListModal();
    }
});

// Fecha modal com tecla ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddToListModal();
    }
});

// Função para verificar se usuário está logado (para usar nas páginas)
function checkLoginAndAddToList(gameId, gameTitle, gameImage = '') {
    // Esta função deve ser chamada pelo botão nas páginas
    // Verifica se há indicação de usuário logado (pode ser verificado via PHP ou sessão)
    
    // Se não estiver logado, redireciona
    if (typeof userLoggedIn !== 'undefined' && !userLoggedIn) {
        alert('Faça login para adicionar jogos à sua lista');
        window.location.href = 'login.php';
        return;
    }
    
    // Se estiver logado, abre o modal
    openAddToListModal(gameId, gameTitle, gameImage);
}
