<?php
require_once 'config.php';

// Abilita CORS per tutte le origini
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gestione preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Router semplice basato su query string o path
$action = $_GET['action'] ?? '';

// Endpoint: /api.php?action=health
if ($action === 'health') {
    $db_healthy = false;
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT 1");
            $db_healthy = $stmt->fetch() !== false;
        } catch (Exception $e) {
            $db_healthy = false;
        }
    }
    json_response([
        'status' => 'healthy',
        'database' => $db_healthy,
        'message' => $db_healthy ? 'Database connected' : 'Database not connected (using mock data)'
    ]);
}

// Endpoint: /api.php?action=list_projects (GET)
if ($action === 'list_projects' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
            $projects = $stmt->fetchAll();
            json_response(['projects' => $projects]);
        } catch (Exception $e) {
            // continua a mock
        }
    }
    // Mock data se database non disponibile
    $mock_projects = [
        [
            'id' => 1,
            'name' => 'Progetto Demo 1',
            'status' => 'completed',
            'user_id' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ];
    json_response(['projects' => $mock_projects]);
}

// Endpoint: /api.php?action=create_project (POST)
if ($action === 'create_project' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? 'Nuovo Progetto';
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (name, status) VALUES (?, 'draft')");
            $stmt->execute([$name]);
            $project_id = $pdo->lastInsertId();
            
            // Notifica Telegram (se configurato)
            if (TELEGRAM_BOT_TOKEN && TELEGRAM_CHAT_ID) {
                $message = "🎉 Nuovo progetto creato: $name (ID: $project_id)";
                file_get_contents("https://api.telegram.org/bot".TELEGRAM_BOT_TOKEN."/sendMessage?chat_id=".TELEGRAM_CHAT_ID."&text=".urlencode($message));
            }
            
            log_message("Project created: $project_id - $name");
            json_response(['status' => 'created', 'project_id' => $project_id]);
        } catch (Exception $e) {
            // continua a mock
        }
    }
    
    // Mock response se database non disponibile
    $mock_project_id = time();
    log_message("Mock project created: $mock_project_id - $name");
    json_response(['status' => 'created', 'project_id' => $mock_project_id]);
}

// Endpoint: /api.php?action=research (POST)
if ($action === 'research' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $query = $input['query'] ?? '';
    $limit = $input['limit'] ?? 10;
    
    // Mock data (da integrare con n8n)
    json_response([
        'status' => 'queued',
        'query' => $query,
        'results' => [
            ['channel' => 'Demo Channel 1', 'opportunity_score' => 85],
            ['channel' => 'Demo Channel 2', 'opportunity_score' => 72]
        ]
    ]);
}

// Endpoint: /api.php?action=generate_lyrics (POST)
if ($action === 'generate_lyrics' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = $input['prompt'] ?? '';
    $language = $input['language'] ?? 'IT';
    $instrumental = $input['instrumental'] ?? false;
    
    // Mock data (da integrare con n8n)
    json_response([
        'status' => 'generated',
        'lyrics' => "[Verse]\nRoma sotto luna...\n[Chorus]\n...",
        'language' => $language,
        'instrumental' => $instrumental
    ]);
}

// Endpoint: /api.php?action=generate_music (POST)
if ($action === 'generate_music' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = $input['prompt'] ?? '';
    $lyrics = $input['lyrics'] ?? '';
    $make_instrumental = $input['make_instrumental'] ?? false;
    
    // Mock data (da integrare con n8n)
    json_response([
        'status' => 'generating',
        'track_id' => 'suno_'.time(),
        'prompt' => $prompt,
        'make_instrumental' => $make_instrumental
    ]);
}

// Endpoint: /api.php?action=generate_video (POST)
if ($action === 'generate_video' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = $input['prompt'] ?? '';
    $duration = $input['duration'] ?? 10;
    $resolution = $input['resolution'] ?? '1080p';
    
    // Mock data (da integrare con n8n)
    json_response([
        'status' => 'generating',
        'video_id' => 'kling_'.time(),
        'prompt' => $prompt,
        'duration' => $duration,
        'resolution' => $resolution
    ]);
}

// Endpoint: /api.php?action=webhook_n8n (POST)
if ($action === 'webhook_n8n' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $event = $input['event'] ?? '';
    $data = $input['data'] ?? [];
    
    log_message("Webhook received: $event - " . json_encode($data));
    
    // Aggiorna database se necessario
    if ($pdo && isset($data['project_id']) && isset($data['status'])) {
        try {
            $stmt = $pdo->prepare("UPDATE projects SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $data['project_id']]);
        } catch (Exception $e) {
            // Ignora errore
        }
    }
    
    json_response(['status' => 'received']);
}

// Default: endpoint non trovato
json_response(['status' => 'error', 'message' => 'Endpoint not found'], 404);
?>
