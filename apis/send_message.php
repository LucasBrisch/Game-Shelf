<?php
include('../database/connection.php');
include('../PhP/session.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Você precisa estar logado para enviar mensagens.']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Dados JSON inválidos.']);
    exit;
}

$receiver_id = isset($data['receiver_id']) ? intval($data['receiver_id']) : 0;
$message = isset($data['message']) ? trim($data['message']) : '';

// Validate input
if ($receiver_id <= 0 || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos.']);
    exit;
}

// Validate message length (max 5000 characters)
if (strlen($message) > 5000) {
    echo json_encode(['success' => false, 'error' => 'Mensagem muito longa. Máximo 5000 caracteres.']);
    exit;
}

$sender_id = $_SESSION['user_id'];

// Check if sender and receiver are the same
if ($sender_id === $receiver_id) {
    echo json_encode(['success' => false, 'error' => 'Você não pode enviar mensagens para si mesmo.']);
    exit;
}

// Check if they are friends (friendship must be accepted)
$friendship_check = $conn->prepare("
    SELECT id FROM friendships 
    WHERE ((requester_id = ? AND receiver_id = ?) OR (requester_id = ? AND receiver_id = ?))
    AND status = 'accepted'
");
$friendship_check->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$friendship_check->execute();
$friendship_result = $friendship_check->get_result();

if ($friendship_result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Você só pode enviar mensagens para amigos.']);
    exit;
}
$friendship_check->close();

// Insert the message
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);

if ($stmt->execute()) {
    $message_id = $stmt->insert_id;
    echo json_encode([
        'success' => true, 
        'message_id' => $message_id,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao enviar mensagem.']);
}

$stmt->close();
$conn->close();
?>
