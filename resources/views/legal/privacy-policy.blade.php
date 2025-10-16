@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-4">Politique de Confidentialité</h1>
            <p class="text-muted">Dernière mise à jour : {{ date('d/m/Y') }}</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">1. Introduction</h2>
                    <p>AutomateHub (ci-après « nous », « notre » ou « AutomateHub ») s'engage à protéger la confidentialité et la sécurité des données personnelles de ses utilisateurs. Cette politique de confidentialité explique comment nous collectons, utilisons, stockons et protégeons vos informations personnelles conformément au Règlement Général sur la Protection des Données (RGPD) et à la législation française en vigueur.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">2. Responsable du traitement</h2>
                    <p><strong>AutomateHub</strong><br>
                    Site web : https://automatehub.fr<br>
                    Email : contact@automatehub.fr</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">3. Données collectées</h2>
                    <p>Nous collectons les types de données suivants :</p>
                    
                    <h5>3.1 Données d'identification</h5>
                    <ul>
                        <li>Nom et prénom</li>
                        <li>Adresse email</li>
                        <li>Numéro de téléphone (optionnel)</li>
                        <li>Nom d'entreprise (pour les comptes professionnels)</li>
                    </ul>

                    <h5 class="mt-3">3.2 Données de connexion</h5>
                    <ul>
                        <li>Identifiants de connexion</li>
                        <li>Adresse IP</li>
                        <li>Type de navigateur et système d'exploitation</li>
                        <li>Données de session et cookies</li>
                    </ul>

                    <h5 class="mt-3">3.3 Données d'utilisation</h5>
                    <ul>
                        <li>Workflows créés et modifiés</li>
                        <li>Historique de navigation sur la plateforme</li>
                        <li>Préférences et paramètres utilisateur</li>
                        <li>Interactions avec les tutoriels et formations</li>
                    </ul>

                    <h5 class="mt-3">3.4 Données de paiement</h5>
                    <ul>
                        <li>Informations de facturation</li>
                        <li>Historique des transactions</li>
                        <li>Note : Les données de carte bancaire sont traitées par notre prestataire de paiement sécurisé (Stripe)</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">4. Base légale et finalités du traitement</h2>
                    <p>Nous traitons vos données personnelles sur les bases légales suivantes :</p>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Finalité</th>
                                <th>Base légale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Création et gestion de votre compte</td>
                                <td>Exécution du contrat</td>
                            </tr>
                            <tr>
                                <td>Fourniture des services AutomateHub</td>
                                <td>Exécution du contrat</td>
                            </tr>
                            <tr>
                                <td>Gestion des paiements et facturation</td>
                                <td>Exécution du contrat</td>
                            </tr>
                            <tr>
                                <td>Support client et assistance technique</td>
                                <td>Intérêt légitime</td>
                            </tr>
                            <tr>
                                <td>Envoi de communications marketing</td>
                                <td>Consentement</td>
                            </tr>
                            <tr>
                                <td>Amélioration de nos services</td>
                                <td>Intérêt légitime</td>
                            </tr>
                            <tr>
                                <td>Respect des obligations légales</td>
                                <td>Obligation légale</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">5. Durée de conservation</h2>
                    <ul>
                        <li><strong>Données de compte actif :</strong> Pendant toute la durée de votre abonnement</li>
                        <li><strong>Données de compte inactif :</strong> 3 ans après la dernière connexion</li>
                        <li><strong>Données de facturation :</strong> 10 ans (obligation légale)</li>
                        <li><strong>Cookies :</strong> Maximum 13 mois</li>
                        <li><strong>Logs de sécurité :</strong> 1 an</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">6. Partage des données</h2>
                    <p>Nous ne vendons jamais vos données personnelles. Nous pouvons partager vos données uniquement avec :</p>
                    <ul>
                        <li><strong>Prestataires techniques :</strong> Hébergement (OVH), paiement (Stripe), envoi d'emails</li>
                        <li><strong>n8n :</strong> Pour la synchronisation et l'exécution de vos workflows</li>
                        <li><strong>Autorités légales :</strong> Si requis par la loi</li>
                    </ul>
                    <p>Tous nos prestataires sont situés dans l'Union Européenne ou offrent des garanties appropriées de protection des données.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">7. Sécurité des données</h2>
                    <p>Nous mettons en œuvre des mesures techniques et organisationnelles appropriées :</p>
                    <ul>
                        <li>Chiffrement SSL/TLS pour toutes les communications</li>
                        <li>Hashage des mots de passe avec bcrypt</li>
                        <li>Sauvegardes régulières et chiffrées</li>
                        <li>Accès restreint aux données personnelles</li>
                        <li>Formation du personnel à la protection des données</li>
                        <li>Tests de sécurité réguliers</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">8. Vos droits</h2>
                    <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li><strong>Droit d'accès :</strong> Obtenir une copie de vos données</li>
                                <li><strong>Droit de rectification :</strong> Corriger vos données</li>
                                <li><strong>Droit à l'effacement :</strong> Supprimer vos données</li>
                                <li><strong>Droit de limitation :</strong> Limiter le traitement</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><strong>Droit à la portabilité :</strong> Recevoir vos données dans un format structuré</li>
                                <li><strong>Droit d'opposition :</strong> S'opposer au traitement</li>
                                <li><strong>Droit de retirer le consentement :</strong> À tout moment</li>
                                <li><strong>Droit de réclamation :</strong> Auprès de la CNIL</li>
                            </ul>
                        </div>
                    </div>
                    
                    <p class="mt-3">Pour exercer vos droits, contactez-nous à : <a href="mailto:privacy@automatehub.fr">privacy@automatehub.fr</a></p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">9. Cookies</h2>
                    <p>Nous utilisons des cookies pour :</p>
                    <ul>
                        <li><strong>Cookies essentiels :</strong> Nécessaires au fonctionnement du site</li>
                        <li><strong>Cookies de performance :</strong> Analyser l'utilisation du site (avec votre consentement)</li>
                        <li><strong>Cookies de préférences :</strong> Mémoriser vos choix</li>
                    </ul>
                    <p>Vous pouvez gérer vos préférences cookies à tout moment dans les paramètres de votre compte.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">10. Transferts internationaux</h2>
                    <p>Vos données sont stockées dans l'Union Européenne. Si un transfert hors UE est nécessaire, nous nous assurons que des garanties appropriées sont en place (clauses contractuelles types, décision d'adéquation).</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">11. Modifications de la politique</h2>
                    <p>Nous pouvons mettre à jour cette politique de confidentialité. Les modifications importantes seront notifiées par email et/ou via une notification sur la plateforme. La date de dernière mise à jour est indiquée en haut de cette page.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">12. Contact</h2>
                    <p>Pour toute question concernant cette politique ou vos données personnelles :</p>
                    <ul>
                        <li>Email : <a href="mailto:privacy@automatehub.fr">privacy@automatehub.fr</a></li>
                        <li>Formulaire : <a href="{{ route('contact') }}">Page de contact</a></li>
                        <li>Courrier : AutomateHub - Protection des données, France</li>
                    </ul>
                    
                    <p class="mt-3"><strong>Autorité de contrôle :</strong><br>
                    Commission Nationale de l'Informatique et des Libertés (CNIL)<br>
                    3 Place de Fontenoy - TSA 80715 - 75334 PARIS CEDEX 07<br>
                    Tél : 01 53 73 22 22<br>
                    Site : <a href="https://www.cnil.fr" target="_blank">www.cnil.fr</a></p>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('home') }}" class="btn btn-primary">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</div>
@endsection