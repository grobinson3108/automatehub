-- Tables pour AutomateHub Workflows Marketplace

-- Catégories principales
CREATE TABLE IF NOT EXISTS workflow_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name_fr VARCHAR(100) NOT NULL,
    name_en VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    parent_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES workflow_categories(id)
);

-- Workflows
CREATE TABLE IF NOT EXISTS workflows (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name_fr VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NOT NULL,
    description_fr TEXT,
    description_en TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    category_id INT,
    difficulty_level ENUM('beginner', 'intermediate', 'expert') DEFAULT 'intermediate',
    node_count INT DEFAULT 0,
    estimated_time_saved INT DEFAULT 0, -- en minutes par semaine
    json_content LONGTEXT, -- Le workflow n8n en JSON
    preview_image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_bestseller BOOLEAN DEFAULT FALSE,
    downloads INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES workflow_categories(id)
);

-- Tags pour recherche
CREATE TABLE IF NOT EXISTS workflow_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL
);

-- Relation workflows-tags
CREATE TABLE IF NOT EXISTS workflow_tag_relations (
    workflow_id INT,
    tag_id INT,
    PRIMARY KEY (workflow_id, tag_id),
    FOREIGN KEY (workflow_id) REFERENCES workflows(id),
    FOREIGN KEY (tag_id) REFERENCES workflow_tags(id)
);

-- Intégrations requises
CREATE TABLE IF NOT EXISTS integrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(255),
    documentation_url VARCHAR(255)
);

-- Relation workflows-integrations
CREATE TABLE IF NOT EXISTS workflow_integrations (
    workflow_id INT,
    integration_id INT,
    is_required BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (workflow_id, integration_id),
    FOREIGN KEY (workflow_id) REFERENCES workflows(id),
    FOREIGN KEY (integration_id) REFERENCES integrations(id)
);

-- Packs de workflows
CREATE TABLE IF NOT EXISTS workflow_packs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name_fr VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NOT NULL,
    description_fr TEXT,
    description_en TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    workflow_count INT DEFAULT 0,
    discount_percentage INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Relation packs-workflows
CREATE TABLE IF NOT EXISTS pack_workflows (
    pack_id INT,
    workflow_id INT,
    PRIMARY KEY (pack_id, workflow_id),
    FOREIGN KEY (pack_id) REFERENCES workflow_packs(id),
    FOREIGN KEY (workflow_id) REFERENCES workflows(id)
);

-- Commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    user_name VARCHAR(255),
    total_amount DECIMAL(10,2) NOT NULL,
    discount_code VARCHAR(50),
    discount_amount DECIMAL(10,2) DEFAULT 0,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50),
    stripe_payment_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Détails commandes
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    item_type ENUM('workflow', 'pack') NOT NULL,
    item_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Téléchargements
CREATE TABLE IF NOT EXISTS downloads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    workflow_id INT,
    download_token VARCHAR(255) UNIQUE NOT NULL,
    downloaded_at TIMESTAMP NULL,
    expires_at TIMESTAMP NOT NULL,
    ip_address VARCHAR(45),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (workflow_id) REFERENCES workflows(id)
);

-- Codes promo
CREATE TABLE IF NOT EXISTS discount_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL,
    minimum_amount DECIMAL(10,2) DEFAULT 0,
    usage_limit INT DEFAULT NULL,
    usage_count INT DEFAULT 0,
    valid_from DATE NOT NULL,
    valid_until DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour optimisation
CREATE INDEX idx_workflows_category ON workflows(category_id);
CREATE INDEX idx_workflows_price ON workflows(price);
CREATE INDEX idx_workflows_difficulty ON workflows(difficulty_level);
CREATE INDEX idx_orders_email ON orders(user_email);
CREATE INDEX idx_orders_status ON orders(payment_status);