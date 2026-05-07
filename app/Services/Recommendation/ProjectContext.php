<?php

namespace App\Services\Recommendation;

/**
 * @phpstan-type ExperienceLevel 'beginner'|'intermediate'|'advanced'
 * @phpstan-type TimelineLevel 'short'|'medium'|'long'
 * @phpstan-type ComplexityLevel 'small'|'medium'|'large'
 */
class ProjectContext
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public array $data,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromValidated(array $data): self
    {
        $data['project_type'] = self::normalizeString($data['project_type'] ?? null);
        $data['preferred_platform'] = self::normalizeString($data['preferred_platform'] ?? null);
        $data['development_experience'] = self::normalizeString($data['development_experience'] ?? null);
        $data['timeline'] = self::normalizeString($data['timeline'] ?? null);
        $data['complexity'] = self::normalizeString($data['complexity'] ?? null);

        $data['scalability_needs'] = self::normalizeString($data['scalability_needs'] ?? null);
        $data['security_requirements'] = self::normalizeString($data['security_requirements'] ?? null);
        $data['performance_requirements'] = self::normalizeString($data['performance_requirements'] ?? null);
        $data['budget_constraints'] = self::normalizeString($data['budget_constraints'] ?? null);
        $data['maintenance_expectations'] = self::normalizeString($data['maintenance_expectations'] ?? null);
        $data['deployment_preference'] = self::normalizeString($data['deployment_preference'] ?? null);
        $data['requirements_stability'] = self::normalizeString($data['requirements_stability'] ?? null);
        $data['stakeholder_involvement'] = self::normalizeString($data['stakeholder_involvement'] ?? null);

        $features = $data['selected_features'] ?? [];
        if (is_string($features)) {
            $features = array_filter(array_map('trim', preg_split('/[,;\n]+/', $features) ?: []));
        }
        if (! is_array($features)) {
            $features = [];
        }
        $data['selected_features'] = array_values(array_unique(array_map(
            static fn (mixed $v): string => self::normalizeString((string) $v) ?? '',
            $features
        )));
        $data['selected_features'] = array_values(array_filter($data['selected_features']));

        $data['project_goal'] = trim((string) ($data['project_goal'] ?? ''));

        return new self($data);
    }

    public function projectName(): string
    {
        return (string) ($this->data['project_name'] ?? '');
    }

    public function projectType(): string
    {
        return (string) ($this->data['project_type'] ?? '');
    }

    /**
     * @return list<string>
     */
    public function selectedFeatures(): array
    {
        /** @var list<string> $features */
        $features = $this->data['selected_features'] ?? [];

        return $features;
    }

    public function teamSize(): int
    {
        return (int) ($this->data['team_size'] ?? 1);
    }

    public function complexity(): string
    {
        return (string) ($this->data['complexity'] ?? '');
    }

    public function preferredPlatform(): string
    {
        return (string) ($this->data['preferred_platform'] ?? '');
    }

    public function developmentExperience(): string
    {
        return (string) ($this->data['development_experience'] ?? '');
    }

    public function timeline(): string
    {
        return (string) ($this->data['timeline'] ?? '');
    }

    public function projectGoal(): string
    {
        return (string) ($this->data['project_goal'] ?? '');
    }

    public function scalabilityNeeds(): string
    {
        return (string) ($this->data['scalability_needs'] ?? '');
    }

    public function securityRequirements(): string
    {
        return (string) ($this->data['security_requirements'] ?? '');
    }

    public function performanceRequirements(): string
    {
        return (string) ($this->data['performance_requirements'] ?? '');
    }

    public function budgetConstraints(): string
    {
        return (string) ($this->data['budget_constraints'] ?? '');
    }

    public function maintenanceExpectations(): string
    {
        return (string) ($this->data['maintenance_expectations'] ?? '');
    }

    public function deploymentPreference(): string
    {
        return (string) ($this->data['deployment_preference'] ?? '');
    }

    public function requirementsStability(): string
    {
        return (string) ($this->data['requirements_stability'] ?? '');
    }

    public function stakeholderInvolvement(): string
    {
        return (string) ($this->data['stakeholder_involvement'] ?? '');
    }

    public function analysisText(): string
    {
        $parts = [
            $this->projectName(),
            $this->projectType(),
            $this->projectGoal(),
            implode(' ', $this->selectedFeatures()),
        ];

        return strtolower(trim(implode(' ', array_filter($parts))));
    }

    public function isBeginnerHeavyTeam(): bool
    {
        return $this->developmentExperience() === 'beginner';
    }

    public function isShortTimeline(): bool
    {
        return $this->timeline() === 'short';
    }

    public function isHighScalability(): bool
    {
        return $this->scalabilityNeeds() === 'high';
    }

    public function isHighSecurity(): bool
    {
        return $this->securityRequirements() === 'high';
    }

    public function isLargeComplexity(): bool
    {
        return $this->complexity() === 'large';
    }

    public function completenessRatio(): float
    {
        /** @var list<string> $required */
        $required = config('recommendation.inputs.required', []);
        if ($required === []) {
            return 1.0;
        }

        $filled = 0;
        foreach ($required as $key) {
            $value = $this->data[$key] ?? null;

            if (is_array($value)) {
                if (count($value) > 0) {
                    $filled++;
                }

                continue;
            }

            if ($value !== null && trim((string) $value) !== '') {
                $filled++;
            }
        }

        return max(0.0, min(1.0, $filled / count($required)));
    }

    private static function normalizeString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = strtolower(trim((string) $value));
        if ($value === '') {
            return null;
        }

        return $value;
    }
}
