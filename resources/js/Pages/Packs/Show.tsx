import { Head, Link, router } from "@inertiajs/react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";
import {
  ArrowRight,
  Star,
  Zap,
  Shield,
  Clock,
  CheckCircle2,
  Gift,
  Wrench,
  Eye,
  ShoppingBag,
  Flame,
  Bot,
  Link as LinkIcon
} from "lucide-react";
import { useState } from "react";

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
  description: string;
  category: string;
  complexity: string;
  workflows_count: number;
  views_count: number;
  sales_count: number;
  is_featured: boolean;
  features: string[];
  benefits: string[];
  requirements: string[];
}

interface Workflow {
  filename: string;
  name: string;
  nodes: number;
  connections: number;
  complexity: string;
}

interface Props {
  pack: Pack;
  currency: string;
  workflows: Workflow[];
  relatedPacks: Pack[];
}

const getDiscountPercentage = (pack: Pack): number => {
  if (!pack.original_price_eur || pack.original_price_eur <= pack.price_eur) {
    return 0;
  }
  return Math.round(((pack.original_price_eur - pack.price_eur) / pack.original_price_eur) * 100);
};

export default function PackShow({ pack, currency: initialCurrency, workflows, relatedPacks }: Props) {
  const [currency, setCurrency] = useState(initialCurrency);
  const discount = getDiscountPercentage(pack);

  const getPrice = () => {
    return currency === 'USD'
      ? Math.floor(pack.price_usd)
      : Math.floor(pack.price_eur);
  };

  const getCurrencySymbol = () => {
    return currency === 'USD' ? '$' : '€';
  };

  const handleCheckout = () => {
    router.post(route('packs.checkout', pack.slug), {
      currency: currency
    });
  };

  return (
    <>
      <Head title={`${pack.marketing_title} - AutomateHub`} />

      <div className="min-h-screen bg-background text-foreground">
        <main>
          {/* Hero Section */}
          <section className="container py-24 sm:py-32 relative">
            <div className="absolute top-20 left-1/2 transform -translate-x-1/2 w-[60%] h-80 bg-primary/50 rounded-full blur-3xl opacity-20"></div>

            <div className="grid lg:grid-cols-2 gap-12 items-center relative z-10">
              <div className="space-y-6">
                {pack.is_featured && (
                  <Badge className="bg-yellow-400 text-yellow-900 hover:bg-yellow-500">
                    <Star className="size-4 mr-2" />
                    Pack Premium - Meilleur Vendeur
                  </Badge>
                )}

                <h1 className="text-4xl md:text-5xl font-bold leading-tight">
                  {pack.marketing_title}
                </h1>

                <p className="text-xl text-muted-foreground">
                  {pack.tagline}
                </p>

                {/* Pricing */}
                <div className="space-y-4">
                  <div className="flex items-center gap-4">
                    {discount > 0 && pack.original_price_eur && (
                      <span className="text-2xl text-muted-foreground line-through">
                        {Math.floor(pack.original_price_eur)}€
                      </span>
                    )}
                    <span className="text-5xl md:text-6xl font-black text-transparent bg-gradient-to-r from-primary to-orange-600 bg-clip-text">
                      {getPrice()}{getCurrencySymbol()}
                    </span>
                    {discount > 0 && (
                      <Badge className="bg-green-600 text-lg px-3 py-1">
                        -{discount}%
                      </Badge>
                    )}
                  </div>

                  {/* Currency Toggle */}
                  <div className="flex gap-2">
                    <Button
                      variant={currency === 'EUR' ? 'default' : 'outline'}
                      onClick={() => setCurrency('EUR')}
                    >
                      EUR (€)
                    </Button>
                    <Button
                      variant={currency === 'USD' ? 'default' : 'outline'}
                      onClick={() => setCurrency('USD')}
                    >
                      USD ($)
                    </Button>
                  </div>

                  {/* CTA Button */}
                  <Button
                    size="lg"
                    className="w-full md:w-auto px-8 font-bold text-lg"
                    onClick={handleCheckout}
                  >
                    <ShoppingBag className="size-5 mr-2" />
                    Acheter Maintenant
                  </Button>

                  {/* Trust Badges */}
                  <div className="flex flex-wrap gap-6 text-sm text-muted-foreground pt-4">
                    <div className="flex items-center gap-2">
                      <Shield className="size-5 text-green-600" />
                      Paiement Sécurisé
                    </div>
                    <div className="flex items-center gap-2">
                      <Zap className="size-5 text-primary" />
                      Livraison Immédiate
                    </div>
                    <div className="flex items-center gap-2">
                      <CheckCircle2 className="size-5 text-blue-600" />
                      Garantie 30 jours
                    </div>
                  </div>

                  {/* Scarcity */}
                  <div className="bg-destructive/10 border border-destructive/20 rounded-lg p-4 inline-flex items-center gap-2">
                    <Flame className="size-5 text-destructive" />
                    <span className="text-sm">
                      Plus que <strong className="text-destructive">{Math.floor(Math.random() * 6) + 3} copies</strong> disponibles à ce prix
                    </span>
                  </div>
                </div>
              </div>

              {/* Right Column - Info Card */}
              <Card className="border-primary/20 shadow-xl">
                <CardContent className="p-8 text-center">
                  <div className="bg-primary/20 p-4 rounded-full ring-8 ring-primary/10 mb-6 text-primary w-fit mx-auto">
                    <Bot className="size-12" />
                  </div>
                  <h3 className="text-2xl font-bold mb-3">
                    {pack.workflows_count} Workflows Inclus
                  </h3>
                  <p className="text-muted-foreground mb-6">
                    Prêts à l'emploi, testés et optimisés pour votre business
                  </p>

                  <div className="grid grid-cols-2 gap-4">
                    <div className="bg-muted/50 rounded-lg p-4">
                      <Eye className="size-6 mx-auto text-primary mb-2" />
                      <p className="text-sm text-muted-foreground">
                        {pack.views_count.toLocaleString()} vues
                      </p>
                    </div>
                    <div className="bg-muted/50 rounded-lg p-4">
                      <ShoppingBag className="size-6 mx-auto text-green-600 mb-2" />
                      <p className="text-sm text-muted-foreground">
                        {pack.sales_count} ventes
                      </p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </section>

          {/* Workflows List */}
          <section className="container py-24 sm:py-32 border-t border-border">
            <div className="text-center mb-12">
              <Badge className="mb-4">
                <Zap className="size-3 mr-1" />
                Workflows Inclus
              </Badge>
              <h2 className="text-3xl md:text-4xl font-bold mb-4">
                <span className="text-transparent bg-gradient-to-r from-primary to-orange-600 bg-clip-text">
                  {pack.workflows_count} Workflows
                </span>{" "}
                Dans Ce Pack
              </h2>
              <p className="text-xl text-muted-foreground">
                Prêts à l'emploi et optimisés pour votre réussite
              </p>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              {workflows.map((workflow, index) => (
                <Card key={index} className="hover:border-primary/40 transition-colors">
                  <CardHeader>
                    <div className="flex justify-between items-start mb-2">
                      <CardTitle className="text-lg">{workflow.name}</CardTitle>
                      <Badge
                        variant={
                          workflow.complexity === 'Simple' ? 'default' :
                          workflow.complexity === 'Intermédiaire' ? 'secondary' :
                          'destructive'
                        }
                      >
                        {workflow.complexity}
                      </Badge>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="flex gap-4 text-sm text-muted-foreground">
                      <span className="flex items-center gap-1">
                        <Zap className="size-4" />
                        {workflow.nodes} nodes
                      </span>
                      <span className="flex items-center gap-1">
                        <LinkIcon className="size-4" />
                        {workflow.connections} connexions
                      </span>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>

          {/* Features & Benefits */}
          <section className="bg-muted/30 py-24 sm:py-32 border-y border-border">
            <div className="container">
              <div className="grid md:grid-cols-2 gap-12">
                {/* Features */}
                <div>
                  <div className="flex items-center gap-3 mb-6">
                    <div className="bg-primary/20 p-2 rounded-full ring-8 ring-primary/10 text-primary">
                      <Zap className="size-5" />
                    </div>
                    <h2 className="text-2xl font-bold">Fonctionnalités</h2>
                  </div>
                  <ul className="space-y-4">
                    {pack.features.map((feature, index) => (
                      <li key={index} className="flex items-start gap-3">
                        <div className="bg-primary text-primary-foreground rounded-full p-1 mt-1">
                          <CheckCircle2 className="size-4" />
                        </div>
                        <span>{feature}</span>
                      </li>
                    ))}
                  </ul>
                </div>

                {/* Benefits */}
                <div>
                  <div className="flex items-center gap-3 mb-6">
                    <div className="bg-green-600/20 p-2 rounded-full ring-8 ring-green-600/10 text-green-600">
                      <Gift className="size-5" />
                    </div>
                    <h2 className="text-2xl font-bold">Avantages</h2>
                  </div>
                  <ul className="space-y-4">
                    {pack.benefits.map((benefit, index) => (
                      <li key={index} className="flex items-start gap-3">
                        <div className="bg-green-600 text-white rounded-full p-1 mt-1">
                          <CheckCircle2 className="size-4" />
                        </div>
                        <span>{benefit}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            </div>
          </section>

          {/* Requirements */}
          {pack.requirements.length > 0 && (
            <section className="container py-24 sm:py-32">
              <Card>
                <CardHeader>
                  <div className="flex items-center gap-3">
                    <div className="bg-primary/20 p-2 rounded-full ring-8 ring-primary/10 text-primary">
                      <Wrench className="size-5" />
                    </div>
                    <CardTitle>Prérequis Techniques</CardTitle>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="grid md:grid-cols-2 gap-4">
                    {pack.requirements.map((requirement, index) => (
                      <div key={index} className="flex items-center gap-2">
                        <div className="bg-primary/10 rounded-full p-2">
                          <CheckCircle2 className="size-4 text-primary" />
                        </div>
                        <span>{requirement}</span>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </section>
          )}

          {/* FAQ */}
          <section className="bg-muted/30 py-24 sm:py-32 border-y border-border">
            <div className="container max-w-3xl">
              <div className="text-center mb-12">
                <Badge className="mb-4">
                  <Shield className="size-3 mr-1" />
                  Support
                </Badge>
                <h2 className="text-3xl md:text-4xl font-bold mb-4">
                  Questions Fréquentes
                </h2>
                <p className="text-xl text-muted-foreground">
                  Tout ce que vous devez savoir avant d'acheter
                </p>
              </div>

              <Accordion type="single" collapsible className="w-full space-y-4">
                <AccordionItem value="item-1" className="bg-card border border-border rounded-lg px-6">
                  <AccordionTrigger className="hover:no-underline">
                    Comment recevoir mes workflows après l'achat ?
                  </AccordionTrigger>
                  <AccordionContent>
                    Vous recevrez un email immédiatement après l'achat avec un lien de téléchargement sécurisé.
                    Le lien est valide pendant 48h et vous permet 3 téléchargements maximum.
                  </AccordionContent>
                </AccordionItem>

                <AccordionItem value="item-2" className="bg-card border border-border rounded-lg px-6">
                  <AccordionTrigger className="hover:no-underline">
                    Comment installer les workflows dans n8n ?
                  </AccordionTrigger>
                  <AccordionContent>
                    Chaque pack inclut un guide d'installation détaillé. Vous devez simplement importer les fichiers JSON
                    dans votre instance n8n et configurer vos API keys.
                  </AccordionContent>
                </AccordionItem>

                <AccordionItem value="item-3" className="bg-card border border-border rounded-lg px-6">
                  <AccordionTrigger className="hover:no-underline">
                    Puis-je obtenir un remboursement ?
                  </AccordionTrigger>
                  <AccordionContent>
                    Oui ! Nous offrons une garantie satisfait ou remboursé de 30 jours. Si les workflows ne correspondent
                    pas à vos attentes, contactez-nous pour un remboursement complet.
                  </AccordionContent>
                </AccordionItem>

                <AccordionItem value="item-4" className="bg-card border border-border rounded-lg px-6">
                  <AccordionTrigger className="hover:no-underline">
                    Y a-t-il du support disponible ?
                  </AccordionTrigger>
                  <AccordionContent>
                    Oui ! Rejoignez notre communauté Skool gratuite pour poser vos questions et obtenir de l'aide :{" "}
                    <a
                      href="https://www.skool.com/audelalia-4222"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-primary hover:underline font-semibold"
                    >
                      skool.com/audelalia-4222
                    </a>
                  </AccordionContent>
                </AccordionItem>
              </Accordion>
            </div>
          </section>

          {/* Related Packs */}
          {relatedPacks.length > 0 && (
            <section className="container py-24 sm:py-32">
              <div className="text-center mb-12">
                <Badge className="mb-4">
                  <TrendingUp className="size-3 mr-1" />
                  Recommandations
                </Badge>
                <h2 className="text-3xl font-bold mb-4">
                  Ces Packs Pourraient Vous Intéresser
                </h2>
              </div>

              <div className="grid md:grid-cols-3 gap-6">
                {relatedPacks.map((related) => (
                  <Link key={related.id} href={route('packs.show', related.slug)} className="group">
                    <Card className="h-full hover:border-primary/40 transition-all hover:shadow-lg">
                      <CardHeader>
                        <CardTitle className="group-hover:text-primary transition-colors">
                          {related.name}
                        </CardTitle>
                        <p className="text-sm text-muted-foreground line-clamp-2">
                          {related.tagline}
                        </p>
                      </CardHeader>
                      <CardContent>
                        <div className="flex justify-between items-center">
                          <span className="text-2xl font-bold text-primary">
                            {Math.floor(related.price_eur)}€
                          </span>
                          <span className="text-sm text-muted-foreground">
                            {related.workflows_count} workflows
                          </span>
                        </div>
                      </CardContent>
                    </Card>
                  </Link>
                ))}
              </div>
            </section>
          )}

          {/* Final CTA */}
          <section className="container py-24 sm:py-32">
            <div className="bg-primary/10 border border-primary/20 rounded-2xl p-12 text-center relative overflow-hidden">
              <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[80%] h-full bg-primary/20 rounded-full blur-3xl opacity-30"></div>
              <div className="relative z-10">
                <h2 className="text-3xl md:text-4xl font-bold mb-4 text-foreground">
                  Prêt à Automatiser Votre Business ?
                </h2>
                <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                  Rejoignez les centaines d'entrepreneurs qui automatisent déjà avec nos workflows
                </p>
                <Button
                  size="lg"
                  className="font-bold text-lg group/arrow"
                  onClick={handleCheckout}
                >
                  <ShoppingBag className="size-5 mr-2" />
                  Acheter pour {getPrice()}{getCurrencySymbol()}
                  <ArrowRight className="size-5 ml-2 group-hover/arrow:translate-x-1 transition-transform" />
                </Button>
              </div>
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
