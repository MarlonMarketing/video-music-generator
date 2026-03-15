# MusicVideoForge PRO

Cloud-Only Broadcast Quality Music Video Generation

## Architecture
WebApp (React) → n8n Webhooks → KIE.ai (Suno+Kling) → OpenRouter → FFmpeg CPU post-processing

## Quick Deploy (5 min)

1. **Clone repo**
   ```bash
   git clone <repo-url>
   cd video-music-generator
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your API keys
   ```

3. **Start services**
   ```bash
   docker-compose up --build
   ```

4. **Access services**
   - Frontend: http://localhost:3000
   - Backend: http://localhost:8000
   - n8n: http://localhost:5678 (admin / your N8N_PASSWORD)

## Features
- 🔍 Research: YouTube opportunity analysis
- ✍️ Lyrics: AI-generated lyrics with instrumental toggle
- 🎵 Music: Suno chirp-v4 preview player
- 🎬 Video: Kling cinematic timeline
- 📊 Projects: A/B variants & ZIP download

## API Keys Required
- OpenRouter (free models)
- KIE.ai (Suno + Kling)
- YouTube Data API
