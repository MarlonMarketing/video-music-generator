text
# 🎵 MusicVideoForge PRO — Cloud-Only Broadcast Quality
# Copy this ENTIRE prompt to OpenCode and generate

## CONSTRAINTS (IMPORTANT)
- NO LOCAL MODELS (solo cloud APIs: OpenRouter + KIE.ai)
- QUALITY: Professional YouTube monetizable  
- SUPPORT: Vocal + Instrumental versions
- DEPLOY: Docker + GitHub private repo ready
- COST: €0.40-0.70 per complete project

## ARCHITECTURE DIAGRAM
WebApp (React) → n8n Webhooks → KIE.ai (Suno+Kling)
↓
OpenClaw + OpenRouter (free models)
↓
FFmpeg CPU post-processing

text

## REQUIRED ENV VARIABLES (.env)
OPENROUTER_API_KEY=sk-or-v1-...
KIEAI_API_KEY=kapi-... # Suno + Kling (one key)
YOUTUBE_API_KEY=AIzaSy... # Google Cloud Console
N8N_PASSWORD=your_secure_pass
SECRET_KEY=supersecretkey123

text

## OPENROUTER MODELS (FREE TOP-TIER ONLY)
RESEARCH=google/gemini-2.0-flash-exp:free
LYRICS=meta-llama/llama-3.3-70b-instruct:free
VIDEO=deepseek/deepseek-r1:free

## 4 OPENCLAW AGENTS (YAML configs)
agents/
├── 1_research_analyst.yaml
├── 2_pro_lyricist.yaml
├── 3_video_director.yaml
└── 4_style_optimizer.yaml

text

## 5 N8N WORKFLOWS (JSON import ready)
n8n_workflows/
├── 1_youtube_research.json
├── 2_pro_lyrics.json
├── 3_kie_suno_pro.json # vocal + instrumental
├── 4_kling_cinematic.json
└── 5_ffmpeg_pro.json

text

## FASTAPI + REACT WEBAPP FEATURES
UI TABS:
🔍 Research → opportunity scores + charts
✍️ Lyrics → AI edit + "Instrumental only" toggle
🎵 Music → Suno chirp-v4 preview player
🎬 Video → Kling timeline + prompt preview
📊 Projects → A/B variants + ZIP download

CONTROLS:

Language: IT/EN/ES

Instrumental: Yes/No

Quality: Fast/Professional

Resolution: 720p/1080p

text

## SUNO chirp-v4 PRO PAYLOADS
### Vocal:
{
"prompt": "professional studio lo-fi jazz, high fidelity, 78bpm C minor",
"lyrics": "[Verse]\nRoma sotto luna...",
"make_instrumental": false,
"model": "chirp-v4"
}

text
### Instrumental:
{
"prompt": "cinematic lo-fi jazz instrumental, broadcast quality, warm analog, 78bpm",
"make_instrumental": true,
"model": "chirp-v4"
}

text

## KLING 2.6 CINEMATIC PROMPTS (AI generated)
"Aerial drone over Rome Colosseum golden hour, slow cinematic pan, 4K film grain,
shallow depth of field, sync to 78bpm jazz, Oscar-winning cinematography"

text

## FFmpeg BROADCAST POST-PROCESSING
loudnorm=-16 LUFS + subtitles Roboto + H.264 CRF18 + metadata embed

text

## DOCKER COMPOSE (5 services)
backend (FastAPI)
frontend (React/nginx)
n8n (workflows)
redis (queue)
celery-ffmpeg (post-processing)

text

## GIT REPO READY
musicvideoforge-pro/
├── backend/
├── frontend/
├── n8n_workflows/ # 5x JSON
├── openclaw_agents/ # 4x YAML
├── docker-compose.yml
├── deploy.sh
├── .env.example
└── README.md

text

## DELIVERABLE REQUEST
GENERATE COMPLETE PROJECT with:
1. All source code (Python + React + TypeScript)
2. 5 n8n workflow JSON files (import-ready)
3. 4 OpenClaw agent YAML files  
4. docker-compose.yml + .env.example
5. GitHub repo structure with .gitignore
6. README.md con 5-minute deploy guide
7. deploy.sh script automatizzato

Quality: Production ready, professional output.