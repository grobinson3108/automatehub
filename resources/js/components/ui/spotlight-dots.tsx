import { cn } from "@/lib/utils";
import { useEffect, useRef, useState } from "react";

interface SpotlightDotsProps {
  className?: string;
  dotSize?: number;
  dotColor?: string;
  gap?: number;
  spotlightSize?: number;
}

export function SpotlightDots({
  className,
  dotSize = 1,
  dotColor = "rgba(220, 142, 33, 0.6)",
  gap = 24,
  spotlightSize = 400,
}: SpotlightDotsProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const spotlightPosRef = useRef({ x: 0, y: 0 });
  const timeRef = useRef(0);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    // Set canvas size
    const updateSize = () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      // Initialize spotlight at center
      spotlightPosRef.current = {
        x: canvas.width / 2,
        y: canvas.height / 2,
      };
    };
    updateSize();
    window.addEventListener("resize", updateSize);

    // Animation loop
    const animate = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      // Update time for smooth animation (very slow)
      timeRef.current += 0.00375;

      // Animate spotlight position in a figure-8 pattern (lemniscate)
      const centerX = canvas.width / 2;
      const centerY = canvas.height / 2;
      const radiusX = Math.min(canvas.width * 0.3, 400);
      const radiusY = Math.min(canvas.height * 0.25, 300);

      // Lemniscate of Bernoulli (figure-8)
      const t = timeRef.current;
      const scale = 2 / (3 - Math.cos(2 * t));
      spotlightPosRef.current.x = centerX + radiusX * scale * Math.cos(t);
      spotlightPosRef.current.y = centerY + radiusY * scale * Math.sin(2 * t) / 2;

      // Draw dots
      const cols = Math.ceil(canvas.width / gap);
      const rows = Math.ceil(canvas.height / gap);

      for (let i = 0; i < cols; i++) {
        for (let j = 0; j < rows; j++) {
          const x = i * gap + gap / 2;
          const y = j * gap + gap / 2;

          // Calculate distance from spotlight
          const distance = Math.sqrt(
            Math.pow(x - spotlightPosRef.current.x, 2) +
            Math.pow(y - spotlightPosRef.current.y, 2)
          );

          // Calculate opacity with smooth gradient falloff
          let opacity = 0;
          if (distance < spotlightSize) {
            opacity = 1 - distance / spotlightSize;
            opacity = Math.pow(opacity, 3); // Cubic falloff for more subtle effect
            opacity *= 0.4; // Reduce max opacity to 40% for subtlety
          }

          if (opacity > 0.02) {
            ctx.fillStyle = dotColor.replace(/[\d.]+\)$/, `${opacity})`);
            ctx.beginPath();
            ctx.arc(x, y, dotSize, 0, Math.PI * 2);
            ctx.fill();
          }
        }
      }

      requestAnimationFrame(animate);
    };

    animate();

    return () => {
      window.removeEventListener("resize", updateSize);
    };
  }, [dotSize, dotColor, gap, spotlightSize]);

  return (
    <canvas
      ref={canvasRef}
      className={cn("fixed inset-0 pointer-events-none", className)}
      style={{ zIndex: 1 }}
    />
  );
}
