# Installazione Video Music Generator (LAMP Stack) - Guida Passo Passo

Questa guida ti accompagna nell'installare e configurare "Video Music Generator" su un server Linux (Oracle Cloud ARM) con stack LAMP e Virtualmin.

## 🎯 Architettura
- **Frontend**: App React statica (servita da Apache)
- **Backend**: API PHP (Apache/mod_php)
- **Database**: MySQL (Virtualmin)
- **Automazione**: n8n (Docker) per workflow AI
- **Web Server**: Apache 2.4

---

## 📋 Prerequisiti
1. Server Oracle Cloud ARM (Ubuntu 24.04 LTS) con Virtualmin
2. Dominio puntato al server (es. `videomusic.plamanco.com`)
3. Accesso SSH/FTP al server
4. Client FTP (es. FileZilla) o accesso al File Manager di Virtualmin

---

## 🔧 Installazione Dettagliata

### STEP 1: Configura Database MySQL
1. Accedi a Virtualmin → **System Settings** → **Database Servers**
2. Clicca **Create a new database**
3. Inserisci:
   - **Database name**: `videomusic`
   - **Database user**: `admin@videomusic`
   - **Password**: Scegli una password forte (es. `Plamanco_2026`)
4. Clicca **Create**
5. (Opzionale) Importa lo schema da `database/schema.sql` se presente

✅ *Verifica: Il database `videomusic` esiste e l'utente `admin@videomusic` ha privilegi.*

---

### STEP 2: Carica i file PHP sul server (FTP)
1. Apri il tuo client FTP (FileZilla) o Virtualmin → **File Manager**
2. Connettiti al server:
   - Host: `ftp.plamanco.com`
   - Username: `videomusic`
   - Password: `Plamanco_2026`
3. Naviga in `/home/videomusic/public_html/api/`
4. Carica questi file dal repository:
   - `backend_php/api.php`
   - `backend_php/auth.php`
5. Sovrascrivi se richiesto

✅ *Verifica: Visitando `https://videomusic.plamanco.com/api/api.php?action=health` dovresti vedere `{"status":"healthy",...}`.*

---

### STEP 3: Crea la tabella utenti
Apri il terminale (o usa curl) ed esegui:

```bash
curl -X POST "https://videomusic.plamanco.com/api/api.php?action=create_users_table"
```

Dovresti ricevere: `{"status":"success","message":"Users table created or already exists"}`

---

### STEP 4: Crea l'utente Admin
Registrati tramite API per ottenere il token JWT:

```bash
curl -X POST "https://videomusic.plamanco.com/api/api.php?action=register" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "email": "admin@example.com",
    "password": "admin_password_123"
  }'
```

**Risposta attesa:**
```json
{
  "status": "success",
  "message": "User registered",
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "user": {
    "id": 1,
    "username": "admin",
    "email": "admin@example.com"
  }
}
```

✅ *Salva il token JWT: lo userai per le chiamate autenticate.*

---

### STEP 5: Ottieni le tue API Keys
Prima di procedere, assicurati di aver creato le seguenti chiavi:

