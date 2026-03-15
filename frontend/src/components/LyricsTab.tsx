import React, { useState } from 'react';
import axios from 'axios';

const LyricsTab: React.FC = () => {
  const [prompt, setPrompt] = useState('');
  const [lyrics, setLyrics] = useState('');
  const [language, setLanguage] = useState('IT');
  const [instrumental, setInstrumental] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleGenerate = async () => {
    setLoading(true);
    try {
      const response = await axios.post('http://localhost:8000/api/lyrics', {
        prompt,
        language,
        instrumental
      });
      setLyrics(response.data.lyrics);
    } catch (error) {
      console.error('Lyrics generation error:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <h2>✍️ Lyrics Generation</h2>
      <div className="card">
        <label>Prompt:</label>
        <textarea
          rows={3}
          placeholder="Describe the mood, theme, and style of your song..."
          value={prompt}
          onChange={(e) => setPrompt(e.target.value)}
        />
        
        <label>Language:</label>
        <select value={language} onChange={(e) => setLanguage(e.target.value)}>
          <option value="IT">Italian</option>
          <option value="EN">English</option>
          <option value="ES">Spanish</option>
        </select>
        
        <label>
          <input
            type="checkbox"
            checked={instrumental}
            onChange={(e) => setInstrumental(e.target.checked)}
          />
          Instrumental only (no lyrics)
        </label>
        
        <button onClick={handleGenerate} disabled={loading}>
          {loading ? 'Generating...' : 'Generate Lyrics'}
        </button>
      </div>
      
      {lyrics && (
        <div className="card">
          <h3>Generated Lyrics</h3>
          <pre>{lyrics}</pre>
        </div>
      )}
    </div>
  );
};

export default LyricsTab;
