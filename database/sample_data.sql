-- Données d'exemple pour AutomateHub

-- Catégories principales
INSERT INTO workflow_categories (name_fr, name_en, slug, icon) VALUES
('E-commerce & Vente', 'E-commerce & Sales', 'ecommerce', '🛒'),
('Marketing & Réseaux sociaux', 'Marketing & Social Media', 'marketing', '📱'),
('Service Client', 'Customer Service', 'customer-service', '💬'),
('Productivité', 'Productivity', 'productivity', '⚡'),
('Intelligence Artificielle', 'Artificial Intelligence', 'ai', '🤖'),
('Data & Analytics', 'Data & Analytics', 'data', '📊');

-- Intégrations populaires
INSERT INTO integrations (name, icon, documentation_url) VALUES
('OpenAI', '🧠', 'https://platform.openai.com/docs'),
('Google Sheets', '📊', 'https://developers.google.com/sheets/api'),
('Telegram', '💬', 'https://core.telegram.org/bots/api'),
('LinkedIn', '💼', 'https://developer.linkedin.com'),
('Slack', '💼', 'https://api.slack.com'),
('MySQL', '🗄️', 'https://dev.mysql.com/doc/'),
('Webhook', '🔗', 'https://n8n.io/integrations/webhook/');

-- Tags
INSERT INTO workflow_tags (name, slug) VALUES
('Agent IA', 'agent-ia'),
('Automatisation', 'automation'),
('Scraping', 'scraping'),
('Email', 'email'),
('Notification', 'notification'),
('Base de données', 'database'),
('API', 'api'),
('Génération contenu', 'content-generation');

-- Exemples de workflows
INSERT INTO workflows (code, name_fr, name_en, description_fr, price, category_id, difficulty_level, node_count, estimated_time_saved) VALUES
('WF001', 'Agent IA Rédacteur LinkedIn', 'AI LinkedIn Writer Agent', 'Génère automatiquement des posts LinkedIn optimisés avec images', 49.00, 2, 'intermediate', 12, 120),
('WF002', 'Scraper E-commerce Multi-sites', 'Multi-site E-commerce Scraper', 'Extrait prix et disponibilité depuis Amazon, eBay, etc.', 79.00, 1, 'expert', 25, 240),
('WF003', 'Assistant Email Intelligent', 'Smart Email Assistant', 'Trie, répond et archive vos emails automatiquement', 39.00, 3, 'beginner', 8, 90),
('WF004', 'Dashboard Analytics Temps Réel', 'Real-time Analytics Dashboard', 'Consolide vos KPIs depuis plusieurs sources', 99.00, 6, 'expert', 30, 300);

-- Packs
INSERT INTO workflow_packs (name_fr, name_en, description_fr, price, original_price, workflow_count, discount_percentage) VALUES
('Pack Découverte n8n', 'n8n Discovery Pack', 'Les 10 workflows essentiels pour démarrer', 49.00, 120.00, 10, 60),
('Pack IA Marketing', 'AI Marketing Pack', '25 workflows IA pour votre marketing', 149.00, 375.00, 25, 60),
('Pack E-commerce Pro', 'E-commerce Pro Pack', '50 workflows pour votre boutique', 299.00, 850.00, 50, 65);

-- Code promo de bienvenue
INSERT INTO discount_codes (code, description, discount_type, discount_value, valid_from, valid_until) VALUES
('BIENVENUE2025', 'Réduction de bienvenue', 'percentage', 10.00, '2025-01-01', '2025-12-31'),
('PACK50', '50€ de réduction sur les packs', 'fixed', 50.00, '2025-01-01', '2025-12-31', 100.00);