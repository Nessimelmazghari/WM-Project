<?php
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();

// Set default state if not already set
if (!isset($_SESSION['video_state'])) {
    $_SESSION['video_state'] = 'paused'; // Initial state could be 'paused' or 'playing'
}

// If a new state is set (from a client request), update it
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['state'])) {
    $_SESSION['video_state'] = $_POST['state'];
}

// Send the current state back to the client via GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['state' => $_SESSION['video_state']]);
}
?>
