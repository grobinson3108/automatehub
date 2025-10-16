#!/usr/bin/env node

const readline = require('readline');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: '/var/www/automatehub/.env' });

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
  terminal: false
});

let pool = null;

function sendResponse(id, result, error = null) {
  const response = {
    jsonrpc: '2.0',
    id: id
  };
  
  if (error) {
    response.error = error;
  } else {
    response.result = result;
  }
  
  process.stdout.write(JSON.stringify(response) + '\n');
}

async function connectDB() {
  try {
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
    }
    return pool;
  } catch (error) {
    throw new Error(`Database connection failed: ${error.message}`);
  }
}

rl.on('line', async (line) => {
  try {
    const request = JSON.parse(line.trim());
    const { id, method, params } = request;
    
    switch (method) {
      case 'initialize':
        sendResponse(id, {
          protocolVersion: '2024-11-05',
          capabilities: { tools: {} },
          serverInfo: {
            name: 'mysql-laravel-server',
            version: '1.0.0'
          }
        });
        break;
        
      case 'tools/list':
        sendResponse(id, {
          tools: [
            {
              name: 'connect_db',
              description: 'Connect to Laravel database',
              inputSchema: {
                type: 'object',
                properties: {}
              }
            },
            {
              name: 'query',
              description: 'Execute SQL query',
              inputSchema: {
                type: 'object',
                properties: {
                  sql: { type: 'string', description: 'SQL query' }
                },
                required: ['sql']
              }
            },
            {
              name: 'list_tables',
              description: 'List all tables',
              inputSchema: {
                type: 'object',
                properties: {}
              }
            }
          ]
        });
        break;
        
      case 'tools/call':
        const toolName = params?.name;
        const args = params?.arguments || {};
        
        try {
          let result;
          
          switch (toolName) {
            case 'connect_db':
              const dbPool = await connectDB();
              result = {
                content: [{
                  type: 'text',
                  text: `✅ Connected to Laravel database: ${process.env.DB_DATABASE || 'unknown'}`
                }]
              };
              break;
              
            case 'query':
              if (!args.sql) {
                throw new Error('SQL query is required');
              }
              const queryPool = await connectDB();
              const [rows] = await queryPool.query(args.sql);
              result = {
                content: [{
                  type: 'text',
                  text: JSON.stringify(rows, null, 2)
                }]
              };
              break;
              
            case 'list_tables':
              const listPool = await connectDB();
              const [tables] = await listPool.query('SHOW TABLES');
              result = {
                content: [{
                  type: 'text',
                  text: JSON.stringify(tables, null, 2)
                }]
              };
              break;
              
            default:
              throw new Error(`Unknown tool: ${toolName}`);
          }
          
          sendResponse(id, result);
        } catch (error) {
          sendResponse(id, null, {
            code: -32603,
            message: error.message
          });
        }
        break;
        
      default:
        sendResponse(id, null, {
          code: -32601,
          message: `Method not found: ${method}`
        });
    }
  } catch (parseError) {
    // Ignore JSON parse errors
  }
});

console.error('Laravel MySQL MCP server started');
