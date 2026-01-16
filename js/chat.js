// Chat Modal Handler
let chatModal = null;
let currentChatFriendId = null;
let chatRefreshInterval = null;

// Initialize chat modal
function initChatModal() {
    // Create modal HTML if it doesn't exist
    if (!document.getElementById('chatModal')) {
        const modalHTML = `
            <div id="chatModal" class="chat-modal-overlay">
                <div class="chat-container">
                    <div class="chat-header">
                        <div class="chat-header-info">
                            <div class="chat-avatar" id="chatAvatar"></div>
                            <div class="chat-user-info">
                                <h3 id="chatFriendName"></h3>
                                <p id="chatFriendRole">Amigo</p>
                            </div>
                        </div>
                        <button class="chat-close-btn" onclick="closeChatModal()">&times;</button>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <div class="chat-loading">Carregando mensagens</div>
                    </div>
                    <div class="chat-input-area">
                        <div class="chat-input-wrapper">
                            <textarea 
                                id="chatInput" 
                                class="chat-input" 
                                placeholder="Digite sua mensagem..."
                                rows="1"
                                maxlength="5000"
                            ></textarea>
                        </div>
                        <button class="chat-send-btn" id="chatSendBtn" onclick="sendMessage()">
                            ➤
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add enter key handler
        document.getElementById('chatInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Auto-resize textarea
        document.getElementById('chatInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });
    }
    chatModal = document.getElementById('chatModal');
}

// Open chat modal with a specific friend
function openChatModal(friendId, friendName) {
    if (!friendId) {
        console.error('Friend ID is required');
        return;
    }

    initChatModal();
    currentChatFriendId = friendId;
    
    // Set friend info
    document.getElementById('chatFriendName').textContent = friendName || 'Amigo';
    const avatar = document.getElementById('chatAvatar');
    avatar.textContent = friendName ? friendName.charAt(0).toUpperCase() : '?';
    
    // Show modal
    chatModal.classList.add('active');
    
    // Load messages
    loadMessages();
    
    // Start refresh interval (every 3 seconds)
    if (chatRefreshInterval) {
        clearInterval(chatRefreshInterval);
    }
    chatRefreshInterval = setInterval(loadMessages, 3000);
    
    // Focus input
    document.getElementById('chatInput').focus();
}

// Close chat modal
function closeChatModal() {
    if (chatModal) {
        chatModal.classList.remove('active');
        currentChatFriendId = null;
        
        // Clear refresh interval
        if (chatRefreshInterval) {
            clearInterval(chatRefreshInterval);
            chatRefreshInterval = null;
        }
    }
}

// Load messages
async function loadMessages() {
    if (!currentChatFriendId) return;
    
    try {
        const response = await fetch(`../apis/get_messages.php?friend_id=${currentChatFriendId}`);
        const data = await response.json();
        
        if (data.success) {
            renderMessages(data.messages);
        } else {
            showChatError(data.error || 'Erro ao carregar mensagens');
        }
    } catch (error) {
        console.error('Error loading messages:', error);
        showChatError('Erro de conexão');
    }
}

// Render messages
function renderMessages(messages) {
    const messagesContainer = document.getElementById('chatMessages');
    
    if (messages.length === 0) {
        messagesContainer.innerHTML = `
            <div class="chat-empty">
                <p>Nenhuma mensagem ainda.</p>
                <p>Envie a primeira mensagem! 👋</p>
            </div>
        `;
        return;
    }
    
    // Store scroll position and check if this is first render
    const wasEmpty = messagesContainer.querySelector('.chat-loading, .chat-empty') !== null;
    const isScrolledToBottom = messagesContainer.scrollHeight - messagesContainer.clientHeight <= messagesContainer.scrollTop + 50;
    
    // Build messages HTML
    let html = '';
    let lastDate = null;
    
    messages.forEach(msg => {
        const msgDate = new Date(msg.created_at);
        const dateStr = msgDate.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' });
        
        // Add date separator if date changed
        if (dateStr !== lastDate) {
            html += `<div class="date-separator">${dateStr}</div>`;
            lastDate = dateStr;
        }
        
        const timeStr = msgDate.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        const messageClass = msg.is_mine ? 'sent' : 'received';
        
        html += `
            <div class="message ${messageClass}">
                <div class="message-content">
                    <div class="message-bubble">${escapeHtml(msg.message)}</div>
                    <div class="message-time">${timeStr}</div>
                </div>
            </div>
        `;
    });
    
    messagesContainer.innerHTML = html;
    
    // Scroll to bottom if was already at bottom or if this is the first render
    if (isScrolledToBottom || wasEmpty) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

// Send message
async function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (!message || !currentChatFriendId) return;
    
    const sendBtn = document.getElementById('chatSendBtn');
    sendBtn.disabled = true;
    
    try {
        const response = await fetch('../apis/send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                receiver_id: currentChatFriendId,
                message: message
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            input.style.height = 'auto';
            loadMessages(); // Reload messages
        } else {
            alert(data.error || 'Erro ao enviar mensagem');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Erro de conexão ao enviar mensagem');
    } finally {
        sendBtn.disabled = false;
        input.focus();
    }
}

// Show error in chat
function showChatError(message) {
    const messagesContainer = document.getElementById('chatMessages');
    messagesContainer.innerHTML = `
        <div class="chat-empty">
            <p style="color: #ef4444;">❌ ${message}</p>
        </div>
    `;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (chatModal && e.target === chatModal) {
        closeChatModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && chatModal && chatModal.classList.contains('active')) {
        closeChatModal();
    }
});
