# Guida per Principianti Assoluti

Questo è un "copia-incolla" per far funzionare tutto senza pensare.

## Cosa ti serve
1. **Un computer** (Windows, Mac o Linux)
2. **Internet**

---

## Passo 1: Installa Docker (il motore che fa funzionare tutto)

1. Vai su [www.docker.com](https://www.docker.com/products/docker-desktop/)
2. Clicca su **"Download for Windows"** (o Mac/Linux)
3. Apri il file scaricato e installalo come un normale programma
4. **IMPORTANTE**: Alla fine dell'installazione, **riavvia il computer**
5. Dopo il riavvio, vedi un'icona di balena nella barra delle applicazioni? bene!

*Se Docker chiede permessi, accetta tutto.*

---

## Passo 2: Scarica il codice

1. Apri il sito [github.com/MarlonMarketing/video-music-generator](https://github.com/MarlonMarketing/video-music-generator)
2. Clicca sul pulsante verde **"Code"**
3. Clicca su **"Download ZIP"**
4. Estrai il file ZIP sul desktop (clic destro → "Estrai tutto")

---

## Passo 3: Ottieni le chiavi API (GRATIS)

Devi registrarti su 3 siti per ottenere chiavi gratuite:

### 1. OpenRouter (per la musica)
1. Vai su [openrouter.ai](https://openrouter.ai/)
2. Clicca **"Sign Up"** e registriti con email
3. Vai su **"Keys"** nel menu
4. Clicca **"Create Key"** e copia la chiave (inizia con `sk-or-`)

### 2. KIE.ai (per musica e video)
1. Vai su [kie.ai](https://kie.ai/)
2. Clicca **"Sign Up"** e registriti
3. Vai su **"API Keys"**
4. Crea una nuova chiave e copiala (inizia con `kapi-`)

### 3. YouTube (per la ricerca)
1. Vai su [console.cloud.google.com](https://console.cloud.google.com/)
2. Accedi con account Google
3. Clicca su **"Nuovo Progetto"** e dagli un nome
4. Vai su **"API e servizi"** → **"Library"**
5. Cerca **"YouTube Data API v3"** e abilitala
6. Vai su **"Credenziali"** → **"Crea credenziali"** → **"Chiave API"**
7. Copia la chiave che appare

---

## Passo 4: Configura il file segreto

1. Apri la cartella scaricata dal passo 2
2. Trova il file chiamato `.env.example`
3. **Rinominalo** in `.env` (rimuovi `.example`)
4. Apri il file con il Blocco Note (clic destro → "Apri con" → "Blocco Note")

Ora modifica il file così:

```env
OPENROUTER_API_KEY=incolla-qui-la-tua-chiave-openrouter
KIEAI_API_KEY=incolla-qui-la-tua-chiave-kie
YOUTUBE_API_KEY=incolla-qui-la-tua-chiave-youtube

N8N_PASSWORD=tua_password_qui
SECRET_KEY=super_secret_qui
```

**Sostituisci** "incolla-qui..." con le tue chiavi ottenute al passo 3.

**Scegli una password** per N8N (qualunque cosa, es: "miosicuro123")

Salva il file (CTRL+S) e chiudi.

---

## Passo 5: Avvia tutto con un solo click

1. Torna nella cartella principale del progetto
2. Trova il file `deploy.sh`
3. **Cliccalo due volte** per eseguirlo

*Se Windows chiede permessi, clicca "Sì"*

**Oppure** se non funziona:
1. Apri il **Prompt dei comandi** (cerca "cmd" nel menu Start)
2. Scrivi: `cd` e spazio, poi trascina la cartella del progetto nella finestra nera
3. Premi Invio
4. Scrivi: `docker-compose up --build -d`
5. Premi Invio e aspetta (ci vorranno alcuni minuti)

---

## Passo 6: Usa il programma!

Una volta finito (vedrai scritte verdi o messaggi di "Done"):

1. Apri il browser (Chrome, Firefox, Edge)
2. Vai su: **http://localhost:3000**

**Eccolo!** Il programma è pronto.

### Come usarlo:

- **🔍 Research**: Cerca canali YouTube per vedere opportunità
- **✍️ Lyrics**: Scrivi una descrizione e crea testi canzoni
- **🎵 Music**: Crea musica con Suno
- **🎬 Video**: Crea video cinematici con Kling
- **📊 Projects**: Gestisci i tuoi progetti salvati

### Accesso n8n (opzionale):
Se vuoi vedere i flussi di lavoro:
- Vai su: **http://localhost:5678**
- Username: `admin`
- Password: quella che hai scritto nel file `.env`

---

## Problemi? Ecco le soluzioni:

### "Docker non si apre"
- Riavvia il computer
- Assicurati che l'icona della balena sia visibile nella barra delle applicazioni

### "Errore nel codice"
- Chiudi tutto e riapri
- Assicurati di aver seguito tutti i passi

### "Non funziona ancora"
- Premi **CTRL+C** nella finestra nera (se aperta)
- Scrivi: `docker-compose down`
- Poi: `docker-compose up --build -d`

---

## Ricorda:
- **Non chiudere** la finestra nera quando il programma è in esecuzione
- Se spegni il computer, riavvia tutto con il file `deploy.sh`
- Le tue chiavi API sono segrete, non condividerle!

**Tutto dovrebbe funzionare ora! Buon divertimento!** 🎵🎬
