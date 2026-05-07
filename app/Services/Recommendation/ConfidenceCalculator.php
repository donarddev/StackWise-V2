<?php

namespace App\Services\Recommendation;

class ConfidenceCalculator
{
    /**
     * @param  list<array<string, mixed>>  $risks
     * @param  list<array<string, mixed>>  $skillGap
     */
    public function calculate(
        ProjectContext $context,
        ScoreBoard $board,
        string $recommendedLanguage,
        string $recommendedFramework,
        string $recommendedSdlc,
        array $risks,
        array $skillGap,
    ): int {
        $completeness = $context->completenessRatio(); // 0..1

        $languageGap = $board->topGap('language');
        $frameworkGap = $board->topGap('framework');
        $sdlcGap = $board->topGap('sdlc');

        $gapSignal = ($languageGap['gap'] + $frameworkGap['gap'] + $sdlcGap['gap']);

        $base = 55;
        $base += (int) round($completeness * 22);
        $base += (int) min(18, max(0, round($gapSignal / 8)));

        $base += $this->consistencyBonusOrPenalty($recommendedLanguage, $recommendedFramework);
        $base -= $this->riskPenalty($risks);
        $base -= $this->skillPenalty($skillGap);

        if ($context->isBeginnerHeavyTeam() && $context->isHighScalability()) {
            $base -= 6;
        }

        if ($context->isShortTimeline() && $context->isLargeComplexity()) {
            $base -= 8;
        }

        return max(50, min(97, $base));
    }

    private function consistencyBonusOrPenalty(string $language, string $framework): int
    {
        $frameworkLanguage = (new TechnologyCatalog)->frameworkLanguage($framework);
        if ($frameworkLanguage === null) {
            return -6;
        }

        if ($frameworkLanguage !== $language) {
            return -12;
        }

        return 4;
    }

    /**
     * @param  list<array<string, mixed>>  $risks
     */
    private function riskPenalty(array $risks): int
    {
        $penalty = 0;
        foreach ($risks as $risk) {
            $level = strtolower((string) ($risk['impact_level'] ?? 'low'));
            $penalty += match ($level) {
                'high' => 8,
                'medium' => 5,
                default => 3,
            };
        }

        return min(18, $penalty);
    }

    /**
     * @param  list<array<string, mixed>>  $skillGap
     */
    private function skillPenalty(array $skillGap): int
    {
        $penalty = 0;
        foreach ($skillGap as $item) {
            $gap = strtolower((string) ($item['gap_level'] ?? 'no gap'));
            $penalty += match ($gap) {
                'medium gap' => 6,
                'small gap' => 3,
                default => 0,
            };
        }

        return min(14, $penalty);
    }
}
