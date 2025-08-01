<?php
session_start();
include('../database/connection.php');
include_once('../PhP/session.php');

// Verifica se está logado
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obtém dados do usuário
$user_data = getUserData();
$user_id = $user_data['id'];

// Obtém dados do POST
$game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : 'Wishlist';
$personal_rating = isset($_POST['personal_rating']) && $_POST['personal_rating'] !== '' ? intval($_POST['personal_rating']) : null;

// Valida game_id
if ($game_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID do jogo inválido']);
    exit;
}

// Valida status
$valid_statuses = ['Playing', 'Completed', 'Dropped', 'Wishlist'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Status inválido']);
    exit;
}

// Valida rating se fornecido
if ($personal_rating !== null && ($personal_rating < 0 || $personal_rating > 10)) {
    echo json_encode(['success' => false, 'message' => 'Nota deve ser entre 0 e 10']);
    exit;
}

try {
    // Verifica se o jogo existe
    $check_game_sql = "SELECT id, title FROM games WHERE id = ?";
    $check_game_stmt = $conn->prepare($check_game_sql);
    $check_game_stmt->bind_param('i', $game_id);
    $check_game_stmt->execute();
    $game_result = $check_game_stmt->get_result();
    
    if ($game_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Jogo não encontrado']);
        exit;
    }
    
    $game_data = $game_result->fetch_assoc();
    $game_title = $game_data['title'];
    
    // Verifica se o jogo já está na lista do usuário
    $check_sql = "SELECT id, status, personal_rating FROM user_games WHERE user_id = ? AND game_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('ii', $user_id, $game_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Jogo já existe na lista - atualizar
        $existing_game = $result->fetch_assoc();
        
        $update_sql = "UPDATE user_games SET status = ?, personal_rating = ? WHERE user_id = ? AND game_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('siii', $status, $personal_rating, $user_id, $game_id);
        
        if ($update_stmt->execute()) {
            // Atualiza também na tabela ratings se houver nota
            if ($personal_rating !== null) {
                $rating_check_sql = "SELECT id FROM ratings WHERE rater_id = ? AND rated_game_id = ?";
                $rating_check_stmt = $conn->prepare($rating_check_sql);
                $rating_check_stmt->bind_param('ii', $user_id, $game_id);
                $rating_check_stmt->execute();
                $rating_result = $rating_check_stmt->get_result();
                
                if ($rating_result->num_rows > 0) {
                    // Atualiza rating existente
                    $rating_update_sql = "UPDATE ratings SET rating = ? WHERE rater_id = ? AND rated_game_id = ?";
                    $rating_update_stmt = $conn->prepare($rating_update_sql);
                    $rating_update_stmt->bind_param('iii', $personal_rating, $user_id, $game_id);
                    $rating_update_stmt->execute();
                } else {
                    // Insere novo rating
                    $rating_insert_sql = "INSERT INTO ratings (rater_id, rated_game_id, rating) VALUES (?, ?, ?)";
                    $rating_insert_stmt = $conn->prepare($rating_insert_sql);
                    $rating_insert_stmt->bind_param('iii', $user_id, $game_id, $personal_rating);
                    $rating_insert_stmt->execute();
                }
            } else {
                // Remove rating se nota foi removida
                $rating_delete_sql = "DELETE FROM ratings WHERE rater_id = ? AND rated_game_id = ?";
                $rating_delete_stmt = $conn->prepare($rating_delete_sql);
                $rating_delete_stmt->bind_param('ii', $user_id, $game_id);
                $rating_delete_stmt->execute();
            }
            
            $status_map = [
                'Playing' => 'Jogando',
                'Completed' => 'Completo',
                'Wishlist' => 'Lista de Desejos',
                'Dropped' => 'Abandonado'
            ];
            
            echo json_encode([
                'success' => true, 
                'message' => "Status de '{$game_title}' atualizado para '{$status_map[$status]}'",
                'action' => 'updated',
                'game_title' => $game_title,
                'status' => $status,
                'rating' => $personal_rating
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar jogo na lista']);
        }
    } else {
        // Jogo não existe na lista - inserir novo
        $insert_sql = "INSERT INTO user_games (user_id, game_id, status, personal_rating) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param('iisi', $user_id, $game_id, $status, $personal_rating);
        
        if ($insert_stmt->execute()) {
            // Insere também na tabela ratings se houver nota
            if ($personal_rating !== null) {
                $rating_insert_sql = "INSERT INTO ratings (rater_id, rated_game_id, rating) VALUES (?, ?, ?)";
                $rating_insert_stmt = $conn->prepare($rating_insert_sql);
                $rating_insert_stmt->bind_param('iii', $user_id, $game_id, $personal_rating);
                $rating_insert_stmt->execute();
            }
            
            $status_map = [
                'Playing' => 'Jogando',
                'Completed' => 'Completo',
                'Wishlist' => 'Lista de Desejos',
                'Dropped' => 'Abandonado'
            ];
            
            echo json_encode([
                'success' => true, 
                'message' => "'{$game_title}' adicionado à sua lista como '{$status_map[$status]}'",
                'action' => 'added',
                'game_title' => $game_title,
                'status' => $status,
                'rating' => $personal_rating
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao adicionar jogo à lista']);
        }
    }
    
} catch (Exception $e) {
    error_log("Erro em addgame_tolist.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

// Fecha conexões
if (isset($check_game_stmt)) $check_game_stmt->close();
if (isset($check_stmt)) $check_stmt->close();
if (isset($update_stmt)) $update_stmt->close();
if (isset($insert_stmt)) $insert_stmt->close();
$conn->close();
?>
