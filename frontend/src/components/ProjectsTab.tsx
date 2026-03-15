import React, { useState, useEffect } from 'react';
import axios from 'axios';

interface Project {
  id: string;
  name: string;
  status: string;
  variants: number;
}

const ProjectsTab: React.FC = () => {
  const [projects, setProjects] = useState<Project[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchProjects();
  }, []);

  const fetchProjects = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/projects');
      setProjects(response.data.projects);
    } catch (error) {
      console.error('Fetch projects error:', error);
    } finally {
      setLoading(false);
    }
  };

  const downloadZip = (projectId: string) => {
    // Placeholder for ZIP download
    alert(`Downloading project ${projectId} as ZIP...`);
  };

  return (
    <div>
      <h2>📊 Projects</h2>
      {loading ? (
        <p>Loading projects...</p>
      ) : projects.length === 0 ? (
        <p>No projects found. Create a new music video to get started!</p>
      ) : (
        <div className="projects-grid">
          {projects.map((project) => (
            <div key={project.id} className="card">
              <h3>{project.name}</h3>
              <p>Status: {project.status}</p>
              <p>Variants: {project.variants}</p>
              <div className="project-actions">
                <button onClick={() => downloadZip(project.id)}>
                  Download ZIP
                </button>
                <button onClick={() => alert(`Viewing A/B variants for ${project.id}`)}>
                  View Variants
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default ProjectsTab;
