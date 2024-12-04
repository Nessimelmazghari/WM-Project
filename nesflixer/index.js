const http = require('http');
const fs = require('fs');  // Bestandsmodule voor logging

const hostname = '0.0.0.0'; // Luistert op alle netwerkinterfaces
const port = 8081; // Poort 8081

// Logbestand
const logFile = './server.log';

// Functie om logs naar het bestand te schrijven
const logToFile = (message) => {
    const logMessage = `${new Date().toISOString()} - ${message}\n`;
    fs.appendFile(logFile, logMessage, (err) => {
        if (err) {
            console.error("Fout bij het loggen:", err);
        }
    });
};

const server = http.createServer((req, res) => {
    // Log het inkomende verzoek
    logToFile(`Verzoek ontvangen van ${req.connection.remoteAddress} naar ${req.url}`);
    
    res.statusCode = 200;
    res.setHeader('Content-Type', 'text/plain');
    res.end('Server is running on port 8081');
});

// Start de server
server.listen(port, hostname, () => {
    logToFile(`Server draait op http://${hostname}:${port}/`); // Log serverstart
    console.log(`Server is running at http://${hostname}:${port}/`);
});
