<?php
header('Content-Type: application/json');
require_once 'db.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$chat_id = $_GET['chat_id'] ?? null;

if (!$chat_id) {
    echo json_encode(['error' => 'Missing chat_id']);
    exit;
}

$stmt = $conn->prepare("SELECT message, username, timestamp FROM messages WHERE chat_id = ? ORDER BY timestamp ASC");
$stmt->bind_param("s", $chat_id);
$stmt->execute();

$result = $stmt->get_result();
$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
$stmt->close();
$conn->close();
?>
