<?php
require_once 'config.php';
session_start();

// Abilita CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Funzione per generare un token JWT semplice (senza libreria esterna)
function generateToken($user_id) {
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode([
        'user_id' => $user_id,
        'exp' => time() + (60 * 60 * 24) // 24 ore
    ]));
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", 'SECRET_KEY_VIDEO_MUSIC', true));
    return "$header.$payload.$signature";
}

// Endpoint: Registrazione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'register') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        json_response(['status' => 'error', 'message' => 'Missing fields'], 400);
    }

    if (!$pdo) {
        json_response(['status' => 'error', 'message' => 'Database not connected'], 500);
    }

    // Verifica se l'utente esiste già
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        json_response(['status' => 'error', 'message' => 'User already exists'], 409);
    }

    // Hash della password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Inserisci utente
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password_hash]);
    
    $user_id = $pdo->lastInsertId();
    
    // Genera token
    $token = generateToken($user_id);

    json_response([
        'status' => 'success',
        'message' => 'User registered',
        'token' => $token,
        'user' => ['id' => $user_id, 'username' => $username, 'email' => $email]
    ]);
}

// Endpoint: Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($email) || empty($password)) {
        json_response(['status' => 'error', 'message' => 'Missing fields'], 400);
    }

    if (!$pdo) {
        json_response(['status' => 'error', 'message' => 'Database not connected'], 500);
    }

    // Trova utente
    $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        json_response(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }

    // Genera token
    $token = generateToken($user['id']);

    json_response([
        'status' => 'success',
        'message' => 'Login successful',
        'token' => $token,
        'user' => ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email']]
    ]);
}

// Endpoint: Salva API Keys per utente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'save_api_keys') {
    // Verifica autenticazione (dovrebbe controllare il token JWT)
    // Per semplicità, qui saltiamo la verifica (in produzione aggiungere middleware)
    
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $input['user_id'] ?? 1; // Default per test
    $api_keys = $input['api_keys'] ?? [];

    if (!$pdo) {
        json_response(['status' => 'error', 'message' => 'Database not connected'], 500);
    }

    // Salva le chiavi nel campo JSON dell'utente
    $stmt = $pdo->prepare("UPDATE users SET api_keys = ? WHERE id = ?");
    $stmt->execute([json_encode($api_keys), $user_id]);

    // Aggiorna le variabili d'ambiente di n8n (simulato per ora)
    // In realtà, dovremmo chiamare l'API di n8n o salvare in un file condiviso
    
    json_response([
        'status' => 'success',
        'message' => 'API keys saved',
        'api_keys' => $api_keys
    ]);
}

// Funzione per inviare risposte JSON (definita anche in config.php, ma la ridichiaro per sicurezza)
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>