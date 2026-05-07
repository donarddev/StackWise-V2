<?php

namespace App\Services\Recommendation;

use Illuminate\Support\Arr;

class TechnologyCatalog
{
    /**
     * @return array<string, mixed>
     */
    public function language(string $language): array
    {
        /** @var array<string, array<string, mixed>> $languages */
        $languages = config('recommendation.catalog.languages', []);

        return $languages[$language] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function framework(string $framework): array
    {
        /** @var array<string, array<string, mixed>> $frameworks */
        $frameworks = config('recommendation.catalog.frameworks', []);

        return $frameworks[$framework] ?? [];
    }

    /**
     * @return list<string>
     */
    public function allLanguages(): array
    {
        /** @var array<string, array<string, mixed>> $languages */
        $languages = config('recommendation.catalog.languages', []);

        return array_keys($languages);
    }

    /**
     * @return list<string>
     */
    public function allFrameworks(): array
    {
        /** @var array<string, array<string, mixed>> $frameworks */
        $frameworks = config('recommendation.catalog.frameworks', []);

        return array_keys($frameworks);
    }

    public function frameworkLanguage(string $framework): ?string
    {
        $meta = $this->framework($framework);
        $language = $meta['language'] ?? null;

        return $language ? (string) $language : null;
    }

    /**
     * @return list<string>
     */
    public function frameworksForLanguage(string $language): array
    {
        $meta = $this->language($language);
        $frameworks = $meta['frameworks'] ?? [];

        if (! is_array($frameworks)) {
            return [];
        }

        return array_values(array_map('strval', $frameworks));
    }

    public function isFrameworkCompatible(string $language, string $framework): bool
    {
        $frameworkLanguage = $this->frameworkLanguage($framework);
        if ($frameworkLanguage === null) {
            return false;
        }

        return $frameworkLanguage === $language;
    }

    /**
     * @return array{learning_curve: string, speed: string, maintainability: string}
     */
    public function frameworkTraits(string $framework): array
    {
        $meta = $this->framework($framework);

        return [
            'learning_curve' => (string) ($meta['learning_curve'] ?? 'medium'),
            'speed' => (string) ($meta['speed'] ?? 'medium'),
            'maintainability' => (string) ($meta['maintainability'] ?? 'medium'),
        ];
    }

    public function learningCurvePenalty(string $curve, string $experience): int
    {
        $curveLevel = match ($curve) {
            'low' => 1,
            'medium' => 2,
            default => 3,
        };

        $experienceLevel = match ($experience) {
            'advanced' => 3,
            'intermediate' => 2,
            default => 1,
        };

        $gap = $curveLevel - $experienceLevel;

        return match (true) {
            $gap <= 0 => 0,
            $gap === 1 => 6,
            default => 12,
        };
    }

    /**
     * @return list<string>
     */
    public function candidateLanguagesForProject(ProjectContext $context): array
    {
        $languages = $this->allLanguages();

        if ($context->preferredPlatform() === 'mobile') {
            return Arr::sort(array_values(array_unique(array_merge(['Dart'], $languages))))->values()->all();
        }

        return $languages;
    }
}
