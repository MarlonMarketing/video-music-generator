import React, { useState } from 'react';
import axios from 'axios';

const MusicTab: React.FC = () => {
  const [prompt, setPrompt] = useState('');
  const [lyrics, setLyrics] = useState('');
  const [makeInstrumental, setMakeInstrumental] = useState(false);
  const [loading, setLoading] = useState(false);
  const [trackUrl, setTrackUrl] = useState('');

  const handleGenerate = async () => {
    setLoading(true);
    try {
      const response = await axios.post('http://localhost:8000/api/music', {
        prompt,
        lyrics,
        make_instrumental: makeInstrumental,
        model: 'chirp-v4'
      });
      // In production, this would return a track URL
      setTrackUrl('https://example.com/track.mp3');
    } catch (error) {
      console.error('Music generation error:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <h2>🎵 Music Generation (Suno chirp-v4)</h2>
      <div className="card">
        <label>Style Prompt:</label>
        <input
          type="text"
          placeholder="professional studio lo-fi jazz, high fidelity, 78bpm C minor"
          value={prompt}
          onChange={(e) => setPrompt(e.target.value)}
        />
        
        <label>Lyrics (optional):</label>
        <textarea
          rows={3}
          placeholder="Paste lyrics here..."
          value={lyrics}
          onChange={(e) => setLyrics(e.target.value)}
        />
        
        <label>
          <input
            type="checkbox"
            checked={makeInstrumental}
            onChange={(e) => setMakeInstrumental(e.target.checked)}
          />
          Generate Instrumental Only
        </label>
        
        <button onClick={handleGenerate} disabled={loading}>
          {loading ? 'Generating...' : 'Generate Music'}
        </button>
      </div>
      
      {trackUrl && (
        <div className="player-container">
          <h3>Preview</h3>
          <audio controls src={trackUrl} style={{ width: '100%' }}>
            Your browser does not support the audio element.
          </audio>
        </div>
      )}
    </div>
  );
};

export default MusicTab;
