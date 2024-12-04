<?php
// Zorg ervoor dat je Ratchet geïnstalleerd hebt via Composer
require dirname(__DIR__) . '/vendor/autoload.php'; 

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class WebRTCServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "Nieuwe verbinding: ({$conn->resourceId})\n";
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Ontvangen van ({$from->resourceId}): $msg\n";
        
        // Stuur het bericht door naar alle verbonden clients behalve de afzender
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        echo "Verbinding gesloten: ({$conn->resourceId})\n";
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Fout: {$e->getMessage()}\n";
        $conn->close();
    }
}

// SSL-certificaten instellen
$context = stream_context_create([
    'ssl' => [
        'local_cert' => '/var/www/vhosts/nessimelmazghari-odisee.be/cert/certificate.crt', // Pad naar je certificaatbestand
        'local_pk' => '/var/www/vhosts/nessimelmazghari-odisee.be/cert/private.key',       // Pad naar je private key bestand
        'cafile' => '/var/www/vhosts/nessimelmazghari-odisee.be/cert/ca_bundle.crt',       // Pad naar je CA bundle bestand
        'verify_peer' => false,                          // Zet dit op false voor testen of als je certificaat niet gecontroleerd hoeft te worden
    ]
]);

// Start de WebSocket-server met SSL
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebRTCServer()
        )
    ),
    8080, // Poort waarop de WebSocket-server luistert
    '0.0.0.0', // Luistert naar alle IP-adressen
    $context // Voeg de SSL-context toe
);

// Start de server
$server->run();
?>
