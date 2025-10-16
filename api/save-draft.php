<?php
// API pour sauvegarder les brouillons via IMAP
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Récupérer les données POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

// Configuration IMAP selon l'environnement
$config = [
    'test' => [
        'host' => 'imap.ionos.fr',
        'port' => 993,
        'username' => 'greg@audelalia.fr',
        'password' => 'Armengo2802!',
        'folder' => 'Brouillons'
    ],
    'production' => [
        'host' => 'ssl0.ovh.net',
        'port' => 993,
        'username' => 'greg@agsteeltrading.com',
        'password' => 'votre_mot_de_passe_production', // À remplacer
        'folder' => 'Drafts'
    ]
];

// Utiliser la configuration de test par défaut
$env = $input['environment'] ?? 'test';
$currentConfig = $config[$env] ?? $config['test'];

try {
    // Créer le contenu EML
    $from = $input['from'] ?? $currentConfig['username'];
    $to = $input['to'] ?? '';
    $subject = $input['subject'] ?? 'Sans sujet';
    $body = $input['body'] ?? '';
    $originalSubject = $input['originalSubject'] ?? '';
    
    // Si c'est une réponse, ajuster le sujet
    if ($originalSubject && strpos($subject, 'Re:') !== 0) {
        $subject = "Re: " . $originalSubject;
    }
    
    $eml = "From: $from\r\n";
    $eml .= "To: $to\r\n";
    $eml .= "Subject: $subject\r\n";
    $eml .= "Date: " . date('r') . "\r\n";
    $eml .= "Message-ID: <" . uniqid() . "@" . parse_url($from, PHP_URL_HOST) . ">\r\n";
    $eml .= "MIME-Version: 1.0\r\n";
    $eml .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $eml .= "Content-Transfer-Encoding: quoted-printable\r\n";
    $eml .= "X-Unsent: 1\r\n";
    $eml .= "\r\n";
    $eml .= quoted_printable_encode($body);
    
    // Connexion IMAP
    $inbox = imap_open(
        "{" . $currentConfig['host'] . ":" . $currentConfig['port'] . "/imap/ssl}",
        $currentConfig['username'],
        $currentConfig['password']
    );
    
    if (!$inbox) {
        throw new Exception('Connexion IMAP impossible: ' . imap_last_error());
    }
    
    // Sauvegarder le brouillon
    $result = imap_append(
        $inbox,
        "{" . $currentConfig['host'] . ":" . $currentConfig['port'] . "/imap/ssl}" . $currentConfig['folder'],
        $eml,
        "\\Draft \\Seen"
    );
    
    imap_close($inbox);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Brouillon sauvegardé avec succès',
            'environment' => $env,
            'folder' => $currentConfig['folder']
        ]);
    } else {
        throw new Exception('Erreur lors de la sauvegarde: ' . imap_last_error());
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>