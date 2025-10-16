import { Head, Link, router } from "@inertiajs/react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  ArrowRight,
  Star,
  Zap,
  TrendingUp,
  Users,
  Eye,
  ShoppingCart,
  Filter,
  Package,
  Sparkles,
  Bot,
  TrendingUp as TrendingUpIcon,
  Briefcase
} from "lucide-react";

interface Pack {
  id: number;
  slug: string;
  name: string;
  marketing_title: string;
  tagline: string;
  price_eur: number;
  price_usd: number;
  original_price_eur: number | null;
  original_price_usd: number | null;
  category: string;
  complexity: string;
  workflows_count: number;
  views_count: number;
  sales_count: number;
  is_featured: boolean;
}

interface Props {
  packs: {
    data: Pack[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  categories: string[];
  currentCategory?: string;
  currentSort: string;
}

const categoryIcons: Record<string, any> = {
  crypto: "₿",
  ia: <Bot className="size-4" />,
  marketing: <TrendingUpIcon className="size-4" />,
  business: <Briefcase className="size-4" />,
};

const categoryColors: Record<string, string> = {
  crypto: "from-blue-500 to-cyan-500",
  ia: "from-purple-500 to-pink-500",
  marketing: "from-green-500 to-emerald-500",
  business: "from-orange-500 to-amber-500",
};

const getDiscountPercentage = (pack: Pack): number => {
  if (!pack.original_price_eur || pack.original_price_eur <= pack.price_eur) {
    return 0;
  }
  return Math.round(((pack.original_price_eur - pack.price_eur) / pack.original_price_eur) * 100);
};

export default function PacksIndex({ packs, categories, currentCategory, currentSort }: Props) {
  const handleCategoryClick = (category: string | null) => {
    const params: Record<string, any> = { sort: currentSort };
    if (category) {
      params.category = category;
    }
    router.get(route('packs.index'), params, { preserveState: true });
  };

  const handleSortClick = (sort: string) => {
    const params: Record<string, any> = { sort };
    if (currentCategory) {
      params.category = currentCategory;
    }
    router.get(route('packs.index'), params, { preserveState: true });
  };

  return (
    <>
      <Head title="Packs Workflows Premium - AutomateHub" />

      <div className="min-h-screen bg-background text-foreground">
        <main>
          {/* Hero Section */}
          <section className="container py-24 sm:py-32 relative">
            <div className="absolute top-20 left-1/2 transform -translate-x-1/2 w-[70%] h-60 bg-primary/50 rounded-full blur-3xl opacity-30"></div>

            <div className="text-center space-y-8 relative z-10">
              <Badge variant="outline" className="text-sm py-2">
                <span className="mr-2 text-primary">
                  <Badge>
                    <Package className="size-3" />
                  </Badge>
                </span>
                <span>34 Packs Premium Disponibles</span>
              </Badge>

              <div className="max-w-screen-md mx-auto">
                <h1 className="text-4xl md:text-6xl font-bold">
                  Packs{" "}
                  <span className="text-transparent bg-gradient-to-r from-primary to-orange-600 bg-clip-text">
                    Workflows Premium
                  </span>
                </h1>
              </div>

              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Des collections complètes de workflows n8n prêts à l'emploi pour automatiser votre business
              </p>
            </div>
          </section>

          {/* Filters */}
          <section className="bg-muted/30 border-y border-border py-6">
            <div className="container">
              {/* Category Filters */}
              <div className="flex flex-wrap justify-center gap-3 mb-6">
                <Button
                  variant={!currentCategory ? "default" : "outline"}
                  onClick={() => handleCategoryClick(null)}
                >
                  Tous les Packs
                </Button>
                {categories.map((category) => (
                  <Button
                    key={category}
                    variant={currentCategory === category ? "default" : "outline"}
                    onClick={() => handleCategoryClick(category)}
                  >
                    <span className="mr-2">{categoryIcons[category]}</span>
                    {category.charAt(0).toUpperCase() + category.slice(1)}
                  </Button>
                ))}
              </div>

              {/* Sort Options */}
              <div className="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                <span>Trier par:</span>
                <button
                  onClick={() => handleSortClick('featured')}
                  className={`hover:text-primary transition-colors ${currentSort === 'featured' ? 'font-bold text-primary' : ''}`}
                >
                  Recommandés
                </button>
                <span>|</span>
                <button
                  onClick={() => handleSortClick('popular')}
                  className={`hover:text-primary transition-colors ${currentSort === 'popular' ? 'font-bold text-primary' : ''}`}
                >
                  Populaires
                </button>
                <span>|</span>
                <button
                  onClick={() => handleSortClick('price_low')}
                  className={`hover:text-primary transition-colors ${currentSort === 'price_low' ? 'font-bold text-primary' : ''}`}
                >
                  Prix croissant
                </button>
                <span>|</span>
                <button
                  onClick={() => handleSortClick('price_high')}
                  className={`hover:text-primary transition-colors ${currentSort === 'price_high' ? 'font-bold text-primary' : ''}`}
                >
                  Prix décroissant
                </button>
              </div>
            </div>
          </section>

          {/* Packs Grid */}
          <section className="container py-16">
            {packs.data.length > 0 ? (
              <>
                <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                  {packs.data.map((pack) => {
                    const discount = getDiscountPercentage(pack);
                    return (
                      <Link
                        key={pack.id}
                        href={route('packs.show', pack.slug)}
                        className="group"
                      >
                        <Card className="h-full border-primary/20 hover:border-primary/40 transition-all hover:shadow-lg">
                          {/* Header with gradient */}
                          <div className={`bg-gradient-to-r ${categoryColors[pack.category]} p-4 text-white`}>
                            <div className="flex justify-between items-center">
                              <div className="flex items-center gap-2">
                                <span>{categoryIcons[pack.category]}</span>
                                <span className="font-semibold capitalize">{pack.category}</span>
                              </div>
                              {pack.is_featured && (
                                <Badge className="bg-yellow-400 text-yellow-900 hover:bg-yellow-500">
                                  <Star className="size-3 mr-1" />
                                  Premium
                                </Badge>
                              )}
                            </div>
                          </div>

                          <CardHeader>
                            <CardTitle className="line-clamp-2 group-hover:text-primary transition-colors">
                              {pack.name}
                            </CardTitle>
                            <p className="text-sm text-muted-foreground line-clamp-2 mt-2">
                              {pack.tagline}
                            </p>
                          </CardHeader>

                          <CardContent>
                            {/* Stats */}
                            <div className="flex justify-between text-sm text-muted-foreground mb-4">
                              <span className="flex items-center gap-1">
                                <Zap className="size-4" />
                                {pack.workflows_count} workflows
                              </span>
                              <span className="flex items-center gap-1">
                                <Eye className="size-4" />
                                {pack.views_count.toLocaleString()}
                              </span>
                            </div>

                            {/* Complexity Badge */}
                            {pack.complexity && (
                              <div className="mb-4">
                                <Badge
                                  variant={
                                    pack.complexity === 'Débutant' ? 'default' :
                                    pack.complexity === 'Intermédiaire' ? 'secondary' :
                                    'destructive'
                                  }
                                >
                                  {pack.complexity}
                                </Badge>
                              </div>
                            )}

                            {/* Price */}
                            <div className="flex justify-between items-end pt-4 border-t border-border">
                              <div>
                                {discount > 0 && (
                                  <div className="flex items-center gap-2 mb-1">
                                    <span className="text-sm text-muted-foreground line-through">
                                      {Math.floor(pack.original_price_eur!)}€
                                    </span>
                                    <Badge variant="default" className="bg-green-600">
                                      -{discount}%
                                    </Badge>
                                  </div>
                                )}
                                <p className="text-3xl font-bold text-primary">
                                  {Math.floor(pack.price_eur)}€
                                </p>
                              </div>
                              <Button size="sm" variant="ghost" className="group-hover:bg-primary group-hover:text-primary-foreground">
                                Voir
                                <ArrowRight className="size-4 ml-1 group-hover:translate-x-1 transition-transform" />
                              </Button>
                            </div>
                          </CardContent>
                        </Card>
                      </Link>
                    );
                  })}
                </div>

                {/* Pagination */}
                {packs.last_page > 1 && (
                  <div className="mt-12 flex justify-center gap-2">
                    {Array.from({ length: packs.last_page }, (_, i) => i + 1).map((page) => (
                      <Button
                        key={page}
                        variant={page === packs.current_page ? "default" : "outline"}
                        size="sm"
                        onClick={() => {
                          const params: Record<string, any> = { page, sort: currentSort };
                          if (currentCategory) params.category = currentCategory;
                          router.get(route('packs.index'), params, { preserveState: true });
                        }}
                      >
                        {page}
                      </Button>
                    ))}
                  </div>
                )}
              </>
            ) : (
              <div className="text-center py-20">
                <Filter className="size-16 mx-auto text-muted-foreground mb-4" />
                <h3 className="text-2xl font-bold mb-2">Aucun pack trouvé</h3>
                <p className="text-muted-foreground mb-6">Essayez de modifier vos filtres</p>
                <Button onClick={() => handleCategoryClick(null)}>
                  Voir tous les packs
                </Button>
              </div>
            )}
          </section>

          {/* Stats Section */}
          <section className="bg-muted/30 py-24 sm:py-32">
            <div className="container">
              <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                  <div className="bg-primary/20 p-3 rounded-full ring-8 ring-primary/10 mb-4 text-primary w-fit mx-auto">
                    <Package className="size-6" />
                  </div>
                  <p className="text-4xl md:text-5xl font-bold text-primary mb-2">34</p>
                  <p className="text-muted-foreground">Packs Premium</p>
                </div>
                <div>
                  <div className="bg-primary/20 p-3 rounded-full ring-8 ring-primary/10 mb-4 text-primary w-fit mx-auto">
                    <Zap className="size-6" />
                  </div>
                  <p className="text-4xl md:text-5xl font-bold text-primary mb-2">580+</p>
                  <p className="text-muted-foreground">Workflows Totaux</p>
                </div>
                <div>
                  <div className="bg-primary/20 p-3 rounded-full ring-8 ring-primary/10 mb-4 text-primary w-fit mx-auto">
                    <Users className="size-6" />
                  </div>
                  <p className="text-4xl md:text-5xl font-bold text-primary mb-2">300+</p>
                  <p className="text-muted-foreground">Clients Satisfaits</p>
                </div>
                <div>
                  <div className="bg-primary/20 p-3 rounded-full ring-8 ring-primary/10 mb-4 text-primary w-fit mx-auto">
                    <Star className="size-6" />
                  </div>
                  <p className="text-4xl md:text-5xl font-bold text-primary mb-2">4.9/5</p>
                  <p className="text-muted-foreground">Note Moyenne</p>
                </div>
              </div>
            </div>
          </section>

          {/* CTA Section */}
          <section className="container py-24 sm:py-32">
            <div className="bg-primary/10 border border-primary/20 rounded-2xl p-12 text-center">
              <div className="bg-primary/20 p-4 rounded-full ring-8 ring-primary/10 mb-6 text-primary w-fit mx-auto">
                <Sparkles className="size-8" />
              </div>
              <h2 className="text-3xl md:text-4xl font-bold mb-4 text-foreground">
                Besoin d'un Pack Personnalisé ?
              </h2>
              <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                Contactez-nous pour créer un pack sur mesure adapté à vos besoins spécifiques
              </p>
              <Button size="lg">
                <ShoppingCart className="size-5 mr-2" />
                Nous Contacter
              </Button>
            </div>
          </section>
        </main>

        <footer className="border-t border-border py-8">
          <div className="container text-center text-muted-foreground">
            <p>© 2025 AutomateHub. Tous droits réservés.</p>
          </div>
        </footer>
      </div>
    </>
  );
}
