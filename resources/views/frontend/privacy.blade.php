@extends('layouts.frontend')

@section('content')
<!-- Privacy Hero -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8" data-aos="fade-up">
                <h1 class="display-4 fw-bold text-infinity-blue mb-4">Politique de Confidentialité</h1>
                <p class="lead mb-4">
                    Protection et traitement de vos données personnelles conformément au RGPD
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Privacy Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-5" data-aos="fade-up">
                    
                    <div class="alert alert-info mb-5">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Dernière mise à jour :</strong> {{ date('d/m/Y') }}
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">1. Responsable du traitement</h2>
                    <div class="mb-5">
                        <p>Le responsable du traitement des données personnelles collectées sur le site Automatehub.fr est :</p>
                        <p class="ps-4">
                            <strong>Gregory Robinson</strong><br>
                            17 avenue de Palavas<br>
                            34000 Montpellier, France<br>
                            Email : contact@automatehub.fr
                        </p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">2. Données collectées</h2>
                    <div class="mb-5">
                        <h4 class="text-pink-medium">2.1 Données collectées automatiquement</h4>
                        <p>Lors de votre visite sur notre site, nous collectons automatiquement :</p>
                        <ul>
                            <li>Votre adresse IP</li>
                            <li>Le type et la version de votre navigateur</li>
                            <li>Votre système d'exploitation</li>
                            <li>Les pages visitées et la durée de visite</li>
                            <li>La source de votre visite (site référent)</li>
                        </ul>

                        <h4 class="text-pink-medium mt-4">2.2 Données fournies volontairement</h4>
                        <p>Lorsque vous utilisez nos services, vous pouvez nous fournir :</p>
                        <ul>
                            <li>Nom et prénom</li>
                            <li>Adresse email</li>
                            <li>Numéro de téléphone</li>
                            <li>Nom de l'entreprise</li>
                            <li>Toute information contenue dans vos messages</li>
                        </ul>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">3. Finalités du traitement</h2>
                    <div class="mb-5">
                        <p>Vos données personnelles sont collectées pour :</p>
                        <ul>
                            <li><strong>Gestion des demandes de contact :</strong> répondre à vos questions et demandes</li>
                            <li><strong>Fourniture de services :</strong> accès aux tutoriels premium, téléchargements</li>
                            <li><strong>Communication :</strong> envoi d'informations sur nos services (avec votre consentement)</li>
                            <li><strong>Amélioration du site :</strong> analyse statistique et optimisation de l'expérience utilisateur</li>
                            <li><strong>Sécurité :</strong> prévention de la fraude et protection du site</li>
                            <li><strong>Obligations légales :</strong> respect des obligations comptables et fiscales</li>
                        </ul>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">4. Base légale du traitement</h2>
                    <div class="mb-5">
                        <p>Le traitement de vos données personnelles est fondé sur :</p>
                        <ul>
                            <li><strong>Votre consentement :</strong> pour l'envoi de communications marketing</li>
                            <li><strong>L'exécution d'un contrat :</strong> pour la fourniture de nos services</li>
                            <li><strong>Nos intérêts légitimes :</strong> pour l'amélioration de nos services et la sécurité</li>
                            <li><strong>Une obligation légale :</strong> pour la conservation de certaines données</li>
                        </ul>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">5. Durée de conservation</h2>
                    <div class="mb-5">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Type de données</th>
                                    <th>Durée de conservation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Données de contact (formulaire)</td>
                                    <td>3 ans après le dernier contact</td>
                                </tr>
                                <tr>
                                    <td>Données de compte utilisateur</td>
                                    <td>Durée de vie du compte + 1 an</td>
                                </tr>
                                <tr>
                                    <td>Données de navigation (cookies)</td>
                                    <td>13 mois maximum</td>
                                </tr>
                                <tr>
                                    <td>Données comptables</td>
                                    <td>10 ans (obligation légale)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">6. Destinataires des données</h2>
                    <div class="mb-5">
                        <p>Vos données peuvent être partagées avec :</p>
                        <ul>
                            <li><strong>Nos prestataires techniques :</strong> hébergeur (IONOS), services d'emailing</li>
                            <li><strong>Les autorités compétentes :</strong> en cas d'obligation légale</li>
                        </ul>
                        <p class="mt-3"><strong>Important :</strong> Nous ne vendons jamais vos données personnelles à des tiers.</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">7. Transferts internationaux</h2>
                    <div class="mb-5">
                        <p>Vos données sont hébergées en France et dans l'Union Européenne. Si un transfert hors UE était nécessaire, nous nous assurerions de la mise en place de garanties appropriées (clauses contractuelles types, décision d'adéquation).</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">8. Vos droits</h2>
                    <div class="mb-5">
                        <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light p-3">
                                    <h5 class="text-infinity-blue"><i class="fas fa-eye me-2"></i>Droit d'accès</h5>
                                    <p class="mb-0">Obtenir la confirmation que vos données sont traitées et en recevoir une copie</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light p-3">
                                    <h5 class="text-infinity-blue"><i class="fas fa-edit me-2"></i>Droit de rectification</h5>
                                    <p class="mb-0">Corriger vos données personnelles si elles sont inexactes ou incomplètes</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light p-3">
                                    <h5 class="text-infinity-blue"><i class="fas fa-trash me-2"></i>Droit à l'effacement</h5>
                                    <p class="mb-0">Demander la suppression de vos données dans certains cas</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light p-3">
                                    <h5 class="text-infinity-blue"><i class="fas fa-hand-paper me-2"></i>Droit d'opposition</h5>
                                    <p class="mb-0">Vous opposer au traitement de vos données pour des raisons légitimes</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light p-3">
                                    <h5 class="text-infinity-blue"><i class="fas fa-lock me-2"></i>Droit à la limitation</h5>
                                    <p class="mb-0">Limiter le traitement de vos données dans certaines circonstances</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light p-3">
                                    <h5 class="text-infinity-blue"><i class="fas fa-download me-2"></i>Droit à la portabilité</h5>
                                    <p class="mb-0">Recevoir vos données dans un format structuré et lisible</p>
                                </div>
                            </div>
                        </div>
                        <p class="mt-4">Pour exercer ces droits, contactez-nous à : <strong>contact@automatehub.fr</strong></p>
                        <p>Vous pouvez également introduire une réclamation auprès de la CNIL : <a href="https://www.cnil.fr" target="_blank" rel="noopener">www.cnil.fr</a></p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">9. Cookies</h2>
                    <div class="mb-5">
                        <h4 class="text-pink-medium">9.1 Qu'est-ce qu'un cookie ?</h4>
                        <p>Un cookie est un petit fichier texte déposé sur votre ordinateur lors de la visite d'un site web.</p>
                        
                        <h4 class="text-pink-medium mt-4">9.2 Types de cookies utilisés</h4>
                        <ul>
                            <li><strong>Cookies essentiels :</strong> nécessaires au fonctionnement du site</li>
                            <li><strong>Cookies analytiques :</strong> pour comprendre comment vous utilisez le site</li>
                            <li><strong>Cookies de préférences :</strong> pour mémoriser vos choix</li>
                        </ul>

                        <h4 class="text-pink-medium mt-4">9.3 Gestion des cookies</h4>
                        <p>Vous pouvez gérer vos préférences cookies à tout moment via les paramètres de votre navigateur ou en nous contactant.</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">10. Sécurité</h2>
                    <div class="mb-5">
                        <p>Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données personnelles contre :</p>
                        <ul>
                            <li>La destruction accidentelle ou illicite</li>
                            <li>La perte accidentelle</li>
                            <li>L'altération</li>
                            <li>La divulgation ou l'accès non autorisé</li>
                        </ul>
                        <p class="mt-3">Ces mesures incluent notamment :</p>
                        <ul>
                            <li>Le chiffrement SSL/TLS pour les transmissions</li>
                            <li>L'accès restreint aux données personnelles</li>
                            <li>La sauvegarde régulière des données</li>
                            <li>La mise à jour régulière de nos systèmes</li>
                        </ul>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">11. Modifications</h2>
                    <div class="mb-5">
                        <p>Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. Les modifications entrent en vigueur dès leur publication sur cette page. Nous vous encourageons à consulter régulièrement cette politique.</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">12. Contact</h2>
                    <div class="mb-5">
                        <p>Pour toute question concernant cette politique de confidentialité ou le traitement de vos données personnelles, vous pouvez nous contacter :</p>
                        <div class="card border-0 bg-light p-4 mt-3">
                            <p class="mb-2"><i class="fas fa-envelope me-2 text-pink-medium"></i><strong>Email :</strong> contact@automatehub.fr</p>
                            <p class="mb-2"><i class="fas fa-user me-2 text-pink-medium"></i><strong>Responsable :</strong> Gregory Robinson</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt me-2 text-pink-medium"></i><strong>Adresse :</strong> 17 avenue de Palavas, 34000 Montpellier, France</p>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <i class="fas fa-shield-alt me-2"></i>
                        <strong>Notre engagement :</strong> Nous nous engageons à protéger vos données personnelles et à respecter votre vie privée conformément au RGPD et aux lois françaises en vigueur.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Responsive tables */
@media (max-width: 768px) {
    .table {
        font-size: 0.875rem;
    }
    
    .table td, .table th {
        padding: 0.5rem;
    }
}
</style>
@endsection
