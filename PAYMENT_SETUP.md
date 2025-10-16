# 💰 Configuration Paiements Content Extractor

## Étape 1 : Créer les Payment Links Stripe

1. Connectez-vous à [Stripe Dashboard](https://dashboard.stripe.com)
2. Allez dans "Payment Links"
3. Créez 3 produits :

### Pack 100 crédits
- Prix : 5€
- Métadonnées : `product_id: pack_100`
- Redirect URL : https://automatehub.fr/content-extractor/success

### Pack 500 crédits
- Prix : 20€
- Métadonnées : `product_id: pack_500`
- Redirect URL : https://automatehub.fr/content-extractor/success

### Pack 1000 crédits
- Prix : 35€
- Métadonnées : `product_id: pack_1000`
- Redirect URL : https://automatehub.fr/content-extractor/success

## Étape 2 : Configurer le Webhook Stripe

1. Dans Stripe > Developers > Webhooks
2. Ajouter endpoint : `https://automatehub.fr/webhooks/stripe`
3. Événements à écouter : `checkout.session.completed`

## Étape 3 : Routes Laravel

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/content-extractor', [ContentExtractorController::class, 'dashboard']);
    Route::get('/content-extractor/success', [ContentExtractorController::class, 'success']);
});

Route::post('/webhooks/stripe', [ContentExtractorController::class, 'stripeWebhook']);
```

## Étape 4 : Migration pour synchro Skool

```bash
php artisan make:migration create_skool_members_table
```

```php
Schema::create('skool_members', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('skool_id')->nullable();
    $table->enum('status', ['active', 'cancelled'])->default('active');
    $table->timestamps();
});
```

## Option Alternative : Gumroad

Plus simple encore, utilisez Gumroad :
1. Créez les produits sur Gumroad
2. Utilisez leur API pour vérifier les achats
3. Webhook pour ajouter les crédits

## Flux utilisateur

1. User se connecte sur automatehub.fr
2. Va sur /content-extractor
3. Voit ses crédits et clé API
4. Clique "Acheter" → Stripe Payment Link
5. Paye → Webhook → Crédits ajoutés
6. Utilise dans n8n

## Avantages de cette approche

✅ Pas besoin de PCI compliance
✅ Pas de formulaire de paiement à créer
✅ Stripe gère tout (taxes, factures, etc.)
✅ Fonctionne immédiatement
✅ Sécurisé et professionnel