- **YouTube Data API v3** (già ottenuta: `AIzaSyBg6IIhXPqaPTUgaeg5gUf5dk8kUzcjXeA`)
- **OpenRouter API Key**: Registrati su [openrouter.ai](https://openrouter.ai) → Settings → Keys → Create Key
- **KIE.ai API Key**: Registrati su [kie.ai](https://kie.ai) → API Keys → Create Key

---

### STEP 6: Salva le API Keys nel backend
Ora invia tutte e tre le chiavi al backend per sincronizzarle:

```bash
curl -X POST "https://videomusic.plamanco.com/api/api.php?action=save_api_keys" \
  -H "Content-Type: application/json" \
  -d '{
    "openrouter_key": "LA_TUA_CHIAVE_OPENROUTER",
    "kieai_key": "LA_TUA_CHIAVE_KIEAI",
    "youtube_key": "AIzaSyBg6IIhXPqaPTUgaeg5gUf5dk8kUzcjXeA"
  }'
```

**Risposta attesa:**
```json
{
  "status": "success",
  "message": "API keys saved and n8n configuration updated",
  "api_keys": { ... }
}
```

✅ *Le chiavi sono ora salvate in `n8n_config.json` e disponibili per i workflow n8n.*

---

### STEP 7: Installa n8n (se non l'hai già fatto)
Se non hai ancora n8n, installalo via Docker:

```bash
docker run -d \
  --name n8n \
  -p 5678:5678 \
  -v ~/.n8n:/home/node/.n8n \
  -e N8N_BASIC_AUTH_ACTIVE=true \
  -e N8N_BASIC_AUTH_USER=admin \
  -e N8N_BASIC_AUTH_PASSWORD=Plamanco_2026 \
  -e N8N_ENCRYPTION_KEY=genera_una_chiave_casuale \
  docker.n8n.io/n8nio/n8n
```

Configura il reverse proxy in Virtualmin per il sottodominio `n8n.plamanco.com`.

---

### STEP 8: Importa i Workflow in n8n
Dal tuo computer (dove hai il repository), esegui:

```bash
cd video-music-generator
node import_n8n_workflows.js n8n_workflows_api_v2.json
```

**Output atteso:**
```
🚀 Inizio importazione workflow in n8n...
✅ Workflow "1_youtube_research" importato con successo! ID: ...
✅ Workflow "2_pro_lyrics" importato con successo! ID: ...
...
✅ Importazione completata!
```

---

### STEP 9: Verifica e Attivazione Workflow
1. Vai su **https://n8n.plamanco.com**
2. Accedi con le credenziali impostate ( admin / Plamanco_2026 )
3. Vai su **Workflows**
4. Assicurati che i seguenti workflow siano presenti e **attivi** (toggle verde):
   - `1_youtube_research`
   - `2_pro_lyrics`
   - `3_kie_suno_pro`
   - `4_kling_cinematic`
   - `5_ffmpeg_pro`

Se non sono attivi, clicca sul toggle in alto a destra per ciascuno.

---

### STEP 10: Test Finale
#### Test YouTube Research:
```bash
curl -X POST "https://n8n.plamanco.com/webhook/research" \
  -H "Content-Type: application/json" \
  -d '{"query":"jazz music","limit":5}'
```

Dovresti vedere una risposta JSON con i risultati della ricerca.

#### Test dal Frontend:
1. Visita: **https://videomusic.plamanco.com**
2. Vai alla sezione **Research**
3. Inserisci una query (es. "jazz") e clicca **Cerca**
4. Dovresti vedere i canali YouTube con punteggi di opportunità

---

## 🛠️ Troubleshooting

| Problema | Soluzione |
|----------|-----------|
| Errore 404 webhook | Assicurati che il workflow sia attivo in n8n (toggle Verde) |
| Errore "Missing API key" | Verifica che `save_api_keys` abbia funzionato e che `n8n_config.json` esista |
| Workflow non parte | Controlla i log in n8n → **Executions** |
| Database connection error | Verifica le credenziali in `backend_php/config.php` |
| Permessi file | I file PHP devono essere leggibili da Apache (chmod 644) |

---

## 📁 Struttura del Repository
```
video-music-generator/
├── backend_php/
│   ├── api.php          # API REST principale
│   ├── auth.php         # Autenticazione utenti
│   └── config.php       # Configurazione database
├── frontend/            # App React (buildata in /public)
├── n8n_workflows/       # JSON dei workflow n8n
├── n8n_workflows_api_v2.json  # File combinato per importazione
├── import_n8n_workflows.js    # Script importazione
├── install.md           # Questo file
└── USER_GUIDE.md        # Manuale d'uso (creato di seguito)
```

---

## 🔐 Sicurezza
- Le API keys sono salvate in `n8n_config.json` (non accessibile publicamente via web, assicurati che `.htaccess` lo protegga)
- Usa password forti per admin e n8n
- Limita le chiavi API YouTube ai domini `videomusic.plamanco.com` e `n8n.plamanco.com`
- Cambia la chiave di crittografia n8n (`N8N_ENCRYPTION_KEY`) con un valore casuale

---

## 🎉 Fatto!
Il sistema è ora operativo. Puoi creare progetti, generare testi, musica e video.

**Link importanti:**
- Frontend: https://videomusic.plamanco.com
- Backend API: https://videomusic.plamanco.com/api/api.php
- n8n: https://n8n.plamanco.com (admin / Plamanco_2026)