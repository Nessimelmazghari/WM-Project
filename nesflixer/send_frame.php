<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'vendor/autoload.php'; // Composer autoloader

$options = array(
    'cluster' => 'eu',  // Gebruik je cluster (eu)
    'useTLS' => true
);

// Maak een nieuwe Pusher-instantie
$pusher = new Pusher\Pusher(
    '428059e9e1cc5055650d', // Je app-key
    '804ca18d02ffd383d3a7', // Je app-secret
    '1906293',               // Je app-id
    $options
);

// Verkrijg de POST-data van de frontend (de chunks)
$data = json_decode(file_get_contents('php://input'), true);

// Verstuur de frame data naar Pusher
$pusher->trigger('video-channel', 'new-frame-chunk', $data);

echo "Frame chunk verzonden!";
?>
