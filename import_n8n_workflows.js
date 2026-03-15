// Script per importare workflow in n8n tramite API
const axios = require('axios');
const fs = require('fs');

const API_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhYjg2ZDlhNy1hOWMyLTQzZjMtOThhNC1jNmJmYjQzOTA0YTEiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYTZiYjkzOTYtZWYxMC00NDEyLTgzNDYtZWRiZjYwMzQxZTMxIiwiaWF0IjoxNzczNTg4MDcxfQ.zrHQGickMmZcdchxgtvr917rzJ7aYpee8VnmgnTGsQ8';
const N8N_URL = 'https://n8n.plamanco.com';

async function importWorkflow(workflowData) {
  try {
    const response = await axios.post(
      `${N8N_URL}/api/v1/workflows`,
      workflowData,
      {
        headers: {
          'X-N8N-API-KEY': API_KEY,
          'Content-Type': 'application/json'
        }
      }
    );
    console.log(`✅ Workflow "${workflowData.name}" importato con successo! ID: ${response.data.id}`);
    return response.data;
  } catch (error) {
    console.error(`❌ Errore importando workflow "${workflowData.name}":`);
    if (error.response) {
      console.error(`Status: ${error.response.status}`);
      console.error(`Data: ${JSON.stringify(error.response.data, null, 2)}`);
    } else {
      console.error(error.message);
    }
    return null;
  }
}

async function main() {
  const args = process.argv.slice(2);
  const inputFile = args[0] || 'n8n_workflows_api_v2.json';
  const workflowsData = JSON.parse(fs.readFileSync(inputFile, 'utf8'));

  console.log('🚀 Inizio importazione workflow in n8n...\n');

  for (const workflow of workflowsData) {
    await importWorkflow(workflow);
    // Pausa di 1 secondo tra le richieste
    await new Promise(resolve => setTimeout(resolve, 1000));
  }

  console.log('\n✅ Importazione completata!');
}

main().catch(console.error);
