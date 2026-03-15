// Script per aggiornare n8n_workflows_api_v2.json con le nuove versioni dei workflow
const fs = require('fs');

const mainFile = 'n8n_workflows_api_v2.json';
const updates = [
  { name: '1_youtube_research', file: 'n8n_workflows/1_youtube_research.json' },
  { name: '2_pro_lyrics', file: 'n8n_workflows/2_pro_lyrics.json' },
  { name: '3_kie_suno_pro', file: 'n8n_workflows/3_kie_suno_pro.json' },
  { name: '4_kling_cinematic', file: 'n8n_workflows/4_kling_cinematic.json' }
  // 5_ffmpeg_pro non è incluso perché non richiede API keys dinamiche, ma è già stato aggiornato
];

let workflows = JSON.parse(fs.readFileSync(mainFile, 'utf8'));

updates.forEach(update => {
  const newWorkflow = JSON.parse(fs.readFileSync(update.file, 'utf8'));
  const index = workflows.findIndex(w => w.name === update.name);
  if (index !== -1) {
    workflows[index] = newWorkflow;
    console.log(`Updated ${update.name}`);
  } else {
    console.log(`Workflow ${update.name} not found, adding it`);
    workflows.push(newWorkflow);
  }
});

fs.writeFileSync(mainFile, JSON.stringify(workflows, null, 2));
console.log('All workflows updated successfully.');