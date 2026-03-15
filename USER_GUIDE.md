# Manuale d'Uso - Video Music Generator

## 🎬 Introduzione
Video Music Generator è una piattaforma che ti permette di:
- Ricercare canali YouTube con alto potenziale di crescita
- Generare testi per canzoni con AI
- Creare musica strumentale con Suno
- Generare video cinematografici con Kling
- Gestire progetti e varianti

Tutto automatizzato tramite workflow n8n.

---

## 🚀 Come Iniziare

### 1. Accedi al Frontend
Vai su: **https://videomusic.plamanco.com**

Vedrai la dashboard principale con 4 sezioni principali:
- **Research** (🔍 Ricerca YouTube)
- **Lyrics** (✍️ Testi)
- **Music** (🎵 Musica)
- **Video** (🎬 Video)
- **Projects** (📊 Progetti)

---

## 📊 Sezione "Research" (Ricerca YouTube)

### Funzionalità
Cerca canali YouTube in base a una parola chiave (es. "jazz", "rock", "electronic") e ottieni una lista di canali con un punteggio di opportunità (70-100).

### Come usare:
1. Vai alla scheda **Research**
2. Inserisci una **Query** (es. "indie folk")
3. Scegli il **Numero di risultati** (default: 5)
4. Clicca **Cerca**
5. Vedi la lista dei canali con:
   - Nome canale
   - Punteggio di opportunità (70-100, più alto = migliore)
6. Clicca su un canale per salvare il progetto o **"Crea Progetto"** per generare una variante

---

## ✍️ Sezione "Lyrics" (Generazione Testi)

### Funzionalità
Genera testi di canzoni in diverse lingue usando AI (OpenRouter Llama 3.3 70B).

### Come usare:
1. Vai alla scheda **Lyrics**
2. Inserisci il **Prompt** (descrizione del tema della canzone)
   - Es: `"love under the rain in Tokyo"`
3. Seleziona la **Lingua** (IT, EN, ES, FR, DE, PT)
4. (Opzionale) Attiva **Strumentale** se vuoi solo la musica senza voce
5. Clicca **Genera Testo**
6. attendi qualche secondo e vedrai il testo completo
7. Puoi **Copiare** il testo o **Salvare nel Progetto**

---

## 🎵 Sezione "Music" (Musica con Suno)

### Funzionalità
Genera un brano musicale a partire da prompt e testo (opzionale) usando Suno via KIE.ai.

### Come usare:
1. Vai alla scheda **Music**
2. Inserisci il **Prompt** (descrizione dello stile musicale)
   - Es: `"upbeat pop rock with electric guitar"`
3. (Opzionale) Inserisci i **Testi** già generati nella sezione Lyrics
4. Seleziona **Modello** (solo `suno-v3.0` per ora)
5. Attiva **Strumentale** se vuoi solo musica senza voce
6. Clicca **Genera Musica**
7. Il sistema invia la richiesta a Suno e salva il `track_id`
8. Lo stato diventa **"generating"** e poi **"completed"**
9. Potrai ascoltare l'anteprima tramite il player integrato

---

## 🎬 Sezione "Video" (Video con Kling)

### Funzionalità
Genera video cinematografici a partire da un prompt testuale usando Kling AI.

### Come usare:
1. Vai alla scheda **Video**
2. Inserisci il **Prompt** (descrizione della scena)
   - Es: `"a sunset over the ocean with waves and seagulls"`
3. Scegli la **Durata** (10s, 15s, 30s)
4. Scegli la **Risoluzione** (`1080p`, `720p`, `480p`)
5. Clicca **Genera Video**
6. Il sistema invia la richiesta a Kling e salva il `video_id`
7. Lo stato diventa **"generating"** e poi **"completed"**
8. Potrai scaricare il video o vederlo in anteprima

---

## 📊 Sezione "Projects" (Gestione Progetti)

### Funzionalità
Visualizza tutti i progetti creati, con varianti A/B per A/B testing.

### Come usare:
1. Vai alla scheda **Projects**
2. Vedi la lista dei progetti:
   - Nome progetto
   - Data creazione
   - Stato (`draft`, `generating`, `completed`, `failed`)
3. Clicca su un progetto per vedere i **dettagli**:
   - Testi generati
   - Musica (track_id)
   - Video (video_id)
   - File ZIP con tutti i materiali
4. Puoi **eliminare** un progetto o **rigenerare** elementi

---

## 🔄 Flusso Completo di Creazione Contenuto

