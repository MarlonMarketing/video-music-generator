# Configurazione n8n per Video Music Creator

## 1. Importare i Workflow in n8n

Accedi a n8n.plamanco.com e importa i seguenti file JSON dalla directory `n8n_workflows/`:

1. **1_youtube_research.json** - Ricerca YouTube opportunità
2. **2_pro_lyrics.json** - Generazione lyrics con OpenRouter
3. **3_kie_suno_pro.json** - Generazione musica Suno via KIE.ai
4. **4_kling_cinematic.json** - Generazione video Kling via KIE.ai
5. **5_ffmpeg_pro.json** - Post-processing FFmpeg

### Procedura importazione:
1. Vai su n8n.plamanco.com
2. Clicca "Add Workflow" → "Import from File"
3. Seleziona il file JSON
4. Clicca "Import"
5. Ripeti per tutti i 5 workflow

## 2. Configurare le Credenziali API

### OpenRouter API Key
1. Vai su n8n.plamanco.com/credentials
2. Clicca "Add Credential" → "OpenRouter API"
3. Inserisci la tua chiave OpenRouter
4. Salva con nome "OpenRouter account"

### KIE.ai API Key
1. Vai su n8n.plamanco.com/credentials
2. Clicca "Add Credential" → "HTTP Header Auth"
3. Configura:
   - Name: KIEAI API
   - Header Name: Authorization
   - Header Value: Bearer TUA_CHIAVE_KIEAI
4. Salva

### YouTube API Key
1. Vai su n8n.plamanco.com/credentials
2. Clicca "Add Credential" → "Google API Key"
3. Inserisci la chiave YouTube Data API v3
4. Salva

## 3. Configurare Variabili d'Ambiente in n8n

Vai su n8n.plamanco.com/variables e aggiungi:

- `YOUTUBE_API_KEY`: La tua chiave YouTube Data API
- `KIEAI_API_KEY`: La tua chiave KIE.ai
- `OPENROUTER_API_KEY`: La tua chiave OpenRouter

## 4. Attivare i Workflow

Per ogni workflow importato:
1. Apri il workflow
2. Clicca su "Active" per attivarlo
3. Configura le credenziali per ogni nodo che richiede autenticazione
4. Clicca "Save"

## 5. Testare i Webhook

### Test YouTube Research:
```bash
curl -X POST https://n8n.plamanco.com/webhook/research \
  -H "Content-Type: application/json" \
  -d '{"query":"musica jazz","limit":5}'
```

### Test Generazione Lyrics:
```bash
curl -X POST https://n8n.plamanco.com/webhook/lyrics \
  -H "Content-Type: application/json" \
  -d '{"prompt":"roma sotto la luna","language":"IT"}'
```

## 6. Integrazione con Backend PHP

Il backend PHP su videomusic.plamanco.com è configurato per ricevere webhook da n8n tramite:
- Endpoint: `https://videomusic.plamanco.com/api/api.php?action=webhook_n8n`

I workflow n8n inviano i risultati a questo endpoint.

## 7. Monitoraggio

- **Logs n8n**: Vai su n8n.plamanco.com/executions per vedere i log
- **Backend Logs**: `/home/videomusic/logs/api.log`

## 8. Risoluzione Problemi

### Webhook non risponde:
- Verifica che il workflow sia attivo
- Controlla i log di n8n
- Verifica la connessione internet del server

### Errori API:
- Controlla le API keys nelle credenziali
- Verifica i limiti di quota delle API

### Timeout:
- Aumenta il timeout nei nodi HTTP Request
- Verifica la connessione al backend PHP

### Errore FFmpeg "command not found":
- Assicurati che FFmpeg sia installato sul server: `sudo apt install ffmpeg`
- Il workflow `5_ffmpeg_pro` utilizza un nodo Code che richiede l'eseguibile ffmpeg nel PATH
