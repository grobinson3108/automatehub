import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Download, Zap, Lock } from "lucide-react";

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
    <Card className="h-full hover:shadow-lg transition-shadow border-border">
      <CardHeader>
        <div className="flex items-start justify-between mb-2">
          <Badge variant={workflow.is_premium ? "default" : "secondary"} className="mb-2">
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
              <Download className="size-3 mr-1" />
              {workflow.downloads_count}
            </span>
          )}
        </div>
        <CardTitle className="text-xl">{workflow.name}</CardTitle>
      </CardHeader>

      <CardContent>
        <p className="text-muted-foreground mb-4 line-clamp-3">
          {workflow.description}
        </p>

        <div className="flex items-center justify-between">
          <Badge variant="outline" className="text-xs">
            {workflow.category}
          </Badge>

          <Button
            size="sm"
            variant={workflow.is_premium ? "default" : "secondary"}
            onClick={() => window.location.href = `/workflows/${workflow.slug}`}
          >
            {workflow.is_premium ? "Voir détails" : "Télécharger"}
          </Button>
        </div>
      </CardContent>
    </Card>
  );
};
