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
    
    // Test connection
    const connection = await pool.getConnection();
    connection.release();
    console.log('✅ Database connected successfully');
  }
  return pool;
}

const server = http.createServer(async (req, res) => {
  // CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  
  if (req.method === 'OPTIONS') {
    res.writeHead(200);
    res.end();
    return;
  }
  
  if (req.method === 'GET' && req.url === '/health') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ status: 'ok', timestamp: new Date().toISOString() }));
    return;
  }
  
  if (req.method === 'POST' && req.url === '/sse') {
    res.writeHead(200, {
      'Content-Type': 'text/event-stream',
      'Cache-Control': 'no-cache',
      'Connection': 'keep-alive',
      'Access-Control-Allow-Origin': '*'
    });
    
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
              serverInfo: { 
                name: 'mysql-laravel-sse', 
                version: '1.0.0',
                description: 'Laravel MySQL MCP Server'
              }
            };
            break;
            
          case 'tools/list':
            response.result = {
              tools: [
                {
                  name: 'connect_db',
                  description: 'Test database connection',
                  inputSchema: { type: 'object', properties: {} }
                },
                {
                  name: 'query',
                  description: 'Execute SQL SELECT query',
                  inputSchema: {
                    type: 'object',
                    properties: {
                      sql: { type: 'string', description: 'SQL query to execute' }
                    },
                    required: ['sql']
                  }
                },
                {
                  name: 'list_tables',
                  description: 'List all database tables',
                  inputSchema: { type: 'object', properties: {} }
                }
              ]
            };
            break;
            
          case 'tools/call':
            const toolName = params?.name;
            const args = params?.arguments || {};
            
            switch (toolName) {
              case 'connect_db':
                await connectDB();
                response.result = {
                  content: [{
                    type: 'text',
                    text: `✅ Connected to Laravel database: ${process.env.DB_DATABASE}`
                  }]
                };
                break;
                
              case 'query':
                if (!args.sql) {
                  throw new Error('SQL query is required');
                }
                const db = await connectDB();
                const [rows] = await db.query(args.sql);
                response.result = {
                  content: [{
                    type: 'text',
                    text: JSON.stringify(rows, null, 2)
                  }]
                };
                break;
                
              case 'list_tables':
                const dbList = await connectDB();
                const [tables] = await dbList.query('SHOW TABLES');
                response.result = {
                  content: [{
                    type: 'text',
                    text: JSON.stringify(tables, null, 2)
                  }]
                };
                break;
                
              default:
                response.error = { code: -32601, message: `Unknown tool: ${toolName}` };
            }
            break;
            
          default:
            response.error = { code: -32601, message: `Method not found: ${method}` };
        }
        
        res.write(`data: ${JSON.stringify(response)}\n\n`);
        res.end();
        
      } catch (error) {
        console.error('Error:', error);
        const errorResponse = {
          jsonrpc: '2.0',
          id: null,
          error: { code: -32603, message: error.message }
        };
        res.write(`data: ${JSON.stringify(errorResponse)}\n\n`);
        res.end();
      }
    });
    return;
  }
  
  res.writeHead(404);
  res.end('Not Found');
});

const PORT = process.env.MCP_PORT || 3001;
server.listen(PORT, '127.0.0.1', () => {
  console.log(`🚀 MySQL MCP SSE server running on http://127.0.0.1:${PORT}`);
  console.log(`📊 Health check: http://127.0.0.1:${PORT}/health`);
  console.log(`🔌 MCP endpoint: http://127.0.0.1:${PORT}/sse`);
});

// Graceful shutdown
process.on('SIGINT', async () => {
  console.log('\n🛑 Shutting down gracefully...');
  if (pool) {
    await pool.end();
  }
  server.close(() => {
    console.log('✅ Server closed');
    process.exit(0);
  });
});
