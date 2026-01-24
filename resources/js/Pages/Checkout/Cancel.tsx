import { Head, Link } from "@inertiajs/react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { SpotlightDots } from "@/components/ui/spotlight-dots";
import {
  XCircle,
  ArrowLeft,
  Package,
  MessageCircle,
  HelpCircle,
  Shield,
  Sparkles
} from "lucide-react";

export default function Cancel() {
  return (
    <>
      <Head title="Paiement Annulé - AutomateHub" />

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
        <div className="glow-orb glow-orb-secondary w-[400px] h-[400px] bottom-1/3 -right-32" style={{ zIndex: 0 }} />

        <main className="relative" style={{ zIndex: 10 }}>
          <section className="container py-24 sm:py-32">
            <div className="max-w-3xl mx-auto space-y-12">
              {/* Cancel Header */}
              <div className="text-center space-y-6">
                <div className="flex justify-center">
                  <div className="glass-card rounded-full p-8 shadow-glow">
                    <XCircle className="size-24 text-muted-foreground" />
                  </div>
                </div>

                <div className="space-y-4">
                  <Badge className="bg-muted-foreground/20 text-muted-foreground border border-muted-foreground/30 px-4 py-2">
                    <XCircle className="size-4 mr-2" />
                    Paiement Annulé
                  </Badge>

                  <h1 className="text-4xl md:text-5xl font-black">
                    Paiement Non Finalisé
                  </h1>

                  <p className="text-xl text-muted-foreground">
                    Votre paiement n'a pas été effectué. Aucun montant n'a été débité de votre compte.
                  </p>
                </div>
              </div>

              {/* Info Card */}
              <Card className="glass-card shadow-glow">
                <CardContent className="p-8 space-y-6">
                  <h2 className="text-2xl font-bold mb-4">Que s'est-il passé ?</h2>

                  <div className="space-y-4 text-muted-foreground">
                    <p>
                      Vous avez annulé le processus de paiement ou fermé la fenêtre Stripe Checkout avant la fin.
                    </p>
                    <p>
                      <strong className="text-foreground">Aucun souci !</strong> Vous pouvez réessayer à tout moment.
                      Votre panier est toujours disponible.
                    </p>
                  </div>
                </CardContent>
              </Card>

              {/* Why Choose Us */}
              <Card className="glass-card">
                <CardContent className="p-8">
                  <h3 className="text-xl font-bold mb-6 flex items-center gap-2">
                    <Sparkles className="size-6 text-accent" />
                    Pourquoi Nos Clients Nous Choisissent
                  </h3>

                  <div className="grid md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <div className="flex items-center gap-2 mb-2">
                        <div className="glass-strong rounded-full p-2">
                          <Shield className="size-5 text-green-400" />
                        </div>
                        <p className="font-semibold">Paiement 100% Sécurisé</p>
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Propulsé par Stripe, leader mondial des paiements en ligne
                      </p>
                    </div>

                    <div className="space-y-2">
                      <div className="flex items-center gap-2 mb-2">
                        <div className="glass-strong rounded-full p-2">
                          <Package className="size-5 text-accent" />
                        </div>
                        <p className="font-semibold">Livraison Immédiate</p>
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Accédez à vos workflows dès validation du paiement
                      </p>
                    </div>

                    <div className="space-y-2">
                      <div className="flex items-center gap-2 mb-2">
                        <div className="glass-strong rounded-full p-2">
                          <MessageCircle className="size-5 text-blue-400" />
                        </div>
                        <p className="font-semibold">Support Réactif</p>
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Notre communauté et notre équipe sont là pour vous aider
                      </p>
                    </div>

                    <div className="space-y-2">
                      <div className="flex items-center gap-2 mb-2">
                        <div className="glass-strong rounded-full p-2">
                          <Shield className="size-5 text-purple-400" />
                        </div>
                        <p className="font-semibold">Garantie 30 Jours</p>
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Satisfait ou remboursé, sans conditions
                      </p>
                    </div>
                  </div>
                </CardContent>
              </Card>

              {/* Actions */}
              <div className="space-y-4">
                <Link href={route('packs.index')}>
                  <Button size="lg" className="w-full btn-gradient font-bold text-lg">
                    <ArrowLeft className="size-5 mr-2" />
                    Retour aux Packs Premium
                  </Button>
                </Link>

                <div className="grid md:grid-cols-2 gap-4">
                  <Link href={route('home')}>
                    <Button variant="outline" size="lg" className="w-full glass-strong">
                      Retour à l'Accueil
                    </Button>
                  </Link>
                  <Button
                    variant="outline"
                    size="lg"
                    className="w-full glass-strong border-accent/30 hover:border-accent/50"
                    onClick={() => window.open('https://www.skool.com/audelalia-4222', '_blank')}
                  >
                    <MessageCircle className="size-5 mr-2" />
                    Contacter le Support
                  </Button>
                </div>
              </div>

              {/* FAQ Quick Access */}
              <Card className="glass-card border-accent/30">
                <CardContent className="p-6 text-center">
                  <HelpCircle className="size-12 text-accent mx-auto mb-4" />
                  <h3 className="text-lg font-bold mb-2">Des Questions ?</h3>
                  <p className="text-sm text-muted-foreground mb-4">
                    Consultez notre FAQ ou rejoignez notre communauté pour obtenir de l'aide
                  </p>
                  <div className="flex flex-col sm:flex-row gap-3 justify-center">
                    <Button
                      variant="outline"
                      className="glass-strong"
                      onClick={() => window.open('https://www.skool.com/audelalia-4222', '_blank')}
                    >
                      Communauté Skool
                    </Button>
                    <Link href={route('packs.index')}>
                      <Button variant="outline" className="glass-strong border-accent/30">
                        Voir les Packs
                      </Button>
                    </Link>
                  </div>
                </CardContent>
              </Card>

              {/* Testimonials Teaser */}
              <div className="glass-card rounded-xl p-8 text-center">
                <p className="text-sm text-muted-foreground mb-4">Ce que disent nos clients :</p>
                <blockquote className="text-lg italic mb-4">
                  "J'ai économisé 15h/semaine grâce aux workflows Google Business. Mon chiffre d'affaires a augmenté de 30% !"
                </blockquote>
                <p className="font-semibold">Marie L.</p>
                <p className="text-sm text-muted-foreground">Pharmacie, Paris</p>
              </div>
            </div>
          </section>
        </main>

        <footer className="border-t border-border/50 py-12 relative" style={{ zIndex: 10 }}>
          <div className="container text-center text-muted-foreground">
            <p>© 2025 AutomateHub. Tous droits réservés.</p>
          </div>
        </footer>
      </div>
    </>
  );
}
