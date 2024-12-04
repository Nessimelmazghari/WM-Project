<?php
header('Content-Type: application/json');
echo json_encode(['results' => [
    ['title' => 'Sidemen Among Us', 'url' => 'https://www.youtube.com/embed/fKjdiU0XlF4'],
    ['title' => 'Brainfarts', 'url' => 'video.mp4'],
	['title' => 'Dummy video', 'url' => 'https://www.w3schools.com/html/mov_bbb.mp4']
]]);
?>
