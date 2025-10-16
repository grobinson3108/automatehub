@extends('layouts.frontend')

@section('content')
<!-- Legal Hero -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8" data-aos="fade-up">
                <h1 class="display-4 fw-bold text-infinity-blue mb-4">Mentions Légales</h1>
                <p class="lead mb-4">
                    Informations légales concernant le site Automatehub.fr
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Legal Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-5" data-aos="fade-up">
                    
                    <h2 class="fw-bold text-infinity-blue mb-4">1. Éditeur du site</h2>
                    <div class="mb-5">
                        <p><strong>Nom :</strong> Gregory Robinson</p>
                        <p><strong>Adresse :</strong> 17 avenue de Palavas, 34000 Montpellier, France</p>
                        <p><strong>Email :</strong> contact@automatehub.fr</p>
                        <p><strong>Statut :</strong> Entrepreneur individuel</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">2. Hébergement</h2>
                    <div class="mb-5">
                        <p><strong>Hébergeur :</strong> IONOS SARL</p>
                        <p><strong>Adresse :</strong> 7 Place de la Gare, 57200 Sarreguemines, France</p>
                        <p><strong>Téléphone :</strong> 0970 808 911</p>
                        <p><strong>Site web :</strong> <a href="https://www.ionos.fr" target="_blank" rel="noopener">www.ionos.fr</a></p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">3. Propriété intellectuelle</h2>
                    <div class="mb-5">
                        <p>L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.</p>
                        <p>La reproduction de tout ou partie de ce site sur un support électronique quel qu'il soit est formellement interdite sauf autorisation expresse du directeur de la publication.</p>
                        <p>Les marques citées sur ce site sont déposées par les sociétés qui en sont propriétaires.</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">4. Responsabilité</h2>
                    <div class="mb-5">
                        <p>Les informations fournies sur ce site le sont à titre indicatif. Automatehub ne saurait garantir l'exactitude, la complétude, l'actualité des informations diffusées sur son site.</p>
                        <p>En conséquence, l'utilisateur reconnaît utiliser ces informations sous sa responsabilité exclusive.</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">5. Données personnelles</h2>
                    <div class="mb-5">
                        <p>Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition aux données personnelles vous concernant.</p>
                        <p>Pour exercer ces droits, vous pouvez nous contacter à l'adresse suivante : contact@automatehub.fr</p>
                        <p>Pour plus d'informations sur le traitement de vos données personnelles, consultez notre <a href="{{ route('privacy') }}">politique de confidentialité</a>.</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">6. Cookies</h2>
                    <div class="mb-5">
                        <p>Ce site utilise des cookies pour améliorer l'expérience utilisateur et analyser le trafic. En continuant à naviguer sur ce site, vous acceptez l'utilisation de cookies conformément à notre politique de confidentialité.</p>
                    </div>

                    <h2 class="fw-bold text-infinity-blue mb-4">7. Droit applicable</h2>
                    <div class="mb-5">
                        <p>Les présentes mentions légales sont régies par le droit français. En cas de litige, les tribunaux français seront seuls compétents.</p>
                    </div>

                    <div class="text-muted">
                        <p><small>Dernière mise à jour : {{ date('d/m/Y') }}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
