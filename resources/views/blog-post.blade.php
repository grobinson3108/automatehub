@extends('layouts.app')

@section('title', $seo['title'] ?? ($post->title . ' | Blog Automatehub'))
@section('description', $seo['meta_description'] ?? $post->excerpt)

@push('styles')
@if(isset($seo['meta_tags']))
    {{-- SEO Meta Tags from n8n article_data --}}
    @foreach($seo['meta_tags'] as $tag)
        @if(isset($tag['name']))
            <meta name="{{ $tag['name'] }}" content="{{ $tag['content'] }}">
        @elseif(isset($tag['property']))
            <meta property="{{ $tag['property'] }}" content="{{ $tag['content'] }}">
        @endif
    @endforeach
@else
    {{-- Fallback SEO Meta Tags --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ $post->excerpt }}">
    <meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
    @if($post->featured_image)
        <meta property="og:image" content="{{ $post->featured_image }}">
    @endif
    <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    @if($post->author)
        <meta property="article:author" content="{{ $post->author->name ?? 'Automatehub' }}">
    @endif

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->title }}">
    <meta name="twitter:description" content="{{ $post->excerpt }}">
    @if($post->featured_image)
        <meta name="twitter:image" content="{{ $post->featured_image }}">
    @endif
@endif

@if(isset($seo['schema_org']))
    {{-- Schema.org JSON-LD from n8n article_data --}}
    <script type="application/ld+json">
    {!! json_encode($seo['schema_org'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@else
    {{-- Fallback Schema.org Article --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "{{ $post->title }}",
        "description": "{{ $post->excerpt }}",
        "datePublished": "{{ $post->published_at->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}",
        "author": {
            "@type": "Person",
            "name": "{{ $post->author->name ?? 'Automatehub' }}"
        },
        @if($post->featured_image)
        "image": "{{ $post->featured_image }}",
        @endif
        "publisher": {
            "@type": "Organization",
            "name": "Automatehub",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/logo/Logo_250.png') }}"
            }
        }
    }
    </script>
@endif

<style>
    .hero-article {
        position: relative;
        background: linear-gradient(135deg, var(--infinity-blue) 0%, var(--blue-medium) 100%);
        padding: 120px 0 80px;
        overflow: hidden;
    }

    .hero-article::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,138.7C672,128,768,128,864,138.7C960,149,1056,171,1152,165.3C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
        background-size: cover;
        opacity: 0.3;
    }

    .hero-article .container {
        position: relative;
        z-index: 1;
    }

    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: center;
        margin-bottom: 30px;
        padding: 15px 0;
        border-bottom: 2px solid rgba(0,0,0,0.1);
    }

    .article-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 0.95rem;
    }

    .article-meta-item i {
        color: var(--infinity-blue);
    }

    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }

    .article-content h2,
    .article-content h3,
    .article-content h4 {
        color: var(--infinity-blue);
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin: 2rem 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .article-content blockquote {
        border-left: 4px solid var(--infinity-blue);
        padding-left: 20px;
        margin: 2rem 0;
        font-style: italic;
        color: #555;
    }

    .article-content ul,
    .article-content ol {
        margin: 1.5rem 0;
        padding-left: 2rem;
    }

    .article-content li {
        margin-bottom: 0.5rem;
    }

    .article-content code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }

    .article-content pre {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        overflow-x: auto;
        margin: 2rem 0;
    }

    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 30px;
    }

    .tag-badge {
        display: inline-block;
        padding: 8px 16px;
        background: linear-gradient(135deg, var(--infinity-blue), var(--blue-medium));
        color: white;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: transform 0.2s;
    }

    .tag-badge:hover {
        transform: translateY(-2px);
        color: white;
    }

    .share-buttons {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .share-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: var(--infinity-blue);
        color: white;
        text-decoration: none;
        transition: all 0.3s;
    }

    .share-btn:hover {
        transform: scale(1.1);
        color: white;
    }

    .related-articles {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 30px;
        margin-top: 50px;
    }

    .related-article-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
        text-decoration: none;
        display: block;
        color: inherit;
    }

    .related-article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        color: inherit;
    }

    .cta-section {
        background: linear-gradient(135deg, var(--infinity-blue), var(--blue-medium));
        color: white;
        border-radius: 15px;
        padding: 40px;
        margin: 50px 0;
        text-align: center;
    }

    .cta-section h3 {
        color: white;
        margin-bottom: 20px;
    }

    .btn-cta {
        background: white;
        color: var(--infinity-blue);
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
    }

    .btn-cta:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        color: var(--infinity-blue);
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 30px;
    }

    .breadcrumb-item a {
        color: white;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: rgba(255,255,255,0.8);
    }