1. **Ricerca** → Trova un argomento YouTube con opportunità
2. **Lyrics** → Genera testi basati sul tema
3. **Music** → Crea musica con i testi (o strumentale)
4. **Video** → Genera video che accompagna la musica
5. **Projects** → Raccogli tutto in un progetto e scarica lo ZIP

---

## ⚙️ API Endpoints (per sviluppatori)

Il backend PHP espone diverse API:

### Health Check
```
GET /api/api.php?action=health
```
Risposta: `{"status":"healthy","database":true,"message":"Database connected"}`

### Lista Progetti
```
GET /api/api.php?action=list_projects
```

### Crea Progetto
```
POST /api/api.php?action=create_project
Body: {"name":"Nome Progetto"}
```

### Ricerca YouTube
```
POST /api/api.php?action=research
Body: {"query":"jazz","limit":5}
```

### Genera Lyrics
```
POST /api/api.php?action=generate_lyrics
Body: {"prompt":"testo canzone","language":"IT","instrumental":false}
```

### Genera Musica
```
POST /api/api.php?action=generate_music
Body: {"prompt":"stile musicale","lyrics":"testo","make_instrumental":false}
```

### Genera Video
```
POST /api/api.php?action=generate_video
Body: {"prompt":"descrizione","duration":10,"resolution":"1080p"}
```

### Webhook n8n (da n8n al backend)
```
POST /api/api.php?action=webhook_n8n
Body: {"event":"event_name","data":{...}}
```

---

## 🔐 Autenticazione Utenti

Il sistema supporta registrazione e login via JWT.

### Registrazione
```bash
curl -X POST "https://videomusic.plamanco.com/api/api.php?action=register" \
  -H "Content-Type: application/json" \
  -d '{"username":"mario","email":"mario@example.com","password":"password123"}'
```

### Login
```bash
curl -X POST "https://videomusic.plamanco.com/api/api.php?action=login" \
  -H "Content-Type: application/json" \
  -d '{"email":"mario@example.com","password":"password123"}'
```

Usa il token JWT ricevuto nelle chiamate successive aggiungendo l'header:
```
Authorization: Bearer <token>
```

---

## ⚡ Workflow n8n

I workflow automatizzati sono:

1. **1_youtube_research**: Riceve webhook, chiama YouTube API, calcola punteggio opportunità → backend
2. **2_pro_lyrics**: Riceve prompt, chiama OpenRouter per generare testi → backend
3. **3_kie_suno_pro**: Riceve prompt e testi, chiama KIE.ai Suno per musica → backend
4. **4_kling_cinematic**: Riceve prompt, durata, risoluzione, chiama KIE.ai Kling per video → backend
5. **5_ffmpeg_pro**: Riceve percorso file, applica post-processing FFmpeg (non ancora implementato)

**Trigger webhook:**
- YouTube: `POST /webhook/research` con `{query, limit}`
- Lyrics: `POST /webhook/lyrics` con `{prompt, language, instrumental}`
- Music: `POST /webhook/music` con `{prompt, lyrics, make_instrumental, model}`
- Video: `POST /webhook/video` con `{prompt, duration, resolution}`

---

## 🔧 Manutenzione

### Visualizza Log
- Log API: `/var/log/apache2/error.log` o il file configurato in `config.php` (`logs/api.log`)
- Log n8n: https://n8n.plamanco.com/executions

### Ricarica configurazione
Dopo aver modificato `config.php`, riavvia Apache:
```bash
sudo systemctl reload apache2
```

### Aggiorna API Keys
Usa l'endpoint `save_api_keys` (vedi sopra) per aggiornare le chiavi senza riavviare.

---

## 🐛 Troubleshooting Comune

| Problema | Soluzione |
|----------|-----------|
| Frontend non carica | Verifica che `.htaccess` sia in `/public_html` e che `mod_rewrite` sia abilitato |
| API restituisce errore DB | Controlla le credenziali in `config.php` e che il MySQL sia attivo |
| n8n non attiva webhook | Verifica che il workflow sia attivo (toggle verde) e che le API keys siano presenti |
| Generazione lenta | Suno e Kling possono impiegare 30-60 secondi; controlla lo stato in **Projects** |
| FFmpeg fallisce | Installa FFmpeg: `sudo apt install ffmpeg` e verifica che sia nel PATH |

---

## 📞 Supporto
Per problemi tecnici o domande, contatta lo sviluppatore o apri un issue su GitHub: https://github.com/MarlonMarketing/video-music-generator

---

**Versione**: 1.0.4 | **Data**: Marzo 2026