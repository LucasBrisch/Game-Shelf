<?php
session_start();
include('../database/connection.php');

function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Processamento do Login
if (isset($_POST['login'])) {
    $username_or_email = validateInput($_POST['username_or_email']);
    $password = $_POST['password'];
    
    if (empty($username_or_email) || empty($password)) {
        $_SESSION['error'] = 'Por favor, preencha todos os campos.';
        header('Location: ../pages/login.php');
        exit;
    }
    
    // Verifica se é email ou username
    $sql = "SELECT id, username, email, password_hash, full_name FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (verifyPassword($password, $user['password_hash'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['success'] = 'Login realizado com sucesso!';
            header('Location: ../pages/index.php');
            exit;
        } else {
            $_SESSION['error'] = 'Credenciais inválidas.';
            header('Location: ../pages/login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Usuário não encontrado.';
        header('Location: ../pages/login.php');
        exit;
    }
}

// Processamento do Registro
if (isset($_POST['register'])) {
    $full_name = validateInput($_POST['full_name']);
    $username = validateInput($_POST['username']);
    $email = validateInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validações
    if (empty($full_name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'Por favor, preencha todos os campos.';
        header('Location: ../pages/register.php');
        exit;
    }
    
    if (!validateEmail($email)) {
        $_SESSION['error'] = 'Por favor, insira um email válido.';
        header('Location: ../pages/register.php');
        exit;
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'A senha deve ter pelo menos 6 caracteres.';
        header('Location: ../pages/register.php');
        exit;
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'As senhas não coincidem.';
        header('Location: ../pages/register.php');
        exit;
    }
    
    if (strlen($username) < 3 || strlen($username) > 20) {
        $_SESSION['error'] = 'O nome de usuário deve ter entre 3 e 20 caracteres.';
        header('Location: ../pages/register.php');
        exit;
    }
    
    // Verifica se o username ou email já existem
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'Nome de usuário ou email já estão em uso.';
        header('Location: ../pages/register.php');
        exit;
    }
    
    // Cria o hash da senha
    $password_hash = hashPassword($password);
    
    // Insere o novo usuário
    $sql = "INSERT INTO users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $full_name, $username, $email, $password_hash);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Conta criada com sucesso! Faça login para continuar.';
        header('Location: ../pages/login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Erro ao criar conta. Tente novamente.';
        header('Location: ../pages/register.php');
        exit;
    }
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ../pages/index.php');
    exit;
}
?>
