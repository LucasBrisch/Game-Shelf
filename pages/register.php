<?php
session_start();
include_once('../PhP/session.php');

// Redireciona se já estiver logado
redirectIfLoggedIn();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Shelf - Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/register.css">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1>🎮 Game Shelf</h1>
                <h2>Criar nova conta</h2>
                <p>Junte-se à nossa comunidade de gamers!</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="register-form" method="POST" action="../PhP/auth.php">
                <div class="form-group">
                    <label for="full_name">Nome Completo</label>
                    <input 
                        type="text" 
                        id="full_name" 
                        name="full_name" 
                        placeholder="Digite seu nome completo"
                        required
                        value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="username">Nome de Usuário</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Digite um nome de usuário único"
                        required
                        minlength="3"
                        maxlength="20"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    >
                    <small class="form-hint">Entre 3 e 20 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Digite seu email"
                        required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Digite uma senha segura"
                            required
                            minlength="6"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <span class="eye-icon">👁️</span>
                        </button>
                    </div>
                    <small class="form-hint">Mínimo de 6 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Senha</label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Confirme sua senha"
                            required
                            minlength="6"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <span class="eye-icon">👁️</span>
                        </button>
                    </div>
                </div>

                <div class="password-match-indicator" id="passwordMatch">
                    <span class="match-text">As senhas coincidem</span>
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="agree_terms" required>
                        <span class="checkmark"></span>
                        Eu concordo com os <a href="#" class="terms-link">Termos de Uso</a> e <a href="#" class="terms-link">Política de Privacidade</a>
                    </label>
                </div>

                <button type="submit" name="register" class="register-btn">
                    Criar Conta
                </button>
            </form>

            <div class="register-footer">
                <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
                <div class="back-to-home">
                    <a href="index.php">← Voltar ao início</a>
                </div>
            </div>
        </div>

        <div class="background-decoration">
            <div class="decoration-circle circle-1"></div>
            <div class="decoration-circle circle-2"></div>
            <div class="decoration-circle circle-3"></div>
            <div class="decoration-circle circle-4"></div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eyeIcon = input.nextElementSibling.querySelector('.eye-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.textContent = '🙈';
            } else {
                input.type = 'password';
                eyeIcon.textContent = '👁️';
            }
        }

        // Password match validation
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const indicator = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                indicator.style.display = 'none';
                return;
            }
            
            indicator.style.display = 'block';
            
            if (password === confirmPassword) {
                indicator.className = 'password-match-indicator match';
                indicator.querySelector('.match-text').textContent = '✓ As senhas coincidem';
            } else {
                indicator.className = 'password-match-indicator no-match';
                indicator.querySelector('.match-text').textContent = '✗ As senhas não coincidem';
            }
        }

        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            
            passwordInput.addEventListener('input', checkPasswordMatch);
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });

            // Username validation
            const usernameInput = document.getElementById('username');
            usernameInput.addEventListener('input', function() {
                const value = this.value;
                const isValid = /^[a-zA-Z0-9_]{3,20}$/.test(value);
                
                if (value.length > 0) {
                    if (isValid) {
                        this.style.borderColor = '#10b981';
                    } else {
                        this.style.borderColor = '#ef4444';
                    }
                } else {
                    this.style.borderColor = '';
                }
            });
        });
    </script>
</body>
</html>
