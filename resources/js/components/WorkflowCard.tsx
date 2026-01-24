import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Download, Zap, Lock, ArrowRight } from "lucide-react";

interface WorkflowCardProps {
  workflow: {
    id: number;
    name: string;
    description: string;
    category: string;
    is_premium: boolean;
    downloads_count?: number;
    slug: string;
  };
}

export const WorkflowCard = ({ workflow }: WorkflowCardProps) => {
  return (
    <Card className={`h-full glass-card border-animated hover-lift transition-smooth ${workflow.is_premium ? 'shadow-glow' : ''}`}>
      <CardHeader className="space-y-4">
        <div className="flex items-start justify-between gap-3">
          <Badge className={workflow.is_premium ? "bg-gradient-premium text-black" : "bg-accent/20 text-accent border border-accent/30"}>
            {workflow.is_premium ? (
              <>
                <Lock className="size-3 mr-1" />
                Premium
              </>
            ) : (
              <>
                <Zap className="size-3 mr-1" />
                Gratuit
              </>
            )}
          </Badge>
          {workflow.downloads_count && workflow.downloads_count > 0 && (
            <span className="text-sm text-muted-foreground flex items-center">
              <Download className="size-3 mr-1 text-green-400" />
              {workflow.downloads_count}
            </span>
          )}
        </div>
        <CardTitle className="text-xl group-hover:text-primary transition-smooth">
          {workflow.name}
        </CardTitle>
      </CardHeader>

      <CardContent className="space-y-4">
        <p className="text-muted-foreground line-clamp-3 text-sm">
          {workflow.description}
        </p>

        <div className="flex items-center justify-between pt-2">
          <Badge className="glass px-2 py-1 text-xs border border-accent/20">
            {workflow.category}
          </Badge>

          <Button
            size="sm"
            className={workflow.is_premium ? "btn-gradient" : "glass-strong hover-lift"}
            onClick={() => window.location.href = `/workflows/${workflow.slug}`}
          >
            {workflow.is_premium ? "Voir détails" : "Télécharger"}
            <ArrowRight className="size-3 ml-1" />
          </Button>
        </div>
      </CardContent>
    </Card>
  );
};