</style>
@endpush

@section('content')
{{-- Hero Section --}}
<section class="hero-article">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 50) }}</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="display-4 fw-bold text-white mb-4">{{ $post->title }}</h1>

                @if($post->excerpt)
                <p class="lead text-white mb-4">{{ $post->excerpt }}</p>
                @endif

                <div class="article-meta text-white">
                    @if($post->author)
                    <div class="article-meta-item">
                        <i class="fas fa-user"></i>
                        <span>{{ $post->author->name ?? 'Automatehub' }}</span>
                    </div>
                    @endif

                    <div class="article-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $post->published_at->format('d F Y') }}</span>
                    </div>

                    @if($post->category)
                    <div class="article-meta-item">
                        <i class="fas fa-folder"></i>
                        <span>{{ $post->category->name }}</span>
                    </div>
                    @endif

                    <div class="article-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $post->reading_time }} min de lecture</span>
                    </div>

                    @if($post->views_count)
                    <div class="article-meta-item">
                        <i class="fas fa-eye"></i>
                        <span>{{ number_format($post->views_count) }} vues</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Featured Image --}}
@if($post->featured_image)
<section class="py-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="img-fluid rounded shadow-lg" style="margin-top: -50px; position: relative; z-index: 10;">
            </div>
        </div>
    </div>
</section>
@endif

{{-- Article Content --}}
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="article-content">
                    {!! $post->content !!}
                </article>

                {{-- CTA Section from article_data if available --}}
                @if(isset($seo['cta']))
                <div class="cta-section">
                    <h3>{{ $seo['cta']['title'] ?? 'Prêt à automatiser vos workflows ?' }}</h3>
                    <p class="mb-4">{{ $seo['cta']['description'] ?? 'Découvrez comment n8n peut transformer votre productivité' }}</p>
                    <a href="{{ $seo['cta']['url'] ?? route('workflows.index') }}" class="btn-cta">
                        {{ $seo['cta']['button_text'] ?? 'Découvrir nos workflows' }}
                    </a>
                </div>
                @endif

                {{-- Tags and Sharing --}}
                <div class="row mt-5 pt-4 border-top">
                    <div class="col-md-6">
                        @if($post->tags && count($post->tags) > 0)
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-tags text-infinity-blue me-2"></i>Tags
                        </h5>
                        <div class="tags-container">
                            @foreach($post->tags as $tag)
                            <a href="#" class="tag-badge">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-share-alt text-infinity-blue me-2"></i>Partager
                        </h5>
                        <div class="share-buttons justify-content-md-end">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" class="share-btn" aria-label="Partager sur Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-btn" aria-label="Partager sur Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" class="share-btn" aria-label="Partager sur LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode(route('blog.show', $post->slug)) }}" class="share-btn" aria-label="Partager par email">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Related Articles --}}
@if(isset($relatedPosts) && count($relatedPosts) > 0)
<section class="pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="related-articles">
                    <h3 class="fw-bold mb-4">
                        <i class="fas fa-newspaper text-infinity-blue me-2"></i>Articles similaires
                    </h3>
                    <div class="row">
                        @foreach($relatedPosts as $related)
                        <div class="col-md-6">
                            <a href="{{ route('blog.show', $related->slug) }}" class="related-article-card">
                                @if($related->featured_image)
                                <img src="{{ $related->featured_image }}" alt="{{ $related->title }}" class="img-fluid rounded mb-3" style="height: 150px; width: 100%; object-fit: cover;">
                                @endif
                                <h5 class="fw-bold">{{ $related->title }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($related->excerpt, 100) }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>{{ $related->published_at->format('d M Y') }}
                                    <i class="fas fa-clock ms-3 me-1"></i>{{ $related->reading_time }} min
                                </small>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
    // Increment view count via AJAX
    fetch('{{ route("blog.show", $post->slug) }}/increment-view', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });
</script>
@endpush
