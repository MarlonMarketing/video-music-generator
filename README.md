# MusicVideoForge PRO

Generazione di Video Musicali di Qualità Broadcast su Architettura LAMP.

## Architettura

- **Frontend**: App React statica servita da Apache.
- **Backend**: API PHP (eseguite via Apache/mod_php).
- **Database**: MySQL (gestito da Virtualmin).
- **Automazione**: n8n (Docker) per i workflow AI (YouTube Research, Lyrics, Musica, Video, FFmpeg).

## Installazione

Per istruzioni dettagliate sull'installazione su server Linux (Oracle Cloud ARM) con stack LAMP e Virtualmin, consulta il file [install.md](install.md).

## Quick Start (Setup LAMP)

1.  **Configura Database MySQL** in Virtualmin.
2.  **Crea Virtual Host** per il dominio in Apache.
3.  **Deploy Frontend**: Carica i file statici React in `/home/videomusic/public_html/`.
4.  **Deploy Backend**: Carica i file PHP in `/home/videomusic/public_html/api/`.
5.  **Installa n8n** via Docker e configura il reverse proxy Apache.
6.  **Importa Workflow** n8n usando lo script `import_n8n_workflows.js`.
7.  **Configura Variabili d'Ambiente** in n8n e nel backend PHP.

## Funzionalità

-   🔍 **Research**: Analisi opportunità YouTube.
-   ✍️ **Lyrics**: Testi canzoni generati da AI (OpenRouter).
-   🎵 **Music**: Generazione musica Suno via KIE.ai.
-   🎬 **Video**: Generazione video cinematici Kling via KIE.ai.
-   📊 **Projects**: Gestione progetti e download ZIP.

## API Keys Richieste

-   **OpenRouter** (per lyrics e modelli AI).
-   **KIE.ai** (per musica Suno e video Kling).
-   **YouTube Data API** (per ricerca canali).

## Repository Structure

-   `frontend/`: Sorgente React.
-   `backend_php/`: Sorgente PHP.
-   `n8n_workflows/`: Definizione workflow n8n.
-   `import_n8n_workflows.js`: Script per importare workflow in n8n.
-   `install.md`: Istruzioni installazione LAMP.
-   `n8n_setup.md`: Istruzioni configurazione n8n.

## License

MIT