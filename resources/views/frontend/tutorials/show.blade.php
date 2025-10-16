@extends('layouts.frontend')

@section('title', 'Tutoriel - Automatehub')
@section('meta_description', 'Tutoriel détaillé sur n8n')

@section('hero')
<div class="bg-primary-dark">
    <div class="content content-full text-center pt-7 pb-5">
        <h1 class="h2 text-white mb-2">
            Tutoriel : {{ $slug }}
        </h1>
        <h2 class="h4 fw-normal text-white-75">
            Apprenez à maîtriser n8n avec nos tutoriels détaillés
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
                        <h3>Tutoriel : {{ $slug }}</h3>
                        <div class="fs-sm fw-medium mb-2">
                            <span class="badge bg-success">Gratuit</span>
                            <span class="text-muted ms-2">Débutant</span>
                            <span class="text-muted ms-2">Publié le 23 mai 2025</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('tutorials.index') }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-fw fa-arrow-left me-1"></i> Retour aux tutoriels
                        </a>
                    </div>
                </div>
                
                <div class="mb-5">
                    <img class="img-fluid rounded" src="https://via.placeholder.com/1200x600?text=Tutoriel+n8n" alt="Tutoriel n8n">
                </div>
                
                <div class="content-heading">Introduction</div>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus.
                </p>
                <p>
                    Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus.
                </p>
                
                <div class="content-heading">Étape 1 : Configuration</div>
                <p>
                    Nam efficitur, massa quis fringilla volutpat, ipsum massa consequat nisi, sed eleifend orci sem sodales lorem. Curabitur molestie eros urna, eleifend molestie risus placerat sed.
                </p>
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fa fa-fw fa-info-circle me-2"></i>
                    <p class="mb-0">Assurez-vous d'avoir installé n8n avant de commencer ce tutoriel.</p>
                </div>
                <pre class="bg-body-dark p-3 rounded"><code>npm install n8n -g</code></pre>
                
                <div class="content-heading">Étape 2 : Création du workflow</div>
                <p>
                    Fusce sagittis, libero non molestie mollis, magna orci ultrices dolor, at vulputate neque nulla lacinia eros. Sed id ligula quis est convallis tempor.
                </p>
                <p>
                    Curabitur molestie eros urna, eleifend molestie risus placerat sed. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor.
                </p>
                
                <div class="content-heading">Conclusion</div>
                <p>
                    Donec ornare turpis non ullamcorper pulvinar. Aenean in mauris dignissim, imperdiet tellus eu, gravida quam. Suspendisse non sem facilisis, ullamcorper odio eu, volutpat neque.
                </p>
                
                <hr class="my-5">
                
                <div class="row">
                    <div class="col-md-6">
                        <h4>Téléchargements</h4>
                        <p>Téléchargez les ressources associées à ce tutoriel :</p>
                        <a href="#" class="btn btn-alt-primary">
                            <i class="fa fa-fw fa-download me-1"></i> Workflow n8n
                        </a>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h4>Partager</h4>
                        <p>Partagez ce tutoriel avec vos amis :</p>
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
                <h3 class="mb-4">Tutoriels similaires</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex py-3">
                            <div class="flex-shrink-0">
                                <img class="img-avatar" src="https://via.placeholder.com/48x48?text=n8n" alt="Tutoriel n8n">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <a class="fw-semibold" href="#">Introduction à n8n</a>
                                <div class="fs-sm text-muted">Découvrez les bases de n8n et apprenez à créer votre premier workflow.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex py-3">
                            <div class="flex-shrink-0">
                                <img class="img-avatar" src="https://via.placeholder.com/48x48?text=n8n" alt="Tutoriel n8n">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <a class="fw-semibold" href="#">Automatisation des emails</a>
                                <div class="fs-sm text-muted">Apprenez à automatiser l'envoi et la réception d'emails avec n8n.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
