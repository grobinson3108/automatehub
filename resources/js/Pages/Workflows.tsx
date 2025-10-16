import { Head } from "@inertiajs/react";
import { WorkflowCard } from "@/components/WorkflowCard";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Search, Filter } from "lucide-react";

interface Workflow {
  id: number;
  name: string;
  description: string;
  category: string;
  is_premium: boolean;
  downloads_count?: number;
  slug: string;
}

interface WorkflowsProps {
  workflows: Workflow[];
  categories: string[];
}

export default function Workflows({ workflows = [], categories = [] }: WorkflowsProps) {
  return (
    <>
      <Head title="Workflows n8n - AutomateHub" />

      <div className="min-h-screen bg-background text-foreground">
        {/* Header */}
        <header className="border-b border-border">
          <div className="container py-6">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-4xl font-bold mb-2 bg-gradient-to-r from-primary to-orange-600 bg-clip-text text-transparent">
                  Workflows n8n
                </h1>
                <p className="text-muted-foreground">
                  Bibliothèque de workflows prêts à l'emploi pour entrepreneurs
                </p>
              </div>
              <Button variant="outline" onClick={() => window.location.href = '/'}>
                Retour à l'accueil
              </Button>
            </div>
          </div>
        </header>

        <main className="container py-12">
          {/* Filters */}
          <div className="mb-8 flex flex-col sm:flex-row gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground size-4" />
              <input
                type="text"
                placeholder="Rechercher un workflow..."
                className="w-full pl-10 pr-4 py-2 rounded-md border border-input bg-background text-foreground"
              />
            </div>

            <Button variant="outline" className="sm:w-auto">
              <Filter className="size-4 mr-2" />
              Filtres
            </Button>
          </div>

          {/* Categories */}
          {categories.length > 0 && (
            <div className="mb-8 flex flex-wrap gap-2">
              <Badge variant="secondary" className="cursor-pointer">Tous</Badge>
              {categories.map((cat) => (
                <Badge key={cat} variant="outline" className="cursor-pointer hover:bg-primary/10">
                  {cat}
                </Badge>
              ))}
            </div>
          )}

          {/* Stats */}
          <div className="mb-8 flex gap-8">
            <div>
              <p className="text-3xl font-bold text-primary">{workflows.length}</p>
              <p className="text-sm text-muted-foreground">Workflows disponibles</p>
            </div>
            <div>
              <p className="text-3xl font-bold text-primary">
                {workflows.filter(w => !w.is_premium).length}
              </p>
              <p className="text-sm text-muted-foreground">Workflows gratuits</p>
            </div>
            <div>
              <p className="text-3xl font-bold text-primary">
                {workflows.filter(w => w.is_premium).length}
              </p>
              <p className="text-sm text-muted-foreground">Workflows premium</p>
            </div>
          </div>

          {/* Workflows Grid */}
          {workflows.length > 0 ? (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {workflows.map((workflow) => (
                <WorkflowCard key={workflow.id} workflow={workflow} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12">
              <p className="text-muted-foreground text-lg">
                Aucun workflow disponible pour le moment.
              </p>
              <p className="text-sm text-muted-foreground mt-2">
                Revenez bientôt, nous ajoutons régulièrement de nouveaux workflows !
              </p>
            </div>
          )}
        </main>

        {/* CTA Footer */}
        <section className="border-t border-border py-16 bg-muted/20">
          <div className="container text-center">
            <h2 className="text-3xl font-bold mb-4">Vous ne trouvez pas ce que vous cherchez ?</h2>
            <p className="text-muted-foreground mb-6 max-w-2xl mx-auto">
              Demandez un workflow personnalisé ou rejoignez notre communauté pour partager vos besoins.
            </p>
            <div className="flex gap-4 justify-center">
              <Button size="lg">
                Demander un workflow
              </Button>
              <Button size="lg" variant="outline">
                Rejoindre la communauté
              </Button>
            </div>
          </div>
        </section>
      </div>
    </>
  );
}
