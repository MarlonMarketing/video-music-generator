import React, { useState } from 'react';
import axios from 'axios';

const VideoTab: React.FC = () => {
  const [prompt, setPrompt] = useState('');
  const [duration, setDuration] = useState(10);
  const [resolution, setResolution] = useState('1080p');
  const [loading, setLoading] = useState(false);
  const [videoUrl, setVideoUrl] = useState('');

  const handleGenerate = async () => {
    setLoading(true);
    try {
      const response = await axios.post('http://localhost:8000/api/video', {
        prompt,
        duration,
        resolution
      });
      // In production, this would return a video URL
      setVideoUrl('https://example.com/video.mp4');
    } catch (error) {
      console.error('Video generation error:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <h2>🎬 Video Generation (Kling)</h2>
      <div className="card">
        <label>Cinematic Prompt:</label>
        <input
          type="text"
          placeholder="Aerial drone over Rome Colosseum golden hour..."
          value={prompt}
          onChange={(e) => setPrompt(e.target.value)}
        />
        
        <label>Duration (seconds):</label>
        <input
          type="number"
          min="5"
          max="60"
          value={duration}
          onChange={(e) => setDuration(parseInt(e.target.value))}
        />
        
        <label>Resolution:</label>
        <select value={resolution} onChange={(e) => setResolution(e.target.value)}>
          <option value="720p">720p</option>
          <option value="1080p">1080p</option>
        </select>
        
        <button onClick={handleGenerate} disabled={loading}>
          {loading ? 'Generating...' : 'Generate Video'}
        </button>
      </div>
      
      {videoUrl && (
        <div className="player-container">
          <h3>Preview</h3>
          <video controls src={videoUrl} style={{ width: '100%' }}>
            Your browser does not support the video element.
          </video>
        </div>
      )}
    </div>
  );
};

export default VideoTab;
