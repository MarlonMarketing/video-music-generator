#!/bin/bash

# MusicVideoForge PRO Deploy Script

set -e

echo "🚀 Starting MusicVideoForge PRO deployment..."

# Check if .env exists
if [ ! -f .env ]; then
    echo "⚠️  .env file not found. Copying from .env.example..."
    cp .env.example .env
    echo "⚠️  Please edit .env with your API keys before proceeding."
    exit 1
fi

# Build and start services
echo "📦 Building and starting Docker containers..."
docker-compose up --build -d

echo "✅ Deployment complete!"
echo ""
echo "Access points:"
echo "  Frontend: http://localhost:3000"
echo "  Backend:  http://localhost:8000"
echo "  n8n:      http://localhost:5678"
echo ""
echo "To view logs: docker-compose logs -f"
