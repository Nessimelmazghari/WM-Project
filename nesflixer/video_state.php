<?php
$stateFile = 'video_state.txt';

// Handle the state update (POST request)
if (isset($_POST['state'])) {
    $state = $_POST['state'];
    // Save the new state to the text file
    file_put_contents($stateFile, $state);
}

// Get the current state from the text file (GET request)
$currentState = file_get_contents($stateFile);

// Return the state as a JSON response
header('Content-Type: application/json');
echo json_encode(['state' => $currentState]);
?>
