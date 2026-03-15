# Installazione e Configurazione MusicVideoForge PRO

Questo documento guida attraverso l'installazione e la configurazione completa del progetto.

## Prerequisiti

### 1. Docker Desktop
Scarica e installa Docker Desktop da [docker.com](https://www.docker.com/products/docker-desktop/).
Assicurati che Docker Desktop sia in esecuzione.

### 2. Git
Assicurati di avere Git installato per clonare il repository (se necessario).

### 3. API Keys
Registrati per ottenere le API keys gratuite:
- **OpenRouter**: [openrouter.ai](https://openrouter.ai/) (per modelli Gemini, Llama, DeepSeek)
- **KIE.ai**: [kie.ai](https://kie.ai/) (per Suno e Kling)
- **YouTube Data API**: [Google Cloud Console](https://console.cloud.google.com/) (per ricerca canali)

## Installazione Passo Passo

### Passo 1: Clona il Repository
```bash
git clone https://github.com/MarlonMarketing/video-music-generator.git
cd video-music-generator
```

### Passo 2: Configura le Variabili d'Ambiente
Copia il file di esempio e inserisci le tue API keys:
```bash
cp .env.example .env
```

Modifica il file `.env` con le tue credenziali:
```env
# API Keys (OBTAIN THESE FROM PROVIDERS)
OPENROUTER_API_KEY=sk-or-v1-tuo-key-qui
KIEAI_API_KEY=kapi-tuo-key-qui
YOUTUBE_API_KEY=AIzaSy-tuo-key-qui

# App Settings
N8N_PASSWORD=your_secure_password
SECRET_KEY=super_secret_key_change_this

# Redis (Docker interna)
REDIS_HOST=redis
REDIS_PORT=6379

# Celery
CELERY_BROKER_URL=redis://redis:6379/0
CELERY_RESULT_BACKEND=redis://redis:6379/0
```

### Passo 3: Avvia i Servizi con Docker
```bash
docker-compose up --build -d
```

Questo comando avvierà:
1. **Backend** (FastAPI) - Porta 8000
2. **Frontend** (React) - Porta 3000
3. **n8n** (Workflows) - Porta 5678
4. **Redis** (Coda) - Porta 6379
5. **Celery-FFmpeg** (Post-processing)

### Passo 4: Verifica l'Installazione
Controlla che tutti i container siano in esecuzione:
```bash
docker-compose ps
```

Dovresti vedere 5 servizi con stato "Up".

### Passo 5: Accedi ai Servizi

#### Frontend (Interfaccia Web)
URL: http://localhost:3000
- Interfaccia a schede per ricerca, lyrics, musica, video e progetti

#### Backend API
URL: http://localhost:8000
- Documentazione API: http://localhost:8000/docs

#### n8n (Workflow Manager)
URL: http://localhost:5678
- Username: `admin`
- Password: quella impostata in `.env` (N8N_PASSWORD)

## Configurazione n8n

### Importa i Workflow
1. Accedi a n8n (http://localhost:5678)
2. Vai su "Workflows" → "Import from File"
3. Importa i 5 workflow dalla cartella `n8n_workflows`:
   - `1_youtube_research.json`
   - `2_pro_lyrics.json`
   - `3_kie_suno_pro.json`
   - `4_kling_cinematic.json`
   - `5_ffmpeg_pro.json`

### Configura le Credenziali in n8n
1. Vai su "Credentials" → "Add Credential"
2. Aggiungi:
   - **OpenRouter API**: Inserisci la tua `OPENROUTER_API_KEY`
   - **HTTP Header Auth**: Per KIE.ai, usa `Authorization: Bearer <KIEAI_API_KEY>`
   - **YouTube API**: Inserisci la tua `YOUTUBE_API_KEY`

### Attiva i Workflow
Per ogni workflow importato:
1. Apri il workflow
2. Clicca su "Active" per attivarlo
3. Salva le modifiche

## Utilizzo del Progetto

### 1. Ricerca YouTube
1. Vai su "Research" nel frontend
2. Inserisci un canale o argomento
3. Clicca "Start Research"
4. Visualizza i risultati con opportunity score

### 2. Generazione Lyrics
1. Vai su "Lyrics"
2. Inserisci un prompt descrittivo
3. Seleziona lingua (IT/EN/ES)
4. Attiva "Instrumental only" se necessario
5. Clicca "Generate Lyrics"

### 3. Generazione Musica
1. Vai su "Music"
2. Inserisci stile e parametri
3. Incolla le lyrics generate (o lascia vuoto per strumentale)
4. Clicca "Generate Music"
5. Ascolta l'anteprima

### 4. Generazione Video
1. Vai su "Video"
2. Inserisci prompt cinematico
3. Seleziona durata e risoluzione
4. Clicca "Generate Video"
5. Visualizza l'anteprima video

### 5. Gestione Progetti
1. Vai su "Projects"
2. Visualizza tutti i progetti creati
3. Scarica ZIP dei progetti
4. Visualizza varianti A/B

## Comandi Utili

### Visualizzare i Logs
```bash
docker-compose logs -f
```

### Riavviare i Servizi
```bash
docker-compose restart
```

### Ferma i Servizi
```bash
docker-compose down
```

### Aggiornare il Progetto
```bash
git pull origin main
docker-compose up --build -d
```

## Risoluzione Problemi

### Problema: Porte già in uso
Se le porte 3000, 8000 o 5678 sono già in uso:
1. Modifica `docker-compose.yml` cambiando le porte
2. Esempio: `"3001:80"` invece di `"3000:80"`

### Problema: API Keys non valide
Verifica che le API keys siano corrette e attive nei rispettivi provider.

### Problema: Container non si avvia
```bash
# Ferma tutto
docker-compose down

# Rimuovi volumi (ATTENZIONE: cancella dati)
docker-compose down -v

# Riavvia
docker-compose up --build
```

### Problema: Frontend non si connette al Backend
Verifica che il servizio `backend` sia in esecuzione e che il file `frontend/nginx.conf` sia configurato correttamente.

## Struttura Cartelle

```
video-music-generator/
├── backend/              # API FastAPI + Celery
├── frontend/             # Interfaccia React
├── n8n_workflows/        # Workflow n8n (JSON)
├── openclaw_agents/      # Agenti AI (YAML)
├── docker-compose.yml    # Configurazione Docker
├── .env.example          # Template variabili d'ambiente
├── deploy.sh             # Script deploy automatico
├── README.md             # Documentazione principale
├── INSTALL.md            # Questo file
└── VERSION               # Versione corrente (1.0.0)
```

## Supporto

Per problemi o domande:
1. Controlla i logs: `docker-compose logs -f`
2. Verifica la documentazione API: http://localhost:8000/docs
3. Controlla lo stato di n8n: http://localhost:5678

## Aggiornamenti Futuri

Per aggiornare alla versione successiva:
```bash
git pull origin main
docker-compose up --build -d
```

La versione corrente è: **v1.0.0**
