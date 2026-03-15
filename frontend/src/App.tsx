import React, { useState } from 'react';
import './App.css';
import ResearchTab from './components/ResearchTab';
import LyricsTab from './components/LyricsTab';
import MusicTab from './components/MusicTab';
import VideoTab from './components/VideoTab';
import ProjectsTab from './components/ProjectsTab';

type TabType = 'research' | 'lyrics' | 'music' | 'video' | 'projects';

function App() {
  const [activeTab, setActiveTab] = useState<TabType>('research');

  const renderTab = () => {
    switch (activeTab) {
      case 'research':
        return <ResearchTab />;
      case 'lyrics':
        return <LyricsTab />;
      case 'music':
        return <MusicTab />;
      case 'video':
        return <VideoTab />;
      case 'projects':
        return <ProjectsTab />;
      default:
        return <ResearchTab />;
    }
  };

  return (
    <div className="app-container">
      <h1>🎵 MusicVideoForge PRO</h1>
      <div className="tabs">
        <button
          className={`tab ${activeTab === 'research' ? 'active' : ''}`}
          onClick={() => setActiveTab('research')}
        >
          🔍 Research
        </button>
        <button
          className={`tab ${activeTab === 'lyrics' ? 'active' : ''}`}
          onClick={() => setActiveTab('lyrics')}
        >
          ✍️ Lyrics
        </button>
        <button
          className={`tab ${activeTab === 'music' ? 'active' : ''}`}
          onClick={() => setActiveTab('music')}
        >
          🎵 Music
        </button>
        <button
          className={`tab ${activeTab === 'video' ? 'active' : ''}`}
          onClick={() => setActiveTab('video')}
        >
          🎬 Video
        </button>
        <button
          className={`tab ${activeTab === 'projects' ? 'active' : ''}`}
          onClick={() => setActiveTab('projects')}
        >
          📊 Projects
        </button>
      </div>
      <div className="tab-content">
        {renderTab()}
      </div>
    </div>
  );
}

export default App;
