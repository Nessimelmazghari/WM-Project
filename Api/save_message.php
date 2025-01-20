<?php
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once 'db.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat_id = $_POST['chat_id'] ?? null;
    $message = $_POST['message'] ?? null;
    $username = $_POST['username'] ?? null;

    if (!$chat_id || !$message || !$username) {
        echo json_encode(['error' => 'Missing chat_id, message, or username']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO messages (chat_id, message, username, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $chat_id, $message, $username);

    echo json_encode(['success' => $stmt->execute()]);
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
$conn->close();
?>
