#!/usr/bin/env node
import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import {
  CallToolRequestSchema,
  ErrorCode,
  ListToolsRequestSchema,
  McpError,
} from '@modelcontextprotocol/sdk/types.js';
import * as mysql from 'mysql2/promise';
import { config } from 'dotenv';
import { parse as parseUrl } from 'url';
import path from 'path';
import { promises as fs } from 'fs';

// Load environment variables
config();

// Charger les variables depuis le .env du projet
config({ path: '/var/www/automatehub/.env' });

class MySQLServer {
  constructor() {
    this.server = new Server(
      {
        name: 'mysql-server',
        version: '1.0.0',
      },
      {
        capabilities: {
          tools: {},
        },
      }
    );
    this.pool = null;
    this.config = null;
    this.setupToolHandlers();
    this.setupErrorHandlers();
  }

  setupErrorHandlers() {
    this.server.onerror = (error) => console.error('[MCP Error]', error);
    process.on('SIGINT', async () => {
      await this.cleanup();
      process.exit(0);
    });
  }

  async cleanup() {
    if (this.pool) {
      await this.pool.end();
      this.pool = null;
    }
    await this.server.close();
  }

  async loadLaravelConfig() {
    return {
      host: process.env.DB_HOST || 'localhost',
      port: parseInt(process.env.DB_PORT || '3306', 10),
      user: process.env.DB_USERNAME || 'root',
      password: process.env.DB_PASSWORD || '',
      database: process.env.DB_DATABASE || 'test',
    };
  }

  setupToolHandlers() {
    this.server.setRequestHandler(ListToolsRequestSchema, async () => ({
      tools: [
        {
          name: 'connect_db',
          description: 'Connect to MySQL database using Laravel .env config',
          inputSchema: {
            type: 'object',
            properties: {},
          },
        },
        {
          name: 'query',
          description: 'Execute a SELECT query',
          inputSchema: {
            type: 'object',
            properties: {
              sql: { type: 'string', description: 'SQL SELECT query' },
              params: { type: 'array', items: { type: 'string' }, description: 'Parameters' },
            },
            required: ['sql'],
          },
        },
        {
          name: 'list_tables',
          description: 'List all tables',
          inputSchema: { type: 'object', properties: {} },
        },
      ],
    }));

    this.server.setRequestHandler(CallToolRequestSchema, async (request) => {
      switch (request.params.name) {
        case 'connect_db':
          return await this.handleConnectDb();
        case 'query':
          return await this.handleQuery(request.params.arguments || {});
        case 'list_tables':
          return await this.handleListTables();
        default:
          throw new McpError(ErrorCode.MethodNotFound, `Unknown tool: ${request.params.name}`);
      }
    });
  }

  async handleConnectDb() {
    try {
      this.config = await this.loadLaravelConfig();
      this.pool = mysql.createPool({
        ...this.config,
        waitForConnections: true,
        connectionLimit: 10,
      });

      const connection = await this.pool.getConnection();
      connection.release();

      return {
        content: [
          {
            type: 'text',
            text: `✅ Connected to ${this.config.database} at ${this.config.host}:${this.config.port}`,
          },
        ],
      };
    } catch (error) {
      throw new McpError(ErrorCode.InternalError, `Connection failed: ${error.message}`);
    }
  }

  async handleQuery(args) {
    if (!args.sql) {
      throw new McpError(ErrorCode.InvalidParams, 'SQL query required');
    }

    if (!this.pool) {
      await this.handleConnectDb();
    }

    try {
      const [rows] = await this.pool.query(args.sql, args.params || []);
      return {
        content: [{ type: 'text', text: JSON.stringify(rows, null, 2) }],
      };
    } catch (error) {
      throw new McpError(ErrorCode.InternalError, `Query failed: ${error.message}`);
    }
  }

  async handleListTables() {
    if (!this.pool) {
      await this.handleConnectDb();
    }

    try {
      const [rows] = await this.pool.query('SHOW TABLES');
      return {
        content: [{ type: 'text', text: JSON.stringify(rows, null, 2) }],
      };
    } catch (error) {
      throw new McpError(ErrorCode.InternalError, `Failed to list tables: ${error.message}`);
    }
  }

  async run() {
    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    console.error('MySQL MCP server running');
  }
}

const server = new MySQLServer();
server.run().catch((error) => {
  console.error('Fatal error:', error);
  process.exit(1);
});
