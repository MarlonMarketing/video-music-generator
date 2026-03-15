import React, { useState } from 'react';
import axios from 'axios';

interface ResearchResult {
  channel: string;
  opportunity_score: number;
}

const ResearchTab: React.FC = () => {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState<ResearchResult[]>([]);
  const [loading, setLoading] = useState(false);

  const handleResearch = async () => {
    setLoading(true);
    try {
      const response = await axios.post('http://localhost:8000/api/research', {
        query,
        limit: 10
      });
      setResults(response.data.results);
    } catch (error) {
      console.error('Research error:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <h2>🔍 YouTube Research</h2>
      <div className="card">
        <input
          type="text"
          placeholder="Enter YouTube channel/topic to research..."
          value={query}
          onChange={(e) => setQuery(e.target.value)}
        />
        <button onClick={handleResearch} disabled={loading}>
          {loading ? 'Researching...' : 'Start Research'}
        </button>
      </div>
      
      {results.length > 0 && (
        <div className="results">
          <h3>Results</h3>
          {results.map((result, index) => (
            <div key={index} className="card">
              <strong>{result.channel}</strong>
              <p>Opportunity Score: {result.opportunity_score}/100</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default ResearchTab;
