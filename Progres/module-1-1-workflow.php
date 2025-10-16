<?php

/**
 * Script de création automatique du workflow Module 1.1
 * Introduction à l'automatisation
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\N8nApiService;

$n8nService = new N8nApiService();

// Configuration du workflow Module 1.1
$workflowConfig = [
    'name' => 'Module 1.1 - Introduction à l\'automatisation',
    'nodes' => [
        [
            'parameters' => [
                'rule' => [
                    'interval' => [
                        ['field' => 'cronExpression', 'value' => '0 9 * * *'] // 9h chaque jour
                    ]
                ]
            ],
            'name' => 'Démarrage quotidien',
            'type' => 'n8n-nodes-base.cron',
            'typeVersion' => 1,
            'position' => [100, 200],
            'id' => 'trigger-node'
        ],
        [
            'parameters' => [
                'values' => [
                    'string' => [
                        [
                            'name' => 'message_bienvenue',
                            'value' => 'Bienvenue dans n8n ! Ceci est votre première automation.'
                        ],
                        [
                            'name' => 'plateforme',
                            'value' => 'n8n'
                        ],
                        [
                            'name' => 'niveau',
                            'value' => 'débutant'
                        ]
                    ],
                    'number' => [
                        [
                            'name' => 'etape',
                            'value' => 1
                        ]
                    ]
                ]
            ],
            'name' => 'Définir les données',
            'type' => 'n8n-nodes-base.set',
            'typeVersion' => 1,
            'position' => [300, 200],
            'id' => 'set-data-node'
        ],
        [
            'parameters' => [
                'conditions' => [
                    'string' => [
                        [
                            'value1' => '={{$json.plateforme}}',
                            'operation' => 'equal',
                            'value2' => 'n8n'
                        ]
                    ]
                ]
            ],
            'name' => 'Vérifier plateforme',
            'type' => 'n8n-nodes-base.if',
            'typeVersion' => 1,
            'position' => [500, 200],
            'id' => 'condition-node'
        ],
        [
            'parameters' => [
                'authentication' => 'predefinedCredentialType',
                'nodeCredentialType' => 'gmailApi',
                'subject' => 'Module 1.1 - Introduction à n8n',
                'message' => 'Félicitations ! Vous avez créé votre premier workflow n8n.\n\nCe workflow démontre :\n- Trigger automatique (Cron)\n- Manipulation de données (Set)\n- Logique conditionnelle (If)\n- Action finale (Email)\n\nMessage : {{$json.message_bienvenue}}\nÉtape : {{$json.etape}}\nNiveau : {{$json.niveau}}',
                'toList' => 'student@example.com'
            ],
            'name' => 'Envoyer confirmation',
            'type' => 'n8n-nodes-base.gmail',
            'typeVersion' => 1,
            'position' => [700, 150],
            'id' => 'email-success-node'
        ],
        [
            'parameters' => [
                'values' => [
                    'string' => [
                        [
                            'name' => 'erreur',
                            'value' => 'Plateforme non reconnue'
                        ]
                    ]
                ]
            ],
            'name' => 'Gérer erreur',
            'type' => 'n8n-nodes-base.set',
            'typeVersion' => 1,
            'position' => [700, 250],
            'id' => 'error-node'
        ]
    ],
    'connections' => [
        'Démarrage quotidien' => [
            'main' => [
                [
                    'node' => 'Définir les données',
                    'type' => 'main',
                    'index' => 0
                ]
            ]
        ],
        'Définir les données' => [
            'main' => [
                [
                    'node' => 'Vérifier plateforme',
                    'type' => 'main',
                    'index' => 0
                ]
            ]
        ],
        'Vérifier plateforme' => [
            'main' => [
                [
                    'node' => 'Envoyer confirmation',
                    'type' => 'main',
                    'index' => 0
                ],
                [
                    'node' => 'Gérer erreur',
                    'type' => 'main',
                    'index' => 0
                ]
            ]
        ]
    ],
    'settings' => [
        'saveManualExecutions' => true,
        'callerPolicy' => 'any',
        'errorWorkflow' => '',
        'timezone' => 'Europe/Paris'
    ]
];

// Documentation du workflow
$documentation = [
    'title' => 'Module 1.1 - Introduction à l\'automatisation',
    'description' => 'Premier workflow du cours n8n MasterClass',
    'duration' => '20 minutes',
    'level' => 'Débutant',
    'objectives' => [
        'Comprendre les concepts de base de n8n',
        'Créer un workflow simple avec trigger, data et action',
        'Apprendre la logique conditionnelle',
        'Maîtriser l\'envoi d\'emails automatisés'
    ],
    'concepts' => [
        'Cron Trigger - Déclenchement automatique',
        'Set Node - Manipulation de données',
        'If Node - Logique conditionnelle',
        'Gmail Node - Action email'
    ],
    'instructions' => [
        '1. Créer un nouveau workflow',
        '2. Ajouter un trigger Cron (9h quotidien)',
        '3. Connecter un node Set avec les données',
        '4. Ajouter une condition If pour vérifier la plateforme',
        '5. Brancher un Gmail node pour le succès',
        '6. Ajouter gestion d\'erreur',
        '7. Tester le workflow manuellement',
        '8. Activer l\'automation'
    ],
    'tips' => [
        'Utilisez le mode debug pour voir les données',
        'Testez chaque node individuellement',
        'Vérifiez les credentials Gmail',
        'Consultez les logs d\'exécution'
    ],
    'exercises' => [
        'Modifier l\'heure du trigger',
        'Changer le message de bienvenue',
        'Ajouter un node Slack au lieu d\'email',
        'Créer une condition sur le niveau'
    ]
];

// Créer le fichier de documentation
file_put_contents(__DIR__ . '/Module-1-1-Documentation.md', "# " . $documentation['title'] . "\n\n" . 
    "**Durée :** " . $documentation['duration'] . "\n" .
    "**Niveau :** " . $documentation['level'] . "\n\n" .
    "## 🎯 Objectifs\n\n" .
    implode("\n", array_map(fn($obj) => "- $obj", $documentation['objectives'])) . "\n\n" .
    "## 📚 Concepts abordés\n\n" .
    implode("\n", array_map(fn($concept) => "- $concept", $documentation['concepts'])) . "\n\n" .
    "## 🔧 Instructions étape par étape\n\n" .
    implode("\n", $documentation['instructions']) . "\n\n" .
    "## 💡 Conseils\n\n" .
    implode("\n", array_map(fn($tip) => "- $tip", $documentation['tips'])) . "\n\n" .
    "## 🏋️ Exercices pratiques\n\n" .
    implode("\n", array_map(fn($exercise) => "- $exercise", $documentation['exercises'])) . "\n\n" .
    "## 🔗 Workflow n8n\n\n" .
    "Le workflow peut être importé directement dans n8n avec cette configuration :\n\n" .
    "```json\n" . json_encode($workflowConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```\n"
);

echo "✅ Documentation Module 1.1 créée : " . __DIR__ . "/Module-1-1-Documentation.md\n";
echo "🔄 Configuration workflow prête pour import n8n\n";