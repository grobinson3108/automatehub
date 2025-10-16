@extends('layouts.frontend')

<style>
    .pagination {
        --bs-pagination-padding-x: 1rem;
        --bs-pagination-padding-y: 0.6rem;
        --bs-pagination-border-radius: 0.5rem;
        --bs-pagination-hover-bg: #f8f9fa;
        --bs-pagination-focus-bg: #e9ecef;
        --bs-pagination-active-bg: #6ba3c3;
        --bs-pagination-active-border-color: #6ba3c3;
    }

    .pagination .page-link {
        font-weight: 500;
        color: #495057;
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        margin: 0 3px;
        transition: all 0.2s ease;
    }

    .pagination .page-item.active .page-link {
        color: white;
        box-shadow: 0 4px 10px rgba(107, 163, 195, 0.2);
    }

    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
    }

    .pagination .page-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .tag-badge {
        display: inline-block;
        padding: 4px 12px;
        margin: 2px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .tag-badge-automatisation {
        background-color: rgba(107, 163, 195, 0.1);
        color: #6ba3c3;
        border: 1px solid #6ba3c3;
    }

    .tag-badge-n8n {
        background-color: rgba(232, 180, 180, 0.1);
        color: #e8b4b4;
        border: 1px solid #e8b4b4;
    }

    .tag-badge-workflow {
        background-color: rgba(84, 180, 141, 0.1);
        color: #54b48d;
        border: 1px solid #54b48d;
    }

    .tag-badge-default {
        background-color: rgba(122, 156, 181, 0.1);
        color: #7a9cb5;
        border: 1px solid #7a9cb5;
    }

    .tag-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h1 class="display-4 fw-bold text-infinity-blue mb-4">Blog n8n</h1>
                <p class="lead mb-4">
                    Découvrez les dernières actualités, tutoriels et conseils sur n8n et l'automatisation.<br>
                    Restez informé des nouvelles fonctionnalités et des meilleures pratiques pour optimiser vos workflows.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @php
                // Articles factices pour la démonstration
                $articles = [
                    (object)[
                        'id' => 1,
                        'title' => 'Comment débuter avec n8n : Guide complet pour les débutants',
                        'slug' => 'comment-debuter-avec-n8n-guide-complet',
                        'excerpt' => 'Découvrez les bases de n8n et créez vos premiers workflows d\'automatisation en quelques étapes simples.',
                        'image' => null,
                        'published_at' => now()->subDays(2),
                        'tags' => ['n8n', 'automatisation', 'débutant', 'workflow']
                    ],
                    (object)[
                        'id' => 2,
                        'title' => 'Les 10 meilleures intégrations n8n pour votre entreprise',
                        'slug' => 'meilleures-integrations-n8n-entreprise',
                        'excerpt' => 'Explorez les intégrations n8n les plus populaires qui peuvent transformer votre productivité.',
                        'image' => null,
                        'published_at' => now()->subDays(5),
                        'tags' => ['n8n', 'intégrations', 'productivité', 'entreprise']
                    ],
                    (object)[
                        'id' => 3,
                        'title' => 'Automatiser votre marketing avec n8n : Cas pratiques',
                        'slug' => 'automatiser-marketing-n8n-cas-pratiques',
                        'excerpt' => 'Apprenez à automatiser vos campagnes marketing et à optimiser votre funnel de conversion.',
                        'image' => null,
                        'published_at' => now()->subWeek(),
                        'tags' => ['n8n', 'marketing', 'automatisation', 'conversion']
                    ],
                    (object)[
                        'id' => 4,
                        'title' => 'Gestion des erreurs dans n8n : Bonnes pratiques',
                        'slug' => 'gestion-erreurs-n8n-bonnes-pratiques',
                        'excerpt' => 'Découvrez comment gérer efficacement les erreurs dans vos workflows n8n.',
                        'image' => null,
                        'published_at' => now()->subWeeks(2),
                        'tags' => ['n8n', 'erreurs', 'workflow', 'bonnes-pratiques']
                    ],
                    (object)[
                        'id' => 5,
                        'title' => 'n8n vs Zapier : Comparaison complète 2025',
                        'slug' => 'n8n-vs-zapier-comparaison-2025',
                        'excerpt' => 'Analyse détaillée des avantages et inconvénients de n8n par rapport à Zapier.',
                        'image' => null,
                        'published_at' => now()->subWeeks(3),
                        'tags' => ['n8n', 'zapier', 'comparaison', 'automatisation']
                    ],
                    (object)[
                        'id' => 6,
                        'title' => 'Créer des webhooks personnalisés avec n8n',
                        'slug' => 'creer-webhooks-personnalises-n8n',
                        'excerpt' => 'Guide pratique pour créer et utiliser des webhooks dans vos workflows n8n.',
                        'image' => null,
                        'published_at' => now()->subMonth(),
                        'tags' => ['n8n', 'webhooks', 'api', 'développement']
                    ]
                ];
            @endphp

            @forelse($articles as $article)
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                    <div class="card h-100 border-0 shadow-sm">
                        @if($article->image)
                            <img src="{{ $article->image }}" class="card-img-top" alt="{{ $article->title }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <div class="text-center">
                                    <i class="fas fa-file-alt fa-3x text-infinity-blue mb-2"></i>
                                    <p class="text-muted mb-0">Article n8n</p>
                                </div>
                            </div>
                        @endif
                        <div class="card-body">
                            <h3 class="h5 fw-bold mb-3">{{ $article->title }}</h3>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-calendar-alt me-1"></i> {{ $article->published_at->format('d/m/Y') }}
                            </p>
                            <p>{{ $article->excerpt }}</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-4">
                            <a href="{{ route('blog.show', $article->slug) }}" class="btn btn-outline-primary rounded-pill px-4">
                                Lire la suite
                            </a>
                        </div>
                        @if(isset($article->tags) && is_array($article->tags) && count($article->tags) > 0)
                            <div class="mb-3 mt-2 p-4">
                                @foreach($article->tags as $articleTag)
                                    @php
                                        // Convertir le tag en slug pour la classe CSS
                                        $tagSlug = \Illuminate\Support\Str::slug($articleTag);
                                    @endphp
                                    <a href="{{ route('blog.tag', $articleTag) }}" 
                                    class="text-decoration-none tag-badge tag-badge-{{ $tagSlug }} tag-badge-{{ !in_array($tagSlug, ['automatisation', 'n8n', 'workflow', 'productivite', 'integration', 'marketing', 'api', 'webhooks']) ? 'default' : $tagSlug }}">
                                        #{{ $articleTag }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p>Aucun article disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
        
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Navigation des articles">
                <ul class="pagination pagination-lg">
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">&laquo;</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" rel="next" aria-label="Suivant">&raquo;</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="text-center mt-3 text-muted">
            <small>Affichage 1 à 6 sur 18 articles</small>
        </div>
    </div>
</section>
@endsection
