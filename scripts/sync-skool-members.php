#!/usr/bin/env php
<?php
/**
 * Script pour synchroniser les membres Skool avec Content Extractor
 * À exécuter via CRON toutes les heures
 */

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

// Configuration Skool
$skoolApiKey = env('SKOOL_API_KEY');
$skoolGroupId = env('SKOOL_GROUP_ID', 'automatehub');

// Récupérer les membres actifs de Skool
// Note: L'API Skool n'est pas publique, vous devrez peut-être :
// 1. Exporter manuellement depuis Skool
// 2. Utiliser Zapier/Make pour synchroniser
// 3. Scraper (non recommandé)

// Pour l'instant, on simule avec un webhook
// Quand quelqu'un s'abonne sur Skool, utilisez Zapier pour appeler :
// POST https://automatehub.fr/api/skool-webhook

class SkoolSync {
    
    public function syncMember($email, $status = 'active') {
        // Vérifier si le membre existe dans Laravel
        $exists = DB::table('skool_members')
            ->where('email', $email)
            ->exists();
        
        if (!$exists && $status === 'active') {
            // Ajouter le membre
            DB::table('skool_members')->insert([
                'email' => $email,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Mettre à jour Content Extractor
            $this->upgradeToSkool($email);
            
            echo "✅ Membre ajouté : $email\n";
            
        } elseif ($exists && $status === 'cancelled') {
            // Désactiver le membre
            DB::table('skool_members')
                ->where('email', $email)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now()
                ]);
            
            // Rétrograder dans Content Extractor
            $this->downgradeFromSkool($email);
            
            echo "❌ Membre désactivé : $email\n";
        }
    }
    
    private function upgradeToSkool($email) {
        // Connexion SQLite Content Extractor
        $db = new SQLite3('/var/www/automatehub/data/content-extractor-quotas.db');
        
        // Mettre à jour le quota
        $stmt = $db->prepare('
            UPDATE user_quotas 
            SET subscription_type = "skool", monthly_quota = 100 
            WHERE email = :email
        ');
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->execute();
        
        $db->close();
    }
    
    private function downgradeFromSkool($email) {
        $db = new SQLite3('/var/www/automatehub/data/content-extractor-quotas.db');
        
        $stmt = $db->prepare('
            UPDATE user_quotas 
            SET subscription_type = "free", monthly_quota = 10 
            WHERE email = :email
        ');
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->execute();
        
        $db->close();
    }
}

// Si exécuté directement
if (php_sapi_name() === 'cli') {
    $sync = new SkoolSync();
    
    // Exemple : synchroniser un membre spécifique
    if (isset($argv[1])) {
        $sync->syncMember($argv[1], $argv[2] ?? 'active');
    } else {
        echo "Usage: php sync-skool-members.php email@example.com [active|cancelled]\n";
    }
}