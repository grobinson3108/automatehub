# 💰 Stratégie Tarifaire Content Extractor

## Pour les abonnés Skool (37€/mois)

### ✅ Inclus dans l'abonnement :
- **100 extractions/mois** (valorisé à 10€)
- Accès prioritaire aux nouvelles fonctionnalités
- Support dédié sur Skool
- Tutoriels exclusifs

### 📈 Au-delà de 100 extractions :
- Pack 100 extractions supplémentaires : 5€
- Pack 500 extractions : 20€
- Pack 1000 extractions : 35€

## Pour les non-abonnés

### 🎯 Découverte :
- **10 extractions gratuites** à l'inscription
- Puis 0,15€/extraction
- Ou abonnement Content Extractor seul : 19€/mois (200 extractions)

### 🚀 Incitation à rejoindre Skool :
"Économisez 50% en devenant membre de la communauté Skool !"
- Skool : 37€/mois = Communauté + Workflows + 100 extractions
- Sans Skool : 19€/mois = Juste 200 extractions

## 💡 Avantages de cette stratégie

1. **Valeur ajoutée Skool** : +10€ de valeur perçue
2. **Marge confortable** : 100 extractions = ~0,20€ de coût réel
3. **Upsell naturel** : Les gros utilisateurs achètent des packs
4. **Acquisition** : 10 gratuites = hook parfait

## 🔧 Implementation technique

### Dans le node n8n :
```javascript
// Vérification du quota
if (subscription.type === 'skool') {
  monthlyLimit = 100;
  extraCost = 0.05; // 50% de réduction
} else if (subscription.type === 'free') {
  monthlyLimit = 10;
  extraCost = 0.15;
} else if (subscription.type === 'pro') {
  monthlyLimit = 200;
  extraCost = 0.08;
}
```

### Tracking dans la DB :
```sql
CREATE TABLE user_quotas (
  user_id VARCHAR(255) PRIMARY KEY,
  subscription_type ENUM('free', 'skool', 'pro'),
  monthly_quota INT DEFAULT 10,
  used_this_month INT DEFAULT 0,
  extra_credits INT DEFAULT 0,
  reset_date DATE
);
```