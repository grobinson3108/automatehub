<?php
/**
 * TARS Analytics - Système de tracking des conversions
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Configuration base de données
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_DATABASE') ?: 'automatehub';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Créer tables si elles n'existent pas
$createTables = "
    CREATE TABLE IF NOT EXISTS tars_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        event_type VARCHAR(100) NOT NULL,
        page VARCHAR(255) NOT NULL,
        user_agent TEXT,
        ip_address VARCHAR(45),
        data JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_session (session_id),
        INDEX idx_event_type (event_type),
        INDEX idx_created_at (created_at)
    );

    CREATE TABLE IF NOT EXISTS tars_conversions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        conversion_type VARCHAR(100) NOT NULL,
        amount DECIMAL(10,2) DEFAULT 0,
        currency VARCHAR(3) DEFAULT 'EUR',
        source VARCHAR(255),
        data JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_session (session_id),
        INDEX idx_conversion_type (conversion_type),
        INDEX idx_created_at (created_at)
    );

    CREATE TABLE IF NOT EXISTS tars_funnels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        step_name VARCHAR(100) NOT NULL,
        step_order INT NOT NULL,
        time_spent INT DEFAULT 0,
        data JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_session (session_id),
        INDEX idx_step (step_name),
        INDEX idx_created_at (created_at)
    );
";

$pdo->exec($createTables);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'POST':
        $action = $input['action'] ?? '';

        switch ($action) {
            case 'track_event':
                trackEvent($pdo, $input);
                break;

            case 'track_conversion':
                trackConversion($pdo, $input);
                break;

            case 'track_funnel_step':
                trackFunnelStep($pdo, $input);
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Unknown action']);
        }
        break;

    case 'GET':
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'analytics':
                getAnalytics($pdo);
                break;

            case 'conversions':
                getConversions($pdo);
                break;

            case 'funnel':
                getFunnelAnalytics($pdo);
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Unknown action']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

function trackEvent($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO tars_events (session_id, event_type, page, user_agent, ip_address, data)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['session_id'] ?? generateSessionId(),
        $data['event_type'] ?? '',
        $data['page'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        getClientIP(),
        json_encode($data['data'] ?? [])
    ]);

    echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
}

function trackConversion($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO tars_conversions (session_id, conversion_type, amount, currency, source, data)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['session_id'] ?? generateSessionId(),
        $data['conversion_type'] ?? '',
        $data['amount'] ?? 0,
        $data['currency'] ?? 'EUR',
        $data['source'] ?? '',
        json_encode($data['data'] ?? [])
    ]);

    echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
}

function trackFunnelStep($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO tars_funnels (session_id, step_name, step_order, time_spent, data)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['session_id'] ?? generateSessionId(),
        $data['step_name'] ?? '',
        $data['step_order'] ?? 0,
        $data['time_spent'] ?? 0,
        json_encode($data['data'] ?? [])
    ]);

    echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
}

function getAnalytics($pdo) {
    $period = $_GET['period'] ?? '7d';

    $where = '';
    switch ($period) {
        case '24h':
            $where = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            break;
        case '7d':
            $where = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case '30d':
            $where = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }

    // Événements par type
    $stmt = $pdo->query("
        SELECT event_type, COUNT(*) as count
        FROM tars_events $where
        GROUP BY event_type
        ORDER BY count DESC
    ");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Conversions par type
    $stmt = $pdo->query("
        SELECT conversion_type, COUNT(*) as count, SUM(amount) as total_amount
        FROM tars_conversions $where
        GROUP BY conversion_type
        ORDER BY count DESC
    ");
    $conversions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Taux de conversion par étape
    $stmt = $pdo->query("
        SELECT step_name, COUNT(*) as visitors, step_order
        FROM tars_funnels $where
        GROUP BY step_name, step_order
        ORDER BY step_order
    ");
    $funnel = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sessions uniques
    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT session_id) as unique_sessions
        FROM tars_events $where
    ");
    $sessions = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'events' => $events,
        'conversions' => $conversions,
        'funnel' => $funnel,
        'unique_sessions' => $sessions['unique_sessions'],
        'period' => $period
    ]);
}

function getConversions($pdo) {
    $date = $_GET['date'] ?? date('Y-m-d');

    $stmt = $pdo->prepare("
        SELECT conversion_type, COUNT(*) as count, SUM(amount) as revenue,
               AVG(amount) as avg_amount, MIN(amount) as min_amount, MAX(amount) as max_amount
        FROM tars_conversions
        WHERE DATE(created_at) = ?
        GROUP BY conversion_type
    ");
    $stmt->execute([$date]);
    $conversions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Évolution sur les 7 derniers jours
    $stmt = $pdo->query("
        SELECT DATE(created_at) as date, COUNT(*) as conversions, SUM(amount) as revenue
        FROM tars_conversions
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date
    ");
    $evolution = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'conversions' => $conversions,
        'evolution' => $evolution,
        'date' => $date
    ]);
}

function getFunnelAnalytics($pdo) {
    $stmt = $pdo->query("
        SELECT
            step_name,
            step_order,
            COUNT(DISTINCT session_id) as unique_visitors,
            AVG(time_spent) as avg_time_spent
        FROM tars_funnels
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY step_name, step_order
        ORDER BY step_order
    ");
    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcul des taux de conversion entre étapes
    $funnel_data = [];
    $previous_visitors = null;

    foreach ($steps as $step) {
        $conversion_rate = null;
        if ($previous_visitors !== null && $previous_visitors > 0) {
            $conversion_rate = round(($step['unique_visitors'] / $previous_visitors) * 100, 2);
        }

        $funnel_data[] = [
            'step_name' => $step['step_name'],
            'step_order' => $step['step_order'],
            'visitors' => $step['unique_visitors'],
            'avg_time_spent' => round($step['avg_time_spent'], 2),
            'conversion_rate' => $conversion_rate
        ];

        $previous_visitors = $step['unique_visitors'];
    }

    echo json_encode(['funnel' => $funnel_data]);
}

function generateSessionId() {
    return bin2hex(random_bytes(16));
}

function getClientIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
?>