@extends('layouts.frontend')

@section('title', 'À propos - Automatehub')
@section('meta_description', 'Découvrez qui nous sommes et notre mission chez Automatehub')

@section('hero')
<div class="bg-primary-dark">
    <div class="content content-full text-center pt-7 pb-5">
        <h1 class="h2 text-white mb-2">
            À propos d'Automatehub
        </h1>
        <h2 class="h4 fw-normal text-white-75">
            Notre mission et notre équipe
        </h2>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-xl-8">
        <div class="block block-rounded">
            <div class="block-content">
                <h3 class="mb-4">Notre mission</h3>
                <p>
                    Automatehub est né d'une passion pour l'automatisation et d'une conviction : n8n est l'un des outils les plus puissants et flexibles pour automatiser des processus métier.
                </p>
                <p>
                    Notre mission est simple : rendre l'automatisation accessible à tous, des débutants aux experts, en fournissant des ressources éducatives de qualité, des tutoriels détaillés et des workflows prêts à l'emploi.
                </p>
                <p>
                    Nous croyons que l'automatisation est la clé pour libérer du temps, réduire les erreurs et permettre aux individus et aux entreprises de se concentrer sur ce qui compte vraiment : la créativité, l'innovation et la croissance.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10 col-xl-8">
        <div class="block block-rounded">
            <div class="block-content">
                <h3 class="mb-4">Notre histoire</h3>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            Automatehub a été fondé en 2024 par une équipe de passionnés d'automatisation qui utilisaient n8n quotidiennement dans leur travail. Face au manque de ressources éducatives en français sur cet outil, ils ont décidé de créer une plateforme dédiée.
                        </p>
                        <p>
                            Au départ simple blog partageant quelques tutoriels, Automatehub s'est rapidement développé pour devenir une plateforme complète d'apprentissage, répondant à une demande croissante de la communauté francophone.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            Aujourd'hui, Automatehub est fier de compter des milliers d'utilisateurs, du débutant curieux au professionnel chevronné, tous unis par la volonté d'optimiser leurs processus grâce à n8n.
                        </p>
                        <p>
                            Notre équipe s'est également agrandie, intégrant des experts en automatisation, des développeurs, des formateurs et des créateurs de contenu, tous dédiés à la mission d'Automatehub : démocratiser l'automatisation avec n8n.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10 col-xl-8">
        <div class="block block-rounded">
            <div class="block-content">
                <h3 class="mb-4 text-center">Notre équipe</h3>
                <div class="row items-push">
                    <div class="col-md-6 col-xl-4">
                        <div class="text-center">
                            <img class="img-avatar img-avatar96 mb-3" src="https://via.placeholder.com/96x96?text=Avatar" alt="Avatar">
                            <h4 class="mb-1">Jean Dupont</h4>
                            <p class="fs-sm fw-medium text-muted mb-2">Fondateur & CEO</p>
                            <div class="fs-sm">
                                <a class="btn btn-sm btn-alt-primary me-1" href="#">
                                    <i class="fab fa-fw fa-linkedin-in"></i>
                                </a>
                                <a class="btn btn-sm btn-alt-primary me-1" href="#">
                                    <i class="fab fa-fw fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="text-center">
                            <img class="img-avatar img-avatar96 mb-3" src="https://via.placeholder.com/96x96?text=Avatar" alt="Avatar">
                            <h4 class="mb-1">Marie Martin</h4>
                            <p class="fs-sm fw-medium text-muted mb-2">Responsable Contenu</p>
                            <div class="fs-sm">
                                <a class="btn btn-sm btn-alt-primary me-1" href="#">
                                    <i class="fab fa-fw fa-linkedin-in"></i>
                                </a>
                                <a class="btn btn-sm btn-alt-primary me-1" href="#">
                                    <i class="fab fa-fw fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="text-center">
                            <img class="img-avatar img-avatar96 mb-3" src="https://via.placeholder.com/96x96?text=Avatar" alt="Avatar">
                            <h4 class="mb-1">Pierre Durand</h4>
                            <p class="fs-sm fw-medium text-muted mb-2">Expert n8n</p>
                            <div class="fs-sm">
                                <a class="btn btn-sm btn-alt-primary me-1" href="#">
                                    <i class="fab fa-fw fa-linkedin-in"></i>
                                </a>
                                <a class="btn btn-sm btn-alt-primary me-1" href="#">
                                    <i class="fab fa-fw fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10 col-xl-8">
        <div class="block block-rounded">
            <div class="block-content">
                <h3 class="mb-4 text-center">Rejoignez-nous dans cette aventure !</h3>
                <p class="text-center">
                    Que vous soyez débutant ou expert, Automatehub est là pour vous accompagner dans votre parcours d'automatisation avec n8n.
                </p>
                <div class="text-center mt-4">
                    <a class="btn btn-lg btn-primary px-4 py-2" href="{{ route('register') }}">
                        <i class="fa fa-fw fa-rocket me-1"></i> Commencer maintenant
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
