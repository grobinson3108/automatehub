import { Hero } from "@/components/sections/Hero";
import { Features } from "@/components/sections/Features";
import { Head } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { ArrowRight, Star, Zap, TrendingUp, Users } from "lucide-react";

export default function Welcome() {
  return (
    <>
      <Head title="AutomateHub - Automatisez votre business avec n8n" />

      <div className="min-h-screen bg-background text-foreground">
        <main>
          <Hero />

          {/* Stats Section */}
          <section className="container py-16 border-b border-border">
            <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
              <div className="text-center">
                <p className="text-4xl md:text-5xl font-bold text-primary mb-2">500+</p>
                <p className="text-muted-foreground">Entrepreneurs actifs</p>
              </div>
              <div className="text-center">
                <p className="text-4xl md:text-5xl font-bold text-primary mb-2">50+</p>
                <p className="text-muted-foreground">Workflows prêts</p>
              </div>
              <div className="text-center">
                <p className="text-4xl md:text-5xl font-bold text-primary mb-2">10h</p>
                <p className="text-muted-foreground">Économisées/semaine</p>
              </div>
              <div className="text-center">
                <p className="text-4xl md:text-5xl font-bold text-primary mb-2">48h</p>
                <p className="text-muted-foreground">ROI moyen</p>
              </div>
            </div>
          </section>

          <Features />

          {/* Popular Workflows Preview */}
          <section className="container py-24 sm:py-32">
            <div className="text-center mb-12">
              <Badge className="mb-4">
                <TrendingUp className="size-3 mr-1" />
                Les plus populaires
              </Badge>
              <h2 className="text-3xl md:text-4xl font-bold mb-4">
                Workflows les plus téléchargés
              </h2>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Découvrez les workflows qui transforment le quotidien des entrepreneurs
              </p>
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
              <Card className="border-primary/20 hover:border-primary/40 transition-colors">
                <CardHeader>
                  <Badge variant="secondary" className="w-fit mb-2">
                    <Zap className="size-3 mr-1" />
                    Gratuit
                  </Badge>
                  <CardTitle>Google Business Auto-Post</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-muted-foreground mb-4">
                    Publiez automatiquement sur Google Business. Parfait pour pharmacies et commerces.
                  </p>
                  <div className="flex items-center justify-between">
                    <span className="flex items-center text-sm text-muted-foreground">
                      <Star className="size-4 text-primary mr-1" />
                      4.9 (127 avis)
                    </span>
                    <Button size="sm" variant="ghost">
                      Voir <ArrowRight className="size-4 ml-1" />
                    </Button>
                  </div>
                </CardContent>
              </Card>

              <Card className="border-primary/20 hover:border-primary/40 transition-colors">
                <CardHeader>
                  <Badge className="w-fit mb-2">Premium</Badge>
                  <CardTitle>SMS Prescription Reminder</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-muted-foreground mb-4">
                    Rappels SMS automatiques pour renouvellement ordonnances. Spécial pharmacies.
                  </p>
                  <div className="flex items-center justify-between">
                    <span className="flex items-center text-sm text-muted-foreground">
                      <Star className="size-4 text-primary mr-1" />
                      5.0 (89 avis)
                    </span>
                    <Button size="sm" variant="ghost">
                      Voir <ArrowRight className="size-4 ml-1" />
                    </Button>
                  </div>
                </CardContent>
              </Card>

              <Card className="border-primary/20 hover:border-primary/40 transition-colors">
                <CardHeader>
                  <Badge className="w-fit mb-2">Premium</Badge>
                  <CardTitle>Pack E-commerce Complet</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-muted-foreground mb-4">
                    5 workflows essentiels pour automatiser votre boutique en ligne de A à Z.
                  </p>
                  <div className="flex items-center justify-between">
                    <span className="flex items-center text-sm text-muted-foreground">
                      <Star className="size-4 text-primary mr-1" />
                      4.8 (203 avis)
                    </span>
                    <Button size="sm" variant="ghost">
                      Voir <ArrowRight className="size-4 ml-1" />
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </div>

            <div className="text-center">
              <Button size="lg" onClick={() => window.location.href = '/workflows'}>
                Voir tous les workflows
                <ArrowRight className="size-5 ml-2" />
              </Button>
            </div>
          </section>

          {/* Testimonials */}
          <section className="bg-muted/30 py-24 sm:py-32">
            <div className="container">
              <div className="text-center mb-12">
                <Badge className="mb-4">
                  <Users className="size-3 mr-1" />
                  Témoignages
                </Badge>
                <h2 className="text-3xl md:text-4xl font-bold mb-4">
                  Ce qu'en disent nos utilisateurs
                </h2>
              </div>

              <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card>
                  <CardContent className="pt-6">
                    <div className="flex mb-4">
                      {[...Array(5)].map((_, i) => (
                        <Star key={i} className="size-4 fill-primary text-primary" />
                      ))}
                    </div>
                    <p className="text-muted-foreground mb-4">
                      "J'ai économisé 15h/semaine grâce aux workflows Google Business. Mon chiffre d'affaires a augmenté de 30% !"
                    </p>
                    <p className="font-semibold">Marie L.</p>
                    <p className="text-sm text-muted-foreground">Pharmacie, Paris</p>
                  </CardContent>
                </Card>

                <Card>
                  <CardContent className="pt-6">
                    <div className="flex mb-4">
                      {[...Array(5)].map((_, i) => (
                        <Star key={i} className="size-4 fill-primary text-primary" />
                      ))}
                    </div>
                    <p className="text-muted-foreground mb-4">
                      "Le pack e-commerce est parfait. Installation en 10min, ROI en 2 jours. Incroyable !"
                    </p>
                    <p className="font-semibold">Thomas D.</p>
                    <p className="text-sm text-muted-foreground">E-commerce, Lyon</p>
                  </CardContent>
                </Card>

                <Card>
                  <CardContent className="pt-6">
                    <div className="flex mb-4">
                      {[...Array(5)].map((_, i) => (
                        <Star key={i} className="size-4 fill-primary text-primary" />
                      ))}
                    </div>
                    <p className="text-muted-foreground mb-4">
                      "Support ultra-réactif, workflows en français, communauté active. Que demander de plus ?"
                    </p>
                    <p className="font-semibold">Sophie M.</p>
                    <p className="text-sm text-muted-foreground">Services, Marseille</p>
                  </CardContent>
                </Card>
              </div>
            </div>
          </section>

          {/* CTA Section */}
          <section className="container py-24 sm:py-32">
            <div className="bg-primary/10 border border-primary/20 rounded-2xl p-12 text-center">
              <h2 className="text-3xl md:text-4xl font-bold mb-4 text-foreground">
                Prêt à automatiser votre business ?
              </h2>
              <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                Rejoignez 500+ entrepreneurs qui ont déjà automatisé leur quotidien avec AutomateHub.
              </p>
              <Button size="lg" onClick={() => window.location.href = '/workflows'}>
                Accéder aux workflows gratuitement
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
