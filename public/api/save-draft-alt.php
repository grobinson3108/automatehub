<?php
// API alternative pour sauvegarder les brouillons via Python
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, host, port, username, password, folder');

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

// Récupérer les headers pour la configuration IMAP
$headers = getallheaders();
$headersLower = array_change_key_case($headers, CASE_LOWER);

// Configuration IMAP depuis les headers ou valeurs par défaut
$currentConfig = [
    'host' => $headersLower['host'] ?? 'imap.ionos.fr',
    'port' => intval($headersLower['port'] ?? 993),
    'username' => $headersLower['username'] ?? 'greg@audelalia.fr',
    'password' => $headersLower['password'] ?? 'Armengo2802!',
    'folder' => $headersLower['folder'] ?? 'Brouillons'
];

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
    $eml .= "Message-ID: <" . uniqid() . "@" . str_replace(['@', 'www.'], '', $from) . ">\r\n";
    $eml .= "MIME-Version: 1.0\r\n";
    $eml .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $eml .= "Content-Transfer-Encoding: quoted-printable\r\n";
    $eml .= "X-Unsent: 1\r\n";
    $eml .= "\r\n";
    $eml .= quoted_printable_encode($body);
    
    // Sauvegarder temporairement le fichier EML
    $tmpFile = tempnam(sys_get_temp_dir(), 'draft_') . '.eml';
    file_put_contents($tmpFile, $eml);
    
    // Script Python pour sauvegarder le brouillon
    $pythonScript = <<<PYTHON
import imaplib
import sys

try:
    mail = imaplib.IMAP4_SSL('{$currentConfig['host']}', {$currentConfig['port']})
    mail.login('{$currentConfig['username']}', '{$currentConfig['password']}')
    
    with open('$tmpFile', 'rb') as f:
        message = f.read()
    
    result = mail.append('{$currentConfig['folder']}', '\\\\Draft', None, message)
    
    if result[0] == 'OK':
        print('SUCCESS')
    else:
        print('ERROR: ' + str(result))
    
    mail.logout()
except Exception as e:
    print('ERROR: ' + str(e))
PYTHON;
    
    // Exécuter le script Python
    $output = shell_exec("python3 -c " . escapeshellarg($pythonScript) . " 2>&1");
    
    // Nettoyer le fichier temporaire
    @unlink($tmpFile);
    
    if (strpos($output, 'SUCCESS') !== false) {
        echo json_encode([
            'success' => true,
            'message' => 'Brouillon sauvegardé avec succès',
            'folder' => $currentConfig['folder'],
            'host' => $currentConfig['host']
        ]);
    } else {
        throw new Exception('Erreur lors de la sauvegarde: ' . $output);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>