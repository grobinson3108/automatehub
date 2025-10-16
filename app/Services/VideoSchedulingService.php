<?php

namespace App\Services;

use Carbon\Carbon;

class VideoSchedulingService
{
    /**
     * Heures optimales de publication par plateforme (heure française)
     */
    private const OPTIMAL_PUBLICATION_TIMES = [
        'youtube' => ['18:00', '20:00', '21:00'],
        'tiktok' => ['19:00', '20:00', '21:00', '22:00'],
        'linkedin' => ['08:00', '12:00', '17:00'],
        'instagram' => ['17:00', '18:00', '19:00', '20:00'],
        'facebook' => ['15:00', '18:00', '20:00']
    ];

    /**
     * Meilleurs jours par plateforme (1 = lundi, 7 = dimanche)
     */
    private const OPTIMAL_PUBLICATION_DAYS = [
        'youtube' => [2, 3, 4, 5], // Mardi à vendredi
        'tiktok' => [1, 2, 3, 4, 5, 6], // Lundi à samedi
        'linkedin' => [2, 3, 4], // Mardi à jeudi
        'instagram' => [1, 2, 3, 4, 5, 6], // Lundi à samedi
        'facebook' => [2, 3, 4, 5, 6] // Mardi à samedi
    ];

    /**
     * Calcule automatiquement les dates de montage et publication
     * basées sur la date et heure de tournage
     */
    public function calculateSchedule(Carbon $filmingDate, string $filmingTime, string $platform): array
    {
        // 1. Calculer la date de montage (1 jour après le tournage)
        $editingDate = $filmingDate->copy()->addDay();

        // 2. Calculer l'heure de montage (même créneau que le tournage)
        $editingTime = $this->calculateEditingTime($filmingTime);

        // 3. Calculer la date et heure de publication optimale
        [$publicationDate, $publicationTime] = $this->calculateOptimalPublication(
            $editingDate,
            $editingTime,
            $platform
        );

        return [
            'editing_date' => $editingDate,
            'editing_start_time' => $editingTime['start'],
            'editing_end_time' => $editingTime['end'],
            'publication_date' => $publicationDate,
            'publication_time' => $publicationTime,
            'scheduled_datetime' => $publicationDate->format('Y-m-d') . ' ' . $publicationTime
        ];
    }

    /**
     * Calcule l'heure de montage basée sur l'heure de tournage
     */
    private function calculateEditingTime(string $filmingTime): array
    {
        // Parsing du format "HH:MM - HH:MM"
        $times = explode(' - ', $filmingTime);
        $startTime = $times[0] ?? '09:00';
        $endTime = $times[1] ?? '10:00';

        // Garder les mêmes créneaux pour le montage
        return [
            'start' => $startTime,
            'end' => $endTime
        ];
    }

    /**
     * Calcule la date et heure optimale de publication
     */
    private function calculateOptimalPublication(Carbon $editingDate, array $editingTime, string $platform): array
    {
        $minimumDelay = $this->calculateMinimumDelay($editingTime);
        $earliestPublication = $editingDate->copy()->addHours($minimumDelay);

        // Récupérer les créneaux optimaux pour cette plateforme
        $optimalTimes = self::OPTIMAL_PUBLICATION_TIMES[$platform] ?? ['12:00'];
        $optimalDays = self::OPTIMAL_PUBLICATION_DAYS[$platform] ?? [1, 2, 3, 4, 5];

        // Chercher le meilleur créneau à partir de la date minimum
        $currentDate = $earliestPublication->copy();

        // Limiter la recherche à 7 jours pour éviter les boucles infinies
        for ($i = 0; $i < 7; $i++) {
            $dayOfWeek = $currentDate->dayOfWeek;
            $dayOfWeek = $dayOfWeek === 0 ? 7 : $dayOfWeek; // Dimanche = 7

            // Si c'est un jour optimal pour cette plateforme
            if (in_array($dayOfWeek, $optimalDays)) {
                // Chercher une heure optimale ce jour-là
                foreach ($optimalTimes as $time) {
                    $testDateTime = $currentDate->copy()->setTimeFromTimeString($time);

                    // Vérifier que c'est après le délai minimum
                    if ($testDateTime >= $earliestPublication) {
                        return [$testDateTime->copy(), $time];
                    }
                }
            }

            $currentDate->addDay();
        }

        // Fallback: prendre la première heure optimale du jour suivant
        $fallbackDate = $earliestPublication->copy()->addDay();
        $fallbackTime = $optimalTimes[0];

        return [$fallbackDate, $fallbackTime];
    }

    /**
     * Calcule le délai minimum entre montage et publication
     */
    private function calculateMinimumDelay(array $editingTime): int
    {
        $editingHour = (int) explode(':', $editingTime['start'])[0];

        // Si montage le matin (avant 14h), publication l'après-midi (minimum 6h)
        // Si montage l'après-midi (après 14h), publication le lendemain matin (minimum 12h)
        return $editingHour < 14 ? 6 : 12;
    }

    /**
     * Recalcule toutes les dates pour une VideoIdea
     */
    public function recalculateVideoIdea(\App\Models\VideoIdea $videoIdea): array
    {
        if (!$videoIdea->filming_date || !$videoIdea->filming_start_time) {
            return [];
        }

        $filmingTime = $videoIdea->filming_start_time . ' - ' . $videoIdea->filming_end_time;

        return $this->calculateSchedule(
            $videoIdea->filming_date,
            $filmingTime,
            $videoIdea->platform
        );
    }

    /**
     * Vérifie s'il y a des conflits de planning
     */
    public function checkConflicts(Carbon $date, string $startTime, string $endTime, ?int $excludeId = null): array
    {
        $conflicts = \App\Models\VideoIdea::where('filming_date', $date)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('filming_start_time', [$startTime, $endTime])
                      ->orWhereBetween('filming_end_time', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          $q->where('filming_start_time', '<=', $startTime)
                            ->where('filming_end_time', '>=', $endTime);
                      });
            });

        if ($excludeId) {
            $conflicts->where('id', '!=', $excludeId);
        }

        return $conflicts->with('videoContentPlan')->get()->toArray();
    }

    /**
     * Propose des créneaux libres pour une date donnée
     */
    public function suggestAvailableSlots(Carbon $date, int $durationHours = 2): array
    {
        $busySlots = \App\Models\VideoIdea::where('filming_date', $date)
            ->orderBy('filming_start_time')
            ->get(['filming_start_time', 'filming_end_time']);

        $availableSlots = [];
        $currentHour = 8; // Commencer à 8h

        foreach ($busySlots as $busy) {
            $busyStart = (int) explode(':', $busy->filming_start_time)[0];

            // S'il y a un créneau libre avant cette réservation
            if ($currentHour + $durationHours <= $busyStart) {
                $availableSlots[] = [
                    'start' => sprintf('%02d:00', $currentHour),
                    'end' => sprintf('%02d:00', $currentHour + $durationHours),
                    'duration' => $durationHours
                ];
            }

            $busyEnd = (int) explode(':', $busy->filming_end_time)[0];
            $currentHour = max($currentHour, $busyEnd);
        }

        // Ajouter un créneau final si possible (avant 20h)
        if ($currentHour + $durationHours <= 20) {
            $availableSlots[] = [
                'start' => sprintf('%02d:00', $currentHour),
                'end' => sprintf('%02d:00', $currentHour + $durationHours),
                'duration' => $durationHours
            ];
        }

        return $availableSlots;
    }
}