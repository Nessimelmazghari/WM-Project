<?php
header('Content-Type: application/json');

$videos = [
    ["title" => "Seisoen 1 episode 1 part 1", "url" => "https://mega.nz/embed/z3ZjQRKQ#3gaLP4FLNOzhX2ayovVhGjzpWFkcagztVGC5DmHKBlE"]
];

echo json_encode($videos);
?>
