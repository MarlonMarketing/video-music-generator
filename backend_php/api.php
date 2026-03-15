<?php
require_once 'config.php';

// Abilita CORS per tutte le origini
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Configurazione n8n
define('N8N_URL', 'https://n8n.plamanco.com');
define('N8N_API_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhYjg2ZDlhNy1hOWMyLTQzZjMtOThhNC1jNmJmYjQzOTA0YTEiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYTZiYjkzOTYtZWYxMC00NDEyLTgzNDYtZWRiZjYwMzQxZTMxIiwiaWF0IjoxNzczNTg4MDcxfQ.zrHQGickMmZcdchxgtvr917rzJ7aYpee8VnmgnTGsQ8'); // Chiave API n8n

// Funzione per aggiornare le variabili d'ambiente in n8n
function updateN8nEnvironmentVariables($variables) {
    $n8n_url = N8N_URL;
    $api_key = N8N_API_KEY;
    
    // Per la versione gratuita di n8n, le variabili non sono supportate via API.
    // Alternativa: Salvare le chiavi in un file JSON condiviso che n8n può leggere.
    // Creiamo un file di configurazione che n8n può includere.
    
    $config_file = __DIR__ . '/../n8n_config.json';
    $config = [];
    
    if (file_exists($config_file)) {
        $config = json_decode(file_get_contents($config_file), true) ?? [];
    }
    
    // Aggiorna le variabili
    foreach ($variables as $key => $value) {
        $config[$key] = $value;
    }
    
    // Salva il file
    file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
    
    // Nota: Dovremmo anche aggiornare le variabili d'ambiente del container n8n,
    // ma per la versione gratuita non è possibile via API.
    // Soluzione temporanea: Salvare in file e modificare i workflow per leggere da file.
    
    return true;
}

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

// Endpoint temporaneo: /api.php?action=create_users_table (DA USARE SOLO UNA VOLTA)
if ($action === 'create_users_table' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$pdo) {
        json_response(['status' => 'error', 'message' => 'Database not connected'], 500);
    }
    
    try {
        // Crea tabella utenti se non esiste
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            api_keys JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        
        json_response(['status' => 'success', 'message' => 'Users table created or already exists']);
    } catch (Exception $e) {
        json_response(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
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

// Endpoint: /api.php?action=register (POST)
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
    
    // Genera token JWT semplice (senza libreria esterna)
    $payload = json_encode([
        'user_id' => $user_id,
        'exp' => time() + (60 * 60 * 24) // 24 ore
    ]);
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", 'SECRET_KEY_VIDEO_MUSIC', true));
    $token = "$header.$payload.$signature";
    
    json_response([
        'status' => 'success',
        'message' => 'User registered',
        'token' => $token,
        'user' => ['id' => $user_id, 'username' => $username, 'email' => $email]
    ]);
}

// Endpoint: /api.php?action=login (POST)
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Genera token JWT
    $payload = json_encode([
        'user_id' => $user['id'],
        'exp' => time() + (60 * 60 * 24) // 24 ore
    ]);
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", 'SECRET_KEY_VIDEO_MUSIC', true));
    $token = "$header.$payload.$signature";

    json_response([
        'status' => 'success',
        'message' => 'Login successful',
        'token' => $token,
        'user' => ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email']]
    ]);
}

// Endpoint: /api.php?action=save_api_keys (POST)
// Salva le API keys e aggiorna automaticamente n8n
if ($action === 'save_api_keys' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  
  // Verifica autenticazione (qui dovresti verificare il token JWT)
  // Per ora, accettiamo qualsiasi richiesta (solo per testing)
  
  $api_keys = [
      'OPENROUTER_API_KEY' => $input['openrouter_key'] ?? '',
      'KIEAI_API_KEY' => $input['kieai_key'] ?? '',
      'YOUTUBE_API_KEY' => $input['youtube_key'] ?? ''
  ];
  
  // Rimuovi le chiavi vuote
  $api_keys = array_filter($api_keys);
  
  // Aggiorna le variabili di n8n (salvando su file)
  $update_result = updateN8nEnvironmentVariables($api_keys);
  
  if ($update_result) {
      // Aggiorna anche il file config.php locale (opzionale)
      $config_content = file_get_contents(__DIR__ . '/config.php');
      foreach ($api_keys as $key => $value) {
          // Sostituisci la definizione esistente
          $pattern = "/define\('$key',.*\);/";
          $replacement = "define('$key', '$value');";
          $config_content = preg_replace($pattern, $replacement, $config_content);
      }
      file_put_contents(__DIR__ . '/config.php', $config_content);
      
      json_response([
          'status' => 'success',
          'message' => 'API keys saved and n8n configuration updated',
          'api_keys' => $api_keys
      ]);
  } else {
      json_response(['status' => 'error', 'message' => 'Failed to update n8n configuration'], 500);
  }
}

// Endpoint: /api.php?action=get_api_keys (GET)
// Restituisce le API keys salvate (per i workflow n8n)
if ($action === 'get_api_keys' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $config_file = __DIR__ . '/../n8n_config.json';
    
    if (file_exists($config_file)) {
        $config = json_decode(file_get_contents($config_file), true);
        json_response([
            'status' => 'success',
            'api_keys' => $config
        ]);
    } else {
        json_response([
            'status' => 'success',
            'api_keys' => []
        ]);
    }
}

// Default: endpoint non trovato
json_response(['status' => 'error', 'message' => 'Endpoint not found'], 404);
?>
