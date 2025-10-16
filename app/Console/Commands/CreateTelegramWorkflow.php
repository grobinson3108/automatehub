<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\N8nApiService;
use Exception;

class CreateTelegramWorkflow extends Command
{
    protected $signature = 'n8n:create-telegram {--token= : Telegram Bot Token} {--name=Telegram AutoResponder : Workflow name}';
    protected $description = 'Create a Telegram automation workflow that handles text and voice messages';

    public function handle(N8nApiService $n8nService)
    {
        $workflowName = $this->option('name');
        $botToken = $this->option('token');
        
        if (!$botToken) {
            $botToken = $this->ask('Entrez le token de votre bot Telegram (optionnel pour le test):');
        }
        
        $this->info("🔄 Création du workflow Telegram: {$workflowName}");

        // Workflow avec trigger Telegram, traitement des messages texte/vocaux et réponses automatiques
        $workflowData = [
            'name' => $workflowName,
            'nodes' => [
                // Telegram Trigger
                [
                    'parameters' => [
                        'authentication' => 'accessToken',
                        'accessToken' => $botToken ?: 'YOUR_TELEGRAM_BOT_TOKEN',
                        'updates' => ['message']
                    ],
                    'id' => 'telegram-trigger',
                    'name' => 'Telegram Trigger',
                    'type' => 'n8n-nodes-base.telegramTrigger',
                    'typeVersion' => 1,
                    'position' => [240, 300],
                    'webhookId' => 'telegram-webhook-' . uniqid()
                ],
                
                // IF node pour détecter le type de message
                [
                    'parameters' => [
                        'conditions' => [
                            'options' => [
                                'caseSensitive' => true,
                                'leftValue' => '',
                                'rightValue' => ''
                            ],
                            'conditions' => [
                                [
                                    'leftValue' => '={{ $json.message.voice }}',
                                    'rightValue' => '',
                                    'operation' => 'notEmpty'
                                ]
                            ],
                            'combinator' => 'and'
                        ]
                    ],
                    'id' => 'if-voice-message',
                    'name' => 'Is Voice Message?',
                    'type' => 'n8n-nodes-base.if',
                    'typeVersion' => 1,
                    'position' => [460, 300]
                ],

                // Traitement des messages vocaux
                [
                    'parameters' => [
                        'values' => [
                            'string' => [
                                [
                                    'name' => 'response',
                                    'value' => '🎤 J\'ai reçu votre message vocal ! Malheureusement, je ne peux pas encore traiter l\'audio directement, mais je peux vous aider avec des messages texte. Que puis-je faire pour vous ?'
                                ],
                                [
                                    'name' => 'message_type',
                                    'value' => 'voice'
                                ],
                                [
                                    'name' => 'chat_id',
                                    'value' => '={{ $json.message.chat.id }}'
                                ]
                            ]
                        ]
                    ],
                    'id' => 'handle-voice',
                    'name' => 'Handle Voice Message',
                    'type' => 'n8n-nodes-base.set',
                    'typeVersion' => 1,
                    'position' => [680, 200]
                ],

                // Traitement des messages texte
                [
                    'parameters' => [
                        'conditions' => [
                            'options' => [
                                'caseSensitive' => false,
                                'leftValue' => '',
                                'rightValue' => ''
                            ],
                            'conditions' => [
                                [
                                    'leftValue' => '={{ $json.message.text.toLowerCase() }}',
                                    'rightValue' => 'bonjour',
                                    'operation' => 'contains'
                                ]
                            ],
                            'combinator' => 'or'
                        ]
                    ],
                    'id' => 'check-greeting',
                    'name' => 'Check Greeting',
                    'type' => 'n8n-nodes-base.if',
                    'typeVersion' => 1,
                    'position' => [680, 400]
                ],

                // Réponse pour salutations
                [
                    'parameters' => [
                        'values' => [
                            'string' => [
                                [
                                    'name' => 'response',
                                    'value' => '👋 Bonjour ! Je suis votre assistant AutomateHub. Je peux vous aider avec vos automatisations. Que souhaitez-vous faire aujourd\'hui ?'
                                ],
                                [
                                    'name' => 'message_type',
                                    'value' => 'greeting'
                                ],
                                [
                                    'name' => 'chat_id',
                                    'value' => '={{ $json.message.chat.id }}'
                                ]
                            ]
                        ]
                    ],
                    'id' => 'greeting-response',
                    'name' => 'Greeting Response',
                    'type' => 'n8n-nodes-base.set',
                    'typeVersion' => 1,
                    'position' => [900, 300]
                ],

                // Réponse générale pour autres messages
                [
                    'parameters' => [
                        'conditions' => [
                            'options' => [
                                'caseSensitive' => false,
                                'leftValue' => '',
                                'rightValue' => ''
                            ],
                            'conditions' => [
                                [
                                    'leftValue' => '={{ $json.message.text.toLowerCase() }}',
                                    'rightValue' => 'aide',
                                    'operation' => 'contains'
                                ],
                                [
                                    'leftValue' => '={{ $json.message.text.toLowerCase() }}',
                                    'rightValue' => 'help',
                                    'operation' => 'contains'
                                ]
                            ],
                            'combinator' => 'or'
                        ]
                    ],
                    'id' => 'check-help',
                    'name' => 'Check Help Request',
                    'type' => 'n8n-nodes-base.if',
                    'typeVersion' => 1,
                    'position' => [900, 500]
                ],

                // Réponse d'aide
                [
                    'parameters' => [
                        'values' => [
                            'string' => [
                                [
                                    'name' => 'response',
                                    'value' => '🤖 Voici ce que je peux faire :\n\n• Répondre à vos salutations\n• Vous aider avec des questions sur l\'automation\n• Traiter vos messages texte et vocaux\n• Vous guider vers les ressources AutomateHub\n\nDites-moi simplement ce dont vous avez besoin !'
                                ],
                                [
                                    'name' => 'message_type',
                                    'value' => 'help'
                                ],
                                [
                                    'name' => 'chat_id',
                                    'value' => '={{ $json.message.chat.id }}'
                                ]
                            ]
                        ]
                    ],
                    'id' => 'help-response',
                    'name' => 'Help Response',
                    'type' => 'n8n-nodes-base.set',
                    'typeVersion' => 1,
                    'position' => [1120, 400]
                ],

                // Réponse par défaut
                [
                    'parameters' => [
                        'values' => [
                            'string' => [
                                [
                                    'name' => 'response',
                                    'value' => '💬 Merci pour votre message ! Je suis en cours d\'apprentissage pour mieux vous répondre. En attendant, vous pouvez me dire "aide" pour voir ce que je peux faire, ou visiter automatehub.fr pour plus de ressources.'
                                ],
                                [
                                    'name' => 'message_type',
                                    'value' => 'default'
                                ],
                                [
                                    'name' => 'chat_id',
                                    'value' => '={{ $json.message.chat.id }}'
                                ]
                            ]
                        ]
                    ],
                    'id' => 'default-response',
                    'name' => 'Default Response',
                    'type' => 'n8n-nodes-base.set',
                    'typeVersion' => 1,
                    'position' => [1120, 600]
                ],

                // Merge des réponses
                [
                    'parameters' => [
                        'mode' => 'mergeByPosition'
                    ],
                    'id' => 'merge-responses',
                    'name' => 'Merge Responses',
                    'type' => 'n8n-nodes-base.merge',
                    'typeVersion' => 2,
                    'position' => [1340, 400]
                ],

                // Envoi de la réponse via Telegram
                [
                    'parameters' => [
                        'authentication' => 'accessToken',
                        'accessToken' => $botToken ?: 'YOUR_TELEGRAM_BOT_TOKEN',
                        'resource' => 'message',
                        'operation' => 'sendMessage',
                        'chatId' => '={{ $json.chat_id }}',
                        'text' => '={{ $json.response }}',
                        'additionalFields' => [
                            'parse_mode' => 'HTML'
                        ]
                    ],
                    'id' => 'send-telegram-response',
                    'name' => 'Send Telegram Response',
                    'type' => 'n8n-nodes-base.telegram',
                    'typeVersion' => 1,
                    'position' => [1560, 400]
                ]
            ],
            'connections' => [
                'Telegram Trigger' => [
                    'main' => [
                        [
                            [
                                'node' => 'Is Voice Message?',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ],
                'Is Voice Message?' => [
                    'main' => [
                        [
                            [
                                'node' => 'Handle Voice Message',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ],
                        [
                            [
                                'node' => 'Check Greeting',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ],
                'Handle Voice Message' => [
                    'main' => [
                        [
                            [
                                'node' => 'Merge Responses',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ],
                'Check Greeting' => [
                    'main' => [
                        [
                            [
                                'node' => 'Greeting Response',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ],
                        [
                            [
                                'node' => 'Check Help Request',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ],
                'Greeting Response' => [
                    'main' => [
                        [
                            [
                                'node' => 'Merge Responses',
                                'type' => 'main',
                                'index' => 1
                            ]
                        ]
                    ]
                ],
                'Check Help Request' => [
                    'main' => [
                        [
                            [
                                'node' => 'Help Response',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ],
                        [
                            [
                                'node' => 'Default Response',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ],
                'Help Response' => [
                    'main' => [
                        [
                            [
                                'node' => 'Merge Responses',
                                'type' => 'main',
                                'index' => 2
                            ]
                        ]
                    ]
                ],
                'Default Response' => [
                    'main' => [
                        [
                            [
                                'node' => 'Merge Responses',
                                'type' => 'main',
                                'index' => 3
                            ]
                        ]
                    ]
                ],
                'Merge Responses' => [
                    'main' => [
                        [
                            [
                                'node' => 'Send Telegram Response',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ]
            ],
            'active' => false,
            'tags' => ['AutomateHub', 'Telegram', 'Assistant', 'Auto-Reply']
        ];

        try {
            $workflow = $n8nService->createWorkflow($workflowData);
            
            $this->info('✅ Workflow Telegram créé avec succès !');
            $this->line('');
            $this->info('📋 Détails du workflow:');
            $this->line('ID: ' . $workflow['id']);
            $this->line('Nom: ' . $workflow['name']);
            $this->line('Actif: ' . ($workflow['active'] ? 'Oui' : 'Non'));
            $this->line('');
            $this->info('🌐 Vous pouvez le voir à: ' . config('n8n.url') . '/workflow/' . $workflow['id']);
            
            if (!$botToken || $botToken === 'YOUR_TELEGRAM_BOT_TOKEN') {
                $this->line('');
                $this->warn('⚠️  N\'oubliez pas de:');
                $this->line('1. Configurer votre token Telegram Bot dans le workflow');
                $this->line('2. Activer le workflow une fois configuré');
                $this->line('3. Configurer le webhook Telegram si nécessaire');
            }
            
            $this->line('');
            $this->info('🤖 Fonctionnalités du bot:');
            $this->line('• Détection automatique des messages vocaux');
            $this->line('• Réponses intelligentes aux salutations');
            $this->line('• Système d\'aide intégré');
            $this->line('• Réponses par défaut pour tous les autres messages');
            
            return 0;

        } catch (Exception $e) {
            $this->error('❌ Échec de la création du workflow Telegram');
            $this->error('Erreur: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), 'unauthorized')) {
                $this->line('');
                $this->warn('💡 Ceci pourrait être un problème d\'authentification:');
                $this->line('1. Vérifiez que votre clé API est correcte');
                $this->line('2. Vérifiez que l\'API est activée dans n8n');
                $this->line('3. Lancez: php artisan n8n:setup-api');
            }
            
            return 1;
        }
    }
}