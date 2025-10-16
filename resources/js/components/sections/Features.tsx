import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Workflow, Zap, Users, BookOpen, Shield, Rocket } from "lucide-react";

interface FeaturesProps {
  icon: React.ReactNode;
  title: string;
  description: string;
}

const featureList: FeaturesProps[] = [
  {
    icon: <Workflow className="size-6" />,
    title: "Workflows n8n prêts",
    description:
      "Bibliothèque de workflows testés pour pharmacies, commerces et services. Import en 1 clic.",
  },
  {
    icon: <BookOpen className="size-6" />,
    title: "Tutoriels vidéo",
    description:
      "Apprenez pas à pas avec nos tutoriels gratuits et premium. De débutant à expert.",
  },
  {
    icon: <Zap className="size-6" />,
    title: "Automation rapide",
    description:
      "Automatisez Google Business, emails, SMS et WhatsApp en quelques minutes.",
  },
  {
    icon: <Users className="size-6" />,
    title: "Communauté Skool",
    description:
      "Rejoignez 500+ entrepreneurs qui automatisent leur business. Support 7j/7.",
  },
  {
    icon: <Shield className="size-6" />,
    title: "Conforme RGPD",
    description:
      "Tous nos workflows respectent le RGPD et les réglementations françaises.",
  },
  {
    icon: <Rocket className="size-6" />,
    title: "ROI immédiat",
    description:
      "Économisez 10h/semaine dès la première automatisation. Rentable en 48h.",
  },
];

export const Features = () => {
  return (
    <section id="features" className="container py-24 sm:py-32">
      <h2 className="text-lg text-primary text-center mb-2 tracking-wider">
        Fonctionnalités
      </h2>

      <h2 className="text-3xl md:text-4xl text-center font-bold mb-4">
        Pourquoi choisir AutomateHub ?
      </h2>

      <h3 className="md:w-1/2 mx-auto text-xl text-center text-muted-foreground mb-8">
        La seule plateforme n8n en français conçue pour les entrepreneurs
        locaux. Workflows, formations et support inclus.
      </h3>

      <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {featureList.map(({ icon, title, description }) => (
          <div key={title}>
            <Card className="h-full bg-background border-0 shadow-none">
              <CardHeader className="flex justify-center items-center">
                <div className="bg-primary/20 p-2 rounded-full ring-8 ring-primary/10 mb-4 text-primary">
                  {icon}
                </div>

                <CardTitle>{title}</CardTitle>
              </CardHeader>

              <CardContent className="text-muted-foreground text-center">
                {description}
              </CardContent>
            </Card>
          </div>
        ))}
      </div>
    </section>
  );
};
