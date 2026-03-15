<?php
// Configurazione Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'videomusic');
define('DB_USER', 'admin@videomusic');
define('DB_PASS', 'Plamanco_2026');

// Connessione
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pdo = null;
}

// API Keys
define('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY') ?: '');
define('KIEAI_API_KEY', getenv('KIEAI_API_KEY') ?: '');
define('YOUTUBE_API_KEY', getenv('YOUTUBE_API_KEY') ?: '');

// Telegram Bot
define('TELEGRAM_BOT_TOKEN', getenv('TELEGRAM_BOT_TOKEN') ?: '');
define('TELEGRAM_CHAT_ID', getenv('TELEGRAM_CHAT_ID') ?: '');

// Funzione per inviare risposte JSON
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Funzione per loggare
function log_message($message) {
    $log_file = __DIR__ . '/../logs/api.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}
?>
