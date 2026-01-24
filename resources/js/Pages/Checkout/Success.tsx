import { Head, Link } from "@inertiajs/react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { SpotlightDots } from "@/components/ui/spotlight-dots";
import {
  CheckCircle2,
  Download,
  Mail,
  Clock,
  Shield,
  Package,
  ArrowRight,
  Calendar,
  AlertCircle
} from "lucide-react";

interface Order {
  id: number;
  pack: {
    id: number;
    name: string;
    slug: string;
    workflows_count: number;
  };
  amount: number;
  currency: string;
  formatted_amount: string;
  customer_email: string;
  download_count: number;
  max_downloads: number;
  remaining_downloads: number;
  delivered_at: string;
  expires_at: string;
  created_at: string;
  stripe_session_id: string;
}

interface Props {
  order: Order;
}

export default function Success({ order }: Props) {
  const downloadUrl = route('checkout.download', order.stripe_session_id);

  return (
    <>
      <Head title="Paiement Réussi - AutomateHub" />

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
            <div className="max-w-4xl mx-auto space-y-12">
              {/* Success Header */}
              <div className="text-center space-y-6">
                <div className="flex justify-center">
                  <div className="glass-card rounded-full p-8 shadow-glow-strong">
                    <CheckCircle2 className="size-24 text-green-400" />
                  </div>
                </div>

                <div className="space-y-4">
                  <Badge className="bg-green-500/20 text-green-400 border border-green-500/30 px-4 py-2">
                    <CheckCircle2 className="size-4 mr-2" />
                    Paiement Confirmé
                  </Badge>

                  <h1 className="text-4xl md:text-5xl font-black">
                    Merci pour Votre Achat !
                  </h1>

                  <p className="text-xl text-muted-foreground">
                    Votre commande a été traitée avec succès. Vous pouvez maintenant télécharger vos workflows.
                  </p>
                </div>
              </div>

              {/* Order Details */}
              <Card className="glass-card shadow-glow">
                <CardContent className="p-8 space-y-6">
                  <div className="flex items-center justify-between pb-6 border-b border-border/50">
                    <div>
                      <h2 className="text-2xl font-bold mb-2">{order.pack.name}</h2>
                      <p className="text-muted-foreground">
                        Commande #{order.id} • {new Date(order.created_at).toLocaleDateString('fr-FR')}
                      </p>
                    </div>
                    <div className="text-right">
                      <p className="text-sm text-muted-foreground mb-1">Montant payé</p>
                      <p className="text-3xl font-black text-gradient-gold">{order.formatted_amount}</p>
                    </div>
                  </div>

                  <div className="grid md:grid-cols-2 gap-6">
                    <div className="flex items-start gap-3">
                      <div className="glass-strong p-3 rounded-full">
                        <Package className="size-5 text-primary" />
                      </div>
                      <div>
                        <p className="font-semibold mb-1">Workflows Inclus</p>
                        <p className="text-sm text-muted-foreground">
                          {order.pack.workflows_count} workflows prêts à l'emploi
                        </p>
                      </div>
                    </div>

                    <div className="flex items-start gap-3">
                      <div className="glass-strong p-3 rounded-full">
                        <Mail className="size-5 text-blue-400" />
                      </div>
                      <div>
                        <p className="font-semibold mb-1">Email de Confirmation</p>
                        <p className="text-sm text-muted-foreground">
                          Envoyé à {order.customer_email}
                        </p>
                      </div>
                    </div>

                    <div className="flex items-start gap-3">
                      <div className="glass-strong p-3 rounded-full">
                        <Download className="size-5 text-green-400" />
                      </div>
                      <div>
                        <p className="font-semibold mb-1">Téléchargements</p>
                        <p className="text-sm text-muted-foreground">
                          {order.remaining_downloads} sur {order.max_downloads} restants
                        </p>
                      </div>
                    </div>

                    <div className="flex items-start gap-3">
                      <div className="glass-strong p-3 rounded-full">
                        <Calendar className="size-5 text-purple-400" />
                      </div>
                      <div>
                        <p className="font-semibold mb-1">Accès Valide</p>
                        <p className="text-sm text-muted-foreground">
                          Jusqu'au {new Date(order.expires_at).toLocaleDateString('fr-FR')}
                        </p>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>

              {/* Download Section */}
              <Card className="glass-card shadow-glow-strong border-2 border-accent/30">
                <CardContent className="p-8 space-y-6">
                  <div className="flex items-center gap-3 mb-4">
                    <div className="glass-strong p-3 rounded-full">
                      <Download className="size-6 text-accent" />
                    </div>
                    <div>
                      <h3 className="text-xl font-bold">Téléchargez Vos Workflows</h3>
                      <p className="text-sm text-muted-foreground">
                        Fichiers JSON prêts à importer dans n8n
                      </p>
                    </div>
                  </div>

                  <div className="space-y-4">
                    <Button
                      size="lg"
                      className="w-full btn-gradient text-lg font-bold"
                      onClick={() => window.location.href = downloadUrl}
                    >
                      <Download className="size-5 mr-2" />
                      Télécharger Maintenant ({order.remaining_downloads} restants)
                    </Button>

                    <div className="glass-strong rounded-lg p-4 space-y-2">
                      <div className="flex items-start gap-2">
                        <AlertCircle className="size-5 text-accent mt-0.5 flex-shrink-0" />
                        <div className="text-sm space-y-1">
                          <p className="font-semibold">Important :</p>
                          <ul className="list-disc list-inside text-muted-foreground space-y-1 ml-2">
                            <li>Vous disposez de <strong className="text-foreground">{order.max_downloads} téléchargements</strong> maximum</li>
                            <li>L'accès expire le <strong className="text-foreground">{new Date(order.expires_at).toLocaleDateString('fr-FR')}</strong> (30 jours)</li>
                            <li>Les workflows sont personnalisés avec votre email</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>

              {/* Next Steps */}
              <Card className="glass-card">
                <CardContent className="p-8">
                  <h3 className="text-xl font-bold mb-6 flex items-center gap-2">
                    <CheckCircle2 className="size-6 text-accent" />
                    Prochaines Étapes
                  </h3>

                  <div className="space-y-4">
                    <div className="flex items-start gap-4">
                      <div className="glass-strong rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 font-bold text-accent">
                        1
                      </div>
                      <div>
                        <p className="font-semibold mb-1">Téléchargez vos workflows</p>
                        <p className="text-sm text-muted-foreground">
                          Cliquez sur le bouton ci-dessus pour récupérer le fichier ZIP
                        </p>
                      </div>
                    </div>

                    <div className="flex items-start gap-4">
                      <div className="glass-strong rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 font-bold text-accent">
                        2
                      </div>
                      <div>
                        <p className="font-semibold mb-1">Importez dans n8n</p>
                        <p className="text-sm text-muted-foreground">
                          Ouvrez n8n → Settings → Import from File → Sélectionnez les fichiers JSON
                        </p>
                      </div>
                    </div>

                    <div className="flex items-start gap-4">
                      <div className="glass-strong rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 font-bold text-accent">
                        3
                      </div>
                      <div>
                        <p className="font-semibold mb-1">Configurez vos API keys</p>
                        <p className="text-sm text-muted-foreground">
                          Suivez le guide d'installation inclus dans le ZIP pour configurer chaque workflow
                        </p>
                      </div>
                    </div>

                    <div className="flex items-start gap-4">
                      <div className="glass-strong rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 font-bold text-accent">
                        4
                      </div>
                      <div>
                        <p className="font-semibold mb-1">Rejoignez la communauté</p>
                        <p className="text-sm text-muted-foreground">
                          Besoin d'aide ? Rejoignez notre{" "}
                          <a
                            href="https://www.skool.com/audelalia-4222"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-accent hover:underline font-semibold"
                          >
                            communauté Skool
                          </a>
                        </p>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>

              {/* Actions */}
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <Link href={route('packs.index')}>
                  <Button variant="outline" size="lg" className="glass-strong border-accent/30 hover:border-accent/50">
                    <Package className="size-5 mr-2" />
                    Découvrir d'Autres Packs
                  </Button>
                </Link>
                <Link href={route('home')}>
                  <Button variant="outline" size="lg" className="glass-strong">
                    Retour à l'Accueil
                    <ArrowRight className="size-5 ml-2" />
                  </Button>
                </Link>
              </div>

              {/* Support */}
              <Card className="glass-card border-accent/30">
                <CardContent className="p-6 text-center">
                  <div className="flex justify-center mb-4">
                    <Shield className="size-12 text-accent" />
                  </div>
                  <h3 className="text-lg font-bold mb-2">Besoin d'Aide ?</h3>
                  <p className="text-sm text-muted-foreground mb-4">
                    Notre équipe et notre communauté sont là pour vous accompagner
                  </p>
                  <Button
                    variant="outline"
                    className="glass-strong"
                    onClick={() => window.open('https://www.skool.com/audelalia-4222', '_blank')}
                  >
                    Accéder au Support
                  </Button>
                </CardContent>
              </Card>
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
