from fastapi import FastAPI, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import redis
import json
import os
from dotenv import load_dotenv

load_dotenv()

app = FastAPI(title="MusicVideoForge PRO Backend")

# CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Redis connection
redis_client = redis.Redis(
    host=os.getenv("REDIS_HOST", "localhost"),
    port=int(os.getenv("REDIS_PORT", 6379)),
    decode_responses=True
)

# Models
class ResearchRequest(BaseModel):
    query: str
    limit: int = 10

class LyricsRequest(BaseModel):
    prompt: str
    language: str = "IT"
    instrumental: bool = False

class MusicRequest(BaseModel):
    prompt: str
    lyrics: str = ""
    make_instrumental: bool = False
    model: str = "chirp-v4"

class VideoRequest(BaseModel):
    prompt: str
    duration: int = 10
    resolution: str = "1080p"

class WebhookRequest(BaseModel):
    event: str
    data: dict

@app.get("/")
async def root():
    return {"message": "MusicVideoForge PRO Backend API"}

@app.post("/webhook/n8n")
async def webhook_n8n(request: WebhookRequest):
    """Receive webhooks from n8n workflows"""
    print(f"Webhook received: {request.event}")
    # Store in Redis for frontend polling
    redis_client.setex(f"webhook:{request.event}", 3600, json.dumps(request.data))
    return {"status": "received"}

@app.post("/api/research")
async def research_youtube(request: ResearchRequest):
    """Trigger YouTube research workflow"""
    # In production, this would call n8n webhook
    # For demo, return mock data
    return {
        "status": "queued",
        "query": request.query,
        "results": [
            {"channel": "Demo Channel 1", "opportunity_score": 85},
            {"channel": "Demo Channel 2", "opportunity_score": 72}
        ]
    }

@app.post("/api/lyrics")
async def generate_lyrics(request: LyricsRequest):
    """Generate lyrics via OpenRouter"""
    # Placeholder - integrate with n8n workflow
    return {
        "status": "generated",
        "lyrics": "[Verse]\nRoma sotto luna...\n[Chorus]\n...",
        "language": request.language,
        "instrumental": request.instrumental
    }

@app.post("/api/music")
async def generate_music(request: MusicRequest):
    """Generate music via Suno API (KIE.ai)"""
    # Placeholder - integrate with n8n workflow
    return {
        "status": "generating",
        "track_id": "suno_12345",
        "prompt": request.prompt,
        "make_instrumental": request.make_instrumental
    }

@app.post("/api/video")
async def generate_video(request: VideoRequest):
    """Generate video via Kling API"""
    # Placeholder - integrate with n8n workflow
    return {
        "status": "generating",
        "video_id": "kling_67890",
        "prompt": request.prompt,
        "duration": request.duration,
        "resolution": request.resolution
    }

@app.get("/api/projects")
async def list_projects():
    """List all projects"""
    # Mock data
    return {
        "projects": [
            {
                "id": "proj_1",
                "name": "Roma Jazz Night",
                "status": "completed",
                "variants": 2
            }
        ]
    }

@app.get("/api/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "redis": redis_client.ping() if redis_client else False
    }
