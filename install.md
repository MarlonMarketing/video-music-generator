# Installazione Video Music Generator (LAMP Stack)

Questo documento guida all'installazione di "Video Music Generator" su un server Linux (Oracle Cloud ARM) utilizzando uno stack LAMP (Linux, Apache, MySQL, PHP) gestito tramite Virtualmin.

## Architettura

- **Frontend**: App React statica (buildata) servita da Apache.
- **Backend**: API PHP (eseguita via Apache/mod_php).
- **Database**: MySQL (gestito da Virtualmin).
- **Automazione**: n8n (installato tramite Docker, accessibile tramite sottodominio).
- **Web Server**: Apache 2.4.

## Prerequisiti

1.  Server Oracle Cloud ARM (Ubuntu 24.04 LTS).
2.  Virtualmin installato e configurato.
3.  Accesso SSH al server.
4.  Dominio puntato al server (es. `videomusic.plamanco.com`).

---

## Step 1: Configurazione Database MySQL

1.  Accedi a Virtualmin -> System Settings -> Database Servers.
2.  Crea un nuovo database MySQL (es. `videomusic`).
3.  Crea un utente database (es. `admin@videomusic`) con password robusta.
4.  Importa lo schema del database (se presente in `database/schema.sql`).

**Credenziali di esempio (da cambiare in produzione):**
-   Database: `videomusic`
-   Utente: `admin@videomusic`
-   Password: `Plamanco_2026`

---

## Step 2: Configurazione Virtual Host (Sito Web)

1.  In Virtualmin, crea un nuovo Virtual Server per il dominio (es. `videomusic.plamanco.com`).
2.  Abilita l'opzione "Execute CGI" e "PHP scripting".
3.  Configura il document root a `/home/videomusic/public_html`.

---

## Step 3: Deploy Frontend React

Il frontend è un'applicazione React statica.

1.  **Build locale** (dal tuo computer):
    ```bash
    cd frontend
    npm run build
    ```
    Questo genera i file statici nella cartella `build`.

2.  **Upload via FTP/SFTP**:
    Carica il contenuto della cartella `build` nella directory `/home/videomusic/public_html/` sul server.

3.  **Configura Apache per SPA (Single Page Application)**:
    Crea o modifica il file `/home/videomusic/public_html/.htaccess`:
    ```apache
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.html [L]
    ```

---

## Step 4: Deploy Backend PHP

1.  Crea una sottocartella `api` in `/home/videomusic/public_html/`.
2.  Carica i file PHP dalla cartella `backend_php/` (in particolare `api.php` e `config.php`) in `/home/videomusic/public_html/api/`.
3.  **Configura le credenziali**:
    Modifica il file `/home/videomusic/public_html/api/config.php` con le tue credenziali database e le API keys:
    ```php
    // Database
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'videomusic');
    define('DB_USER', 'admin@videomusic');
    define('DB_PASS', 'Plamanco_2026');

    // API Keys (impostale anche come variabili d'ambiente in n8n)
    define('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY') ?: 'tua_chiave_openrouter');
    define('KIEAI_API_KEY', getenv('KIEAI_API_KEY') ?: 'tua_chiave_kieai');
    define('YOUTUBE_API_KEY', getenv('YOUTUBE_API_KEY') ?: 'tua_chiave_youtube');
    ```

---

## Step 5: Installazione e Configurazione n8n

n8n è l'orchestratore dei workflow AI.

1.  **Installazione Docker** (consigliata su Oracle Cloud ARM):
    ```bash
    # Installa Docker
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh

    # Avvia n8n con variabili d'ambiente
    docker run -d \
        --name n8n \
        -p 5678:5678 \
        -v ~/.n8n:/home/node/.n8n \
        -e N8N_BASIC_AUTH_ACTIVE=true \
        -e N8N_BASIC_AUTH_USER=admin \
        -e N8N_BASIC_AUTH_PASSWORD=Plamanco_2026 \
        -e N8N_ENCRYPTION_KEY=tua_chiave_criptazione_unica \
        -e TZ=Europe/Rome \
        docker.n8n.io/n8nio/n8n
    ```

2.  **Configura il reverse proxy in Apache** (per accesso tramite sottodominio es. `n8n.plamanco.com`):
    Assicurati che il modulo `mod_proxy` sia abilitato (`a2enmod proxy proxy_http`).
    Crea un nuovo Virtual Host in Virtualmin per il sottodominio `n8n.plamanco.com` e aggiungi queste direttive:
    ```apache
    <VirtualHost *:80>
        ServerName n8n.plamanco.com
        ProxyPass / http://localhost:5678/
        ProxyPassReverse / http://localhost:5678/
    </VirtualHost>
    ```
    *Nota: Per HTTPS, configura SSL con Let's Encrypt in Virtualmin.*

3.  **Importare i Workflow**:
    Usa lo script `import_n8n_workflows.js` presente nel repository per importare i workflow definiti in `n8n_workflows/`.
    ```bash
    # sul server (dopo aver clonato il repo)
    cd /path/to/video-music-generator
    npm install axios
    node import_n8n_workflows.js
    ```
    *Nota: Assicurati di avere la variabile d'ambiente `N8N_API_KEY` impostata con la chiave API di n8n (visibile in n8n -> Settings -> API).*

    **Nota Importante su FFmpeg**:
    Il workflow `5_ffmpeg_pro` utilizza un nodo "Code" (n8n-nodes-base.code) invece di "Execute Command" a causa delle limitazioni di sicurezza dell'ambiente LAMP. Questo nodo esegue FFmpeg tramite `child_process.execFileSync`. Assicurati che FFmpeg sia installato sul server (`sudo apt install ffmpeg`).

---

## Step 6: Variabili d'Ambiente n8n

Accedi a n8n -> Settings -> Variables e aggiungi le seguenti variabili (valori corrispondono a quelli in `config.php`):
-   `YOUTUBE_API_KEY`: La tua chiave YouTube Data API
-   `KIEAI_API_KEY`: La tua chiave KIE.ai
-   `OPENROUTER_API_KEY`: La tua chiave OpenRouter

---

## Step 7: Verifica Finale

1.  Accedi al frontend tramite browser (`https://videomusic.plamanco.com`).
2.  Crea un nuovo progetto e verifica che la richiesta venga processata correttamente.
3.  Controlla i log di n8n (n8n.plamanco.com/executions) per verificare l'esecuzione dei workflow.
4.  Controlla i log di Apache in `/var/log/apache2/error.log` per eventuali errori PHP.

---

## File Struttura del Repository

-   `frontend/`: Codice sorgente React (statico dopo build).
-   `backend_php/`: Codice sorgente PHP per le API.
-   `n8n_workflows/`: Definizione JSON dei workflow n8n.
-   `import_n8n_workflows.js`: Script per importare i workflow in n8n.
-   `install.md`: Questo file.
-   `n8n_setup.md`: Istruzioni dettagliate per configurare n8n (import workflow, credenziali, ecc.).

## Troubleshooting

-   **Errori PHP "Connection refused"**: Verifica le credenziali database in `config.php`.
-   **Workflow n8n non si attivano**: Verifica che le variabili d'ambiente siano corrette e che le API keys siano valide.
-   **Frontend non carica**: Verifica che Apache serva correttamente la cartella `public_html` e che il file `.htaccess` sia presente.
-   **n8n non raggiungibile**: Verifica il reverse proxy in Apache e che la porta 5678 sia aperta sul server.