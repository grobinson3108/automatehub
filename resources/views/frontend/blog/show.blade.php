@extends('layouts.frontend')

@section('title', 'Article de blog - Automatehub')
@section('meta_description', 'Article détaillé sur n8n et l\'automatisation')

@section('hero')
<div class="bg-primary-dark">
    <div class="content content-full text-center pt-7 pb-5">
        <h1 class="h2 text-white mb-2">
            Article : {{ $slug }}
        </h1>
        <h2 class="h4 fw-normal text-white-75">
            Actualités, conseils et astuces sur n8n et l'automatisation
        </h2>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-xl-8">
        <div class="block block-rounded">
            <div class="block-content">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h3>{{ $slug }}</h3>
                        <div class="fs-sm fw-medium mb-2">
                            <span class="text-muted">Publié le 23 mai 2025</span>
                            <span class="text-muted ms-2">par Jean Dupont</span>
                            <span class="text-muted ms-2">dans Automatisation</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('blog.index') }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-fw fa-arrow-left me-1"></i> Retour au blog
                        </a>
                    </div>
                </div>
                
                <div class="mb-5">
                    <img class="img-fluid rounded" src="https://via.placeholder.com/1200x600?text=Blog+n8n" alt="Article de blog n8n">
                </div>
                
                <p class="fs-lg">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus.
                </p>
                
                <p>
                    Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate.
                </p>
                
                <p>
                    Integer hendrerit dignissim lorem, id scelerisque antequam odio. Morbi convallis imperdiet dolor, eget hendrerit erat lobortis malesuada. Quisque fringilla, nunc at tincidunt consequat, nibh massa ultrices estibulum felis enim cursus lorem, vitae consequat ligula est a ligula.
                </p>
                
                <div class="content-heading">Les avantages de n8n</div>
                
                <p>
                    Curabitur molestie eros urna, eleifend molestie risus placerat sed. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus.
                </p>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <ul class="fa-ul">
                            <li class="mb-2">
                                <span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                <span class="fw-medium">Open source</span> - Code source ouvert et communauté active
                            </li>
                            <li class="mb-2">
                                <span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                <span class="fw-medium">Auto-hébergement</span> - Contrôle total sur vos données
                            </li>
                            <li class="mb-2">
                                <span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                <span class="fw-medium">Flexibilité</span> - Personnalisation avancée des workflows
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="fa-ul">
                            <li class="mb-2">
                                <span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                <span class="fw-medium">Intégrations</span> - Plus de 200 services supportés
                            </li>
                            <li class="mb-2">
                                <span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                <span class="fw-medium">Interface visuelle</span> - Création de workflows intuitive
                            </li>
                            <li class="mb-2">
                                <span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                <span class="fw-medium">Fonctions JavaScript</span> - Transformations de données avancées
                            </li>
                        </ul>
                    </div>
                </div>
                
                <p>
                    Fusce sagittis, libero non molestie mollis, magna orci ultrices dolor, at vulputate neque nulla lacinia eros. Sed id ligula quis est convallis tempor. Curabitur molestie eros urna, eleifend molestie risus placerat sed.
                </p>
                
                <div class="content-heading">Conclusion</div>
                
                <p>
                    Donec ornare turpis non ullamcorper pulvinar. Aenean in mauris dignissim, imperdiet tellus eu, gravida quam. Suspendisse non sem facilisis, ullamcorper odio eu, volutpat neque. Morbi id nunc ut velit consectetur tristique. Ut vulputate sollicitudin odio quis viverra.
                </p>
                
                <hr class="my-5">
                
                <div class="row">
                    <div class="col-md-6">
                        <h4>Tags</h4>
                        <a href="#" class="btn btn-sm btn-alt-primary me-1 mb-1">n8n</a>
                        <a href="#" class="btn btn-sm btn-alt-primary me-1 mb-1">Automatisation</a>
                        <a href="#" class="btn btn-sm btn-alt-primary me-1 mb-1">Workflow</a>
                        <a href="#" class="btn btn-sm btn-alt-primary me-1 mb-1">Productivité</a>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h4>Partager</h4>
                        <a href="#" class="btn btn-sm btn-alt-primary me-1">
                            <i class="fab fa-fw fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-alt-primary me-1">
                            <i class="fab fa-fw fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-alt-primary me-1">
                            <i class="fab fa-fw fa-linkedin-in"></i>
                        </a>
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
                <h3 class="mb-4">Articles similaires</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex py-3">
                            <div class="flex-shrink-0">
                                <img class="img-avatar" src="https://via.placeholder.com/48x48?text=n8n" alt="Article de blog n8n">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <a class="fw-semibold" href="#">Les nouveautés de n8n en 2025</a>
                                <div class="fs-sm text-muted">Découvrez les dernières fonctionnalités ajoutées à n8n et comment elles peuvent améliorer vos workflows.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex py-3">
                            <div class="flex-shrink-0">
                                <img class="img-avatar" src="https://via.placeholder.com/48x48?text=n8n" alt="Article de blog n8n">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <a class="fw-semibold" href="#">5 workflows n8n pour gagner du temps</a>
                                <div class="fs-sm text-muted">Voici 5 workflows n8n qui vous feront gagner des heures chaque semaine dans votre travail quotidien.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
