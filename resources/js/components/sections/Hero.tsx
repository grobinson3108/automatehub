import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { ArrowRight, Zap } from "lucide-react";

export const Hero = () => {
  return (
    <section className="container w-full">
      <div className="grid place-items-center lg:max-w-screen-xl gap-8 mx-auto py-20 md:py-32">
        <div className="text-center space-y-8">
          <Badge variant="outline" className="text-sm py-2">
            <span className="mr-2 text-primary">
              <Badge>
                <Zap className="size-3" />
              </Badge>
            </span>
            <span> Automatisez votre business en quelques clics </span>
          </Badge>

          <div className="max-w-screen-md mx-auto text-center text-4xl md:text-6xl font-bold">
            <h1>
              La plateforme
              <span className="text-transparent px-2 bg-gradient-to-r from-primary to-orange-600 bg-clip-text">
                n8n
              </span>
              pour entrepreneurs
            </h1>
          </div>

          <p className="max-w-screen-sm mx-auto text-xl text-muted-foreground">
            Workflows prêts à l'emploi, tutoriels vidéo et communauté active.
            Automatisez votre pharmacie, commerce ou service en minutes.
          </p>

          <div className="space-y-4 md:space-y-0 md:space-x-4">
            <Button className="w-5/6 md:w-1/4 font-bold group/arrow" size="lg">
              Commencer gratuitement
              <ArrowRight className="size-5 ml-2 group-hover/arrow:translate-x-1 transition-transform" />
            </Button>

            <Button
              variant="secondary"
              className="w-5/6 md:w-1/4 font-bold"
              size="lg"
            >
              Voir les workflows
            </Button>
          </div>
        </div>

        <div className="relative group mt-14">
          <div className="absolute top-2 lg:-top-8 left-1/2 transform -translate-x-1/2 w-[90%] mx-auto h-24 lg:h-80 bg-primary/50 rounded-full blur-3xl"></div>
          <div className="w-full md:w-[1200px] mx-auto rounded-lg relative leading-none flex items-center border border-t-2 border-secondary border-t-primary/30 bg-card p-8">
            <div className="text-center w-full">
              <p className="text-muted-foreground text-lg">
                📸 Dashboard Preview Coming Soon
              </p>
              <p className="text-sm text-muted-foreground mt-2">
                Interface n8n automatehub.fr
              </p>
            </div>
          </div>

          <div className="absolute bottom-0 left-0 w-full h-20 md:h-28 bg-gradient-to-b from-background/0 via-background/50 to-background rounded-lg"></div>
        </div>
      </div>
    </section>
  );
};
