#!/usr/bin/env node

const http = require('http');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: '/var/www/automatehub/.env' });

let pool = null;

async function connectDB() {
  if (!pool) {
    pool = mysql.createPool({
      host: process.env.DB_HOST || 'localhost',
      port: parseInt(process.env.DB_PORT || '3306', 10),
      user: process.env.DB_USERNAME || 'root',
      password: process.env.DB_PASSWORD || '',
      database: process.env.DB_DATABASE || 'test',
      waitForConnections: true,
      connectionLimit: 10,
    });
  }
  return pool;
}

const server = http.createServer(async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Content-Type', 'application/json');
  
  if (req.method === 'GET' && req.url === '/health') {
    res.writeHead(200);
    res.end(JSON.stringify({ status: 'ok' }));
    return;
  }
  
  if (req.method === 'POST' && req.url === '/mcp') {
    let body = '';
    req.on('data', chunk => body += chunk);
    req.on('end', async () => {
      try {
        const request = JSON.parse(body);
        const { id, method, params } = request;
        
        let response = { jsonrpc: '2.0', id };
        
        switch (method) {
          case 'initialize':
            response.result = {
              protocolVersion: '2024-11-05',
              capabilities: { tools: {} },
              serverInfo: { name: 'mysql-laravel-http', version: '1.0.0' }
            };
            break;
            
          case 'tools/list':
            response.result = {
              tools: [
                {
                  name: 'query',
                  description: 'Execute SQL query',
                  inputSchema: {
                    type: 'object',
                    properties: { sql: { type: 'string' } },
                    required: ['sql']
                  }
                }
              ]
            };
            break;
            
          case 'tools/call':
            const db = await connectDB();
            const [rows] = await db.query(params.arguments.sql);
            response.result = {
              content: [{ type: 'text', text: JSON.stringify(rows, null, 2) }]
            };
            break;
            
          default:
            response.error = { code: -32601, message: 'Method not found' };
        }
        
        res.writeHead(200);
        res.end(JSON.stringify(response));
      } catch (error) {
        res.writeHead(500);
        res.end(JSON.stringify({
          jsonrpc: '2.0',
          id: null,
          error: { code: -32603, message: error.message }
        }));
      }
    });
  } else {
    res.writeHead(404);
    res.end('Not Found');
  }
});

const PORT = 3001;
server.listen(PORT, () => {
  console.log(`MySQL MCP HTTP server running on port ${PORT}`);
});
