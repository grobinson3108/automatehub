@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-4">Mentions Légales</h1>
            <p class="text-muted">En vigueur au {{ date('d/m/Y') }}</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">1. Éditeur du site</h2>
                    <p><strong>AutomateHub</strong><br>
                    Plateforme d'apprentissage et d'automatisation n8n<br>
                    Site web : https://automatehub.fr<br>
                    Email : contact@automatehub.fr</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">2. Hébergement</h2>
                    <p><strong>OVH</strong><br>
                    2 rue Kellermann<br>
                    59100 Roubaix - France<br>
                    Téléphone : 1007<br>
                    Site : https://www.ovh.com</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">3. Directeur de la publication</h2>
                    <p>Le directeur de la publication est le responsable d'AutomateHub.<br>
                    Contact : direction@automatehub.fr</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">4. Propriété intellectuelle</h2>
                    <p>L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.</p>
                    
                    <p>La reproduction de tout ou partie de ce site sur quelque support que ce soit est formellement interdite sauf autorisation expresse du directeur de la publication.</p>
                    
                    <p>Les marques et logos figurant sur ce site sont des marques déposées. Leur mention n'accorde en aucune manière une licence ou un droit d'utilisation quelconque des dites marques, qui ne peuvent donc être utilisées sans le consentement préalable et écrit du propriétaire de la marque.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">5. Protection des données personnelles</h2>
                    <p>Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez de droits sur vos données personnelles.</p>
                    
                    <p>Pour plus d'informations, consultez notre <a href="{{ route('privacy-policy') }}">Politique de Confidentialité</a>.</p>
                    
                    <p>Pour exercer vos droits : privacy@automatehub.fr</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">6. Cookies</h2>
                    <p>Ce site utilise des cookies pour améliorer l'expérience utilisateur. En continuant à naviguer sur ce site, vous acceptez l'utilisation de cookies conformément à notre politique de cookies.</p>
                    
                    <p>Types de cookies utilisés :</p>
                    <ul>
                        <li>Cookies essentiels au fonctionnement du site</li>
                        <li>Cookies de mesure d'audience (avec consentement)</li>
                        <li>Cookies de préférences utilisateur</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">7. Responsabilité</h2>
                    <p>Les informations contenues sur ce site sont aussi précises que possible et le site est périodiquement remis à jour, mais peut toutefois contenir des inexactitudes, des omissions ou des lacunes.</p>
                    
                    <p>AutomateHub ne pourra être tenu responsable des dommages directs et indirects causés au matériel de l'utilisateur, lors de l'accès au site, résultant de l'utilisation d'un matériel ne répondant pas aux spécifications requises, ou de l'apparition d'un bug ou d'une incompatibilité.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">8. Liens hypertextes</h2>
                    <p>Les liens hypertextes mis en place dans le cadre du présent site internet en direction d'autres ressources présentes sur le réseau Internet ne sauraient engager la responsabilité d'AutomateHub.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">9. Droit applicable et juridiction</h2>
                    <p>Tout litige en relation avec l'utilisation du site AutomateHub est soumis au droit français. En cas de litige, les tribunaux français seront seuls compétents.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">10. Contact</h2>
                    <p>Pour toute question ou demande d'information concernant le site, ou tout signalement de contenu ou d'activités illicites, l'utilisateur peut contacter l'éditeur à l'adresse suivante : contact@automatehub.fr</p>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('home') }}" class="btn btn-primary">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</div>
@endsection