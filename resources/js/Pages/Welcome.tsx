import { Head, Link } from "@inertiajs/react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { SpotlightDots } from "@/components/ui/spotlight-dots";
import {
  ArrowRight,
  Star,
  Zap,
  TrendingUp,
  Users,
  Shield,
  Rocket,
  BookOpen,
  Workflow,
  CheckCircle2,
  Package,
  Clock,
  Target,
  Sparkles,
  Eye,
  Play,
  MessageSquare,
  Award,
  Download
} from "lucide-react";

export default function Welcome() {
  return (
    <>
      <Head title="AutomateHub - Automatisez votre business avec n8n" />

      <div className="min-h-screen bg-background text-foreground relative overflow-hidden">
        {/* Spotlight Dots Background */}
        <SpotlightDots
          dotSize={1.5}
          dotColor="rgba(220, 142, 33, 1)"
          gap={28}
          spotlightSize={1000}
        />

        {/* Glow Orbs */}
        <div className="glow-orb glow-orb-primary w-[500px] h-[500px] -top-32 -left-32" style={{ zIndex: 0 }} />
        <div className="glow-orb glow-orb-secondary w-[400px] h-[400px] top-1/3 -right-32" style={{ zIndex: 0 }} />
        <div className="glow-orb glow-orb-primary w-[300px] h-[300px] bottom-1/4 left-1/4" style={{ zIndex: 0 }} />

        <main className="relative" style={{ zIndex: 10 }}>
          {/* Hero Section */}
          <section className="container py-24 sm:py-32">
            <div className="text-center space-y-8 max-w-5xl mx-auto">
              <Badge className="glass px-4 py-2 border border-accent/30">
                <Sparkles className="size-3 mr-2 text-accent" />
                <span className="text-foreground">500+ Entrepreneurs Automatisent Déjà Leur Business</span>
              </Badge>

              <div className="space-y-6">
                <h1 className="text-5xl md:text-7xl font-black leading-tight">
                  Automatisez Votre{" "}
                  <span className="text-gradient-gold">
                    Business
                  </span>
                  <br />
                  avec{" "}
                  <span className="text-gradient-gold">n8n</span>
                </h1>

                <p className="text-xl md:text-2xl text-muted-foreground max-w-3xl mx-auto">
                  La plateforme française pour automatiser votre pharmacie, commerce ou service.
                  <span className="text-foreground font-semibold"> Workflows prêts, tutoriels vidéo et support 7j/7.</span>
                </p>
              </div>

              {/* CTA Buttons */}
              <div className="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                <Button size="lg" className="btn-gradient group/btn">
                  Commencer Gratuitement
                  <ArrowRight className="size-5 ml-2 group-hover/btn:translate-x-1 transition-transform" />
                </Button>
                <Button size="lg" variant="outline" className="glass-strong border-accent/30 hover:border-accent/50">
                  <Play className="size-5 mr-2" />
                  Voir la Démo
                </Button>
              </div>

              {/* Trust Indicators */}
              <div className="flex flex-wrap items-center justify-center gap-6 pt-8 text-sm text-muted-foreground">
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="size-4 text-green-400" />
                  <span>Pas de carte bancaire</span>
                </div>
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="size-4 text-green-400" />
                  <span>Garantie 30 jours</span>
                </div>
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="size-4 text-green-400" />
                  <span>Support français</span>
                </div>
              </div>
            </div>
          </section>

          {/* Stats Section */}
          <section className="container py-16">
            <div className="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
              {[
                { icon: Users, label: "Entrepreneurs Actifs", value: "500+", color: "text-primary" },
                { icon: Package, label: "Workflows Disponibles", value: "50+", color: "text-blue-400" },
                { icon: Clock, label: "Économie Moyenne", value: "10h/sem", color: "text-green-400" },
                { icon: Target, label: "ROI Moyen", value: "48h", color: "text-purple-400" },
              ].map((stat, index) => (
                <div key={index} className="glass-card rounded-xl p-6 text-center hover-lift transition-smooth">
                  <div className="flex justify-center mb-3">
                    <div className="glass-strong p-3 rounded-full">
                      <stat.icon className={`size-6 ${stat.color}`} />
                    </div>
                  </div>
                  <p className="text-3xl font-bold mb-1">{stat.value}</p>
                  <p className="text-sm text-muted-foreground">{stat.label}</p>
                </div>
              ))}
            </div>
          </section>

          {/* Featured Packs Section */}
          <section className="container py-24">
            <div className="text-center mb-12 space-y-4">
              <Badge className="bg-gradient-premium text-black px-4 py-2 shadow-glow">
                <TrendingUp className="size-3 mr-2" />
                Les Plus Populaires
              </Badge>
              <h2 className="text-4xl md:text-5xl font-black">
                <span className="text-gradient-gold">Packs Premium</span> les Plus Vendus
              </h2>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Découvrez les workflows qui transforment le quotidien de centaines d'entrepreneurs
              </p>
            </div>

            <div className="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto mb-12">
              {[
                {
                  name: "Pack Pharmacie Pro",
                  tagline: "Automatisation complète pour pharmacies",
                  price: 49,
                  workflows: 8,
                  category: "automation",
                  featured: true,
                  sales: 127,
                },
                {
                  name: "Pack E-commerce",
                  tagline: "De la commande à la livraison automatisée",
                  price: 39,
                  workflows: 5,
                  category: "e-commerce",
                  featured: false,
                  sales: 203,
                },
                {
                  name: "Pack Marketing Local",
                  tagline: "Google Business + Réseaux sociaux auto",
                  price: 29,
                  workflows: 6,
                  category: "marketing",
                  featured: false,
                  sales: 89,
                },
              ].map((pack, index) => (
                <Card key={index} className={`glass-card border-animated hover-lift transition-smooth ${pack.featured ? 'shadow-glow-strong' : 'shadow-glow'}`}>
                  <CardHeader className="space-y-4">
                    <div className="flex items-start justify-between gap-3">
                      <Badge className={pack.featured ? "bg-gradient-premium text-black" : "bg-accent/20 text-accent border border-accent/30"}>
                        {pack.featured ? (
                          <>
                            <Star className="size-3 mr-1 fill-current" />
                            Bestseller
                          </>
                        ) : (
                          <span className="capitalize">{pack.category}</span>
                        )}
                      </Badge>
                    </div>

                    <CardTitle className="text-2xl group-hover:text-primary transition-smooth">
                      {pack.name}
                    </CardTitle>

                    <p className="text-sm text-muted-foreground">
                      {pack.tagline}
                    </p>
                  </CardHeader>

                  <CardContent className="space-y-6">
                    <div className="flex items-baseline gap-2">
                      <span className="text-4xl font-black text-gradient-gold">
                        {pack.price}€
                      </span>
                    </div>

                    <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
                      <span className="flex items-center gap-1">
                        <Zap className="size-4 text-primary" />
                        {pack.workflows} workflows
                      </span>
                      <span className="flex items-center gap-1">
                        <Download className="size-4 text-green-400" />
                        {pack.sales} ventes
                      </span>
                    </div>

                    <Button className="w-full btn-gradient group/btn">
                      Voir le Pack
                      <ArrowRight className="size-4 ml-2 group-hover/btn:translate-x-1 transition-transform" />
                    </Button>
                  </CardContent>
                </Card>
              ))}
            </div>

            <div className="text-center">
              <Link href="/packs">
                <Button size="lg" variant="outline" className="glass-strong border-accent/30 hover:border-accent/50">
                  Voir Tous les Packs Premium
                  <Package className="size-5 ml-2" />
                </Button>
              </Link>
            </div>
          </section>

          {/* Features Grid */}
          <section className="container py-24">
            <div className="text-center mb-12 space-y-4">
              <Badge className="glass px-4 py-2 border border-accent/30">
                <Rocket className="size-3 mr-2 text-accent" />
                Fonctionnalités
              </Badge>
              <h2 className="text-4xl md:text-5xl font-black">
                Pourquoi <span className="text-gradient-gold">AutomateHub</span> ?
              </h2>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                La seule plateforme n8n en français conçue pour les entrepreneurs locaux
              </p>
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
              {[
                {
                  icon: Workflow,
                  title: "Workflows n8n Prêts",
                  description: "Bibliothèque de workflows testés pour pharmacies, commerces et services. Import en 1 clic.",
                  color: "text-primary"
                },
                {
                  icon: BookOpen,
                  title: "Tutoriels Vidéo",
                  description: "Apprenez pas à pas avec nos tutoriels gratuits et premium. De débutant à expert.",
                  color: "text-blue-400"
                },
                {
                  icon: Zap,
                  title: "Automation Rapide",
                  description: "Automatisez Google Business, emails, SMS et WhatsApp en quelques minutes.",
                  color: "text-accent"
                },
                {
                  icon: Users,
                  title: "Communauté Skool",
                  description: "Rejoignez 500+ entrepreneurs qui automatisent leur business. Support 7j/7.",
                  color: "text-purple-400"
                },
                {
                  icon: Shield,
                  title: "Conforme RGPD",
                  description: "Tous nos workflows respectent le RGPD et les réglementations françaises.",
                  color: "text-green-400"
                },
                {
                  icon: Rocket,
                  title: "ROI Immédiat",
                  description: "Économisez 10h/semaine dès la première automatisation. Rentable en 48h.",
                  color: "text-orange-400"
                },
              ].map((feature, index) => (
                <Card key={index} className="glass-card hover-lift transition-smooth">
                  <CardHeader>
                    <div className="flex justify-center mb-4">
                      <div className="glass-strong p-4 rounded-full">
                        <feature.icon className={`size-8 ${feature.color}`} />
                      </div>
                    </div>
                    <CardTitle className="text-center text-xl">{feature.title}</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-muted-foreground text-center">
                      {feature.description}
                    </p>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>

          {/* How It Works */}
          <section className="container py-24">
            <div className="text-center mb-12 space-y-4">
              <Badge className="glass px-4 py-2 border border-accent/30">
                <Target className="size-3 mr-2 text-accent" />
                Comment Ça Marche
              </Badge>
              <h2 className="text-4xl md:text-5xl font-black">
                Automatisez en <span className="text-gradient-gold">3 Étapes</span>
              </h2>
            </div>

            <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
              {[
                {
                  step: "1",
                  title: "Choisissez Votre Pack",
                  description: "Parcourez nos packs premium et sélectionnez celui qui correspond à votre business.",
                  icon: Package,
                },
                {
                  step: "2",
                  title: "Importez les Workflows",
                  description: "Un clic pour importer tous les workflows dans votre instance n8n. Configuration guidée.",
                  icon: Download,
                },
                {
                  step: "3",
                  title: "Automatisez Tout",
                  description: "Activez vos workflows et profitez de 10h économisées par semaine. ROI en 48h.",
                  icon: Rocket,
                },
              ].map((step, index) => (
                <div key={index} className="text-center space-y-4">
                  <div className="flex justify-center">
                    <div className="relative">
                      <div className="glass-card rounded-full p-8 shadow-glow">
                        <step.icon className="size-12 text-accent" />
                      </div>
                      <div className="absolute -top-2 -right-2 bg-gradient-premium text-black rounded-full w-10 h-10 flex items-center justify-center font-black text-lg shadow-glow">
                        {step.step}
                      </div>
                    </div>
                  </div>
                  <h3 className="text-2xl font-bold">{step.title}</h3>
                  <p className="text-muted-foreground">{step.description}</p>
                </div>
              ))}
            </div>
          </section>

          {/* Testimonials */}
          <section className="container py-24">
            <div className="text-center mb-12 space-y-4">
              <Badge className="glass px-4 py-2 border border-accent/30">
                <MessageSquare className="size-3 mr-2 text-accent" />
                Témoignages
              </Badge>
              <h2 className="text-4xl md:text-5xl font-black">
                Ce Qu'en Disent <span className="text-gradient-gold">Nos Clients</span>
              </h2>
            </div>

            <div className="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
              {[
                {
                  name: "Marie L.",
                  role: "Pharmacie, Paris",
                  content: "J'ai économisé 15h/semaine grâce aux workflows Google Business. Mon chiffre d'affaires a augmenté de 30% !",
                  rating: 5,
                  avatar: "M",
                },
                {
                  name: "Thomas D.",
                  role: "E-commerce, Lyon",
                  content: "Le pack e-commerce est parfait. Installation en 10min, ROI en 2 jours. Incroyable !",
                  rating: 5,
                  avatar: "T",
                },
                {
                  name: "Sophie M.",
                  role: "Services, Marseille",
                  content: "Support ultra-réactif, workflows en français, communauté active. Que demander de plus ?",
                  rating: 5,
                  avatar: "S",
                },
              ].map((testimonial, index) => (
                <Card key={index} className="glass-card hover-lift transition-smooth">
                  <CardContent className="pt-6 space-y-4">
                    <div className="flex mb-4">
                      {[...Array(testimonial.rating)].map((_, i) => (
                        <Star key={i} className="size-4 fill-accent text-accent" />
                      ))}
                    </div>
                    <p className="text-muted-foreground italic">
                      "{testimonial.content}"
                    </p>
                    <div className="flex items-center gap-3 pt-4 border-t border-border/50">
                      <div className="glass-strong rounded-full w-12 h-12 flex items-center justify-center font-bold text-accent">
                        {testimonial.avatar}
                      </div>
                      <div>
                        <p className="font-semibold">{testimonial.name}</p>
                        <p className="text-sm text-muted-foreground">{testimonial.role}</p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>

          {/* Social Proof */}
          <section className="container py-16">
            <div className="glass-card rounded-3xl p-12 max-w-4xl mx-auto">
              <div className="grid md:grid-cols-3 gap-8 text-center">
                <div className="space-y-2">
                  <div className="flex items-center justify-center gap-1 mb-2">
                    <Star className="size-6 fill-accent text-accent" />
                    <Star className="size-6 fill-accent text-accent" />
                    <Star className="size-6 fill-accent text-accent" />
                    <Star className="size-6 fill-accent text-accent" />
                    <Star className="size-6 fill-accent text-accent" />
                  </div>
                  <p className="text-3xl font-black">4.9/5</p>
                  <p className="text-sm text-muted-foreground">Note moyenne</p>
                </div>
                <div className="space-y-2">
                  <Award className="size-12 text-accent mx-auto mb-2" />
                  <p className="text-3xl font-black">500+</p>
                  <p className="text-sm text-muted-foreground">Clients satisfaits</p>
                </div>
                <div className="space-y-2">
                  <TrendingUp className="size-12 text-accent mx-auto mb-2" />
                  <p className="text-3xl font-black">5000+</p>
                  <p className="text-sm text-muted-foreground">Workflows déployés</p>
                </div>
              </div>
            </div>
          </section>

          {/* Final CTA */}
          <section className="container py-24">
            <div className="glass-card rounded-3xl p-12 md:p-16 text-center max-w-4xl mx-auto shadow-glow-strong">
              <div className="space-y-6">
                <Badge className="bg-gradient-premium text-black px-4 py-2 shadow-glow">
                  <Sparkles className="size-4 mr-2" />
                  Rejoignez-nous Maintenant
                </Badge>

                <h2 className="text-4xl md:text-5xl font-black">
                  Prêt à Automatiser Votre
                  <br />
                  <span className="text-gradient-gold">Business</span> ?
                </h2>

                <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                  Rejoignez 500+ entrepreneurs qui ont déjà automatisé leur quotidien et économisent 10h/semaine
                </p>

                <div className="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                  <Button size="lg" className="btn-gradient group/btn">
                    Commencer Gratuitement
                    <ArrowRight className="size-5 ml-2 group-hover/btn:translate-x-1 transition-transform" />
                  </Button>
                  <Link href="/packs">
                    <Button size="lg" variant="outline" className="glass-strong border-accent/30 hover:border-accent/50">
                      <Package className="size-5 mr-2" />
                      Voir les Packs Premium
                    </Button>
                  </Link>
                </div>

                <div className="flex flex-wrap items-center justify-center gap-6 pt-6 text-sm text-muted-foreground">
                  <div className="flex items-center gap-2">
                    <CheckCircle2 className="size-4 text-green-400" />
                    <span>Garantie satisfait ou remboursé 30 jours</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <CheckCircle2 className="size-4 text-green-400" />
                    <span>Support français 7j/7</span>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </main>

        <footer className="border-t border-border/50 py-12 relative" style={{ zIndex: 10 }}>
          <div className="container">
            <div className="grid md:grid-cols-4 gap-8 mb-8">
              <div className="space-y-4">
                <h3 className="font-bold text-lg">AutomateHub</h3>
                <p className="text-sm text-muted-foreground">
                  La plateforme n8n française pour automatiser votre business.
                </p>
              </div>
              <div className="space-y-4">
                <h4 className="font-semibold">Produits</h4>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li><Link href="/packs" className="hover:text-accent transition-colors">Packs Premium</Link></li>
                  <li><Link href="/workflows" className="hover:text-accent transition-colors">Workflows Gratuits</Link></li>
                  <li><a href="#" className="hover:text-accent transition-colors">Tutoriels</a></li>
                </ul>
              </div>
              <div className="space-y-4">
                <h4 className="font-semibold">Ressources</h4>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li><a href="#" className="hover:text-accent transition-colors">Documentation</a></li>
                  <li><a href="#" className="hover:text-accent transition-colors">Communauté Skool</a></li>
                  <li><a href="#" className="hover:text-accent transition-colors">Blog</a></li>
                </ul>
              </div>
              <div className="space-y-4">
                <h4 className="font-semibold">Légal</h4>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li><a href="#" className="hover:text-accent transition-colors">CGV</a></li>
                  <li><a href="#" className="hover:text-accent transition-colors">Confidentialité</a></li>
                  <li><a href="#" className="hover:text-accent transition-colors">Mentions légales</a></li>
                </ul>
              </div>
            </div>
            <div className="border-t border-border/50 pt-8 text-center text-muted-foreground">
              <p>© 2025 AutomateHub. Tous droits réservés.</p>
            </div>
          </div>
        </footer>
      </div>
    </>
  );
}
