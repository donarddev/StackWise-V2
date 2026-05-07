<?php

namespace App\Services\Recommendation;

use Illuminate\Support\Arr;

class RecommendationEngine
{
    public function __construct(
        private readonly TechnologyCatalog $catalog,
        private readonly ExplanationBuilder $explanationBuilder,
        private readonly ConfidenceCalculator $confidenceCalculator,
        private readonly RiskAnalyzer $riskAnalyzer,
        private readonly SkillGapAnalyzer $skillGapAnalyzer,
        private readonly RoadmapBuilder $roadmapBuilder,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function recommend(ProjectContext $context): array
    {
        $board = new ScoreBoard;

        $this->scoreLanguages($board, $context);
        $languageTop = $board->top('language') ?? ['candidate' => 'PHP', 'score' => 0];
        $recommendedLanguage = $languageTop['candidate'];

        $this->scoreFrameworks($board, $context, $recommendedLanguage);
        $frameworkTop = $board->top('framework') ?? ['candidate' => $this->fallbackFramework($recommendedLanguage), 'score' => 0];
        $recommendedFramework = $frameworkTop['candidate'];

        $this->scoreSdlc($board, $context);
        $sdlcTop = $board->top('sdlc') ?? ['candidate' => 'Agile', 'score' => 0];
        $recommendedSdlc = $sdlcTop['candidate'];

        if (! $this->catalog->isFrameworkCompatible($recommendedLanguage, $recommendedFramework)) {
            $recommendedFramework = $this->fallbackFramework($recommendedLanguage);
        }

        $reasons = $this->explanationBuilder->buildReasons(
            context: $context,
            board: $board,
            language: $recommendedLanguage,
            framework: $recommendedFramework,
            sdlc: $recommendedSdlc,
        );

        $alternatives = $this->buildAlternativeStacks($context, $board, $recommendedLanguage, $recommendedFramework, $recommendedSdlc);
        $whyNot = $this->buildWhyNot($context, $board, $recommendedLanguage, $recommendedFramework, $recommendedSdlc);
        $risks = $this->riskAnalyzer->analyze($context, $recommendedLanguage, $recommendedFramework, $recommendedSdlc);
        $skills = $this->skillGapAnalyzer->analyze($context, $recommendedLanguage, $recommendedFramework);
        $roadmap = $this->roadmapBuilder->build($context, $recommendedSdlc);

        $confidence = $this->confidenceCalculator->calculate(
            context: $context,
            board: $board,
            recommendedLanguage: $recommendedLanguage,
            recommendedFramework: $recommendedFramework,
            recommendedSdlc: $recommendedSdlc,
            risks: $risks,
            skillGap: $skills,
        );

        return [
            'project_summary' => [
                'project_name' => $context->projectName(),
                'project_type' => $context->projectType(),
                'selected_features' => $context->selectedFeatures(),
                'team_size' => $context->teamSize(),
                'complexity' => $context->complexity(),
                'preferred_platform' => $context->preferredPlatform(),
                'development_experience' => $context->developmentExperience(),
                'timeline' => $context->timeline(),
                'project_goal' => $context->projectGoal(),
                'scalability_needs' => $context->scalabilityNeeds(),
                'security_requirements' => $context->securityRequirements(),
                'performance_requirements' => $context->performanceRequirements(),
                'budget_constraints' => $context->budgetConstraints(),
                'maintenance_expectations' => $context->maintenanceExpectations(),
                'deployment_preference' => $context->deploymentPreference(),
            ],
            'main_recommendation' => [
                'language' => $recommendedLanguage,
                'framework' => $recommendedFramework,
                'sdlc_model' => $recommendedSdlc,
                'confidence_score' => $confidence,
            ],
            'explanation' => [
                'language_reason' => $reasons['language_reason'],
                'framework_reason' => $reasons['framework_reason'],
                'sdlc_reason' => $reasons['sdlc_reason'],
            ],
            'alternative_stacks' => $alternatives,
            'why_not_this' => $whyNot,
            'risk_analysis' => $risks,
            'skill_gap_analysis' => $skills,
            'project_roadmap' => $roadmap,
            'feedback' => [],
            'engine_debug' => [
                'language_scores' => $board->scores('language'),
                'framework_scores' => $board->scores('framework'),
                'sdlc_scores' => $board->scores('sdlc'),
            ],
        ];
    }

    private function scoreLanguages(ScoreBoard $board, ProjectContext $context): void
    {
        /** @var array<string, mixed> $rules */
        $rules = config('recommendation.rules', []);

        $projectType = $context->projectType();
        $typeRules = Arr::get($rules, "project_type.{$projectType}.language", []);
        if (is_array($typeRules)) {
            $board->addMany('language', Arr::map($typeRules, static fn ($v): int => (int) $v), "Project type is {$projectType}.");
        }

        $scalability = $context->scalabilityNeeds();
        $scalabilityRules = Arr::get($rules, "scalability_needs.{$scalability}", []);
        if (is_array($scalabilityRules) && $scalability !== '') {
            $board->addMany('language', Arr::map($scalabilityRules, static fn ($v): int => (int) $v), "Scalability needs are {$scalability}.");
        }

        $security = $context->securityRequirements();
        $securityRules = Arr::get($rules, "security_requirements.{$security}", []);
        if (is_array($securityRules) && $security !== '') {
            $board->addMany('language', Arr::map($securityRules, static fn ($v): int => (int) $v), "Security requirements are {$security}.");
        }

        $budget = $context->budgetConstraints();
        $budgetRules = Arr::get($rules, "budget_constraints.{$budget}", []);
        if (is_array($budgetRules) && $budget !== '') {
            $board->addMany('language', Arr::map($budgetRules, static fn ($v): int => (int) $v), "Budget constraints are {$budget}.");
        }

        $maintenance = $context->maintenanceExpectations();
        $maintenanceRules = Arr::get($rules, "maintenance_expectations.{$maintenance}", []);
        if (is_array($maintenanceRules) && $maintenance !== '') {
            $board->addMany('language', Arr::map($maintenanceRules, static fn ($v): int => (int) $v), "Maintenance expectations are {$maintenance}.");
        }

        $deployment = $context->deploymentPreference();
        $deploymentRules = Arr::get($rules, "deployment_preference.{$deployment}", []);
        if (is_array($deploymentRules) && $deployment !== '') {
            $board->addMany('language', Arr::map($deploymentRules, static fn ($v): int => (int) $v), "Deployment preference is {$deployment}.");
        }

        $platform = $context->preferredPlatform();
        if ($platform === 'mobile') {
            $board->add('language', 'Dart', 18, 'Mobile platform target strongly favors Flutter/Dart for one codebase.');
        }
        if ($platform === 'web') {
            $board->add('language', 'PHP', 8, 'Web platform target aligns well with PHP for server-rendered and CRUD apps.');
            $board->add('language', 'TypeScript', 6, 'Web platform can benefit from TypeScript for interactive full-stack apps.');
        }

        $experience = $context->developmentExperience();
        if ($experience === 'beginner') {
            $board->add('language', 'PHP', 10, 'Beginner-level teams typically ship faster with PHP for CRUD web apps.');
            $board->add('language', 'Python', 10, 'Beginner-level teams typically ship faster with Python for simple apps and AI features.');
            $board->add('language', 'Java', -10, 'Java usually has a steeper ramp-up for beginners compared to PHP/Python.');
        }

        $timeline = $context->timeline();
        if ($timeline === 'short') {
            $board->add('language', 'PHP', 6, 'Short timelines favor stacks with strong scaffolding and rapid iteration.');
            $board->add('language', 'TypeScript', 4, 'Short timelines can fit Node/TypeScript when the scope is API-first or real-time.');
            $board->add('language', 'Java', -8, 'Short timelines are risky with Java for beginners due to setup and structure overhead.');
        }

        $this->scoreByFeatures($board, $context);
        $this->applyPerformanceAdjustments($board, $context);
        $this->applyGuardrails($board, $context);
    }

    private function scoreByFeatures(ScoreBoard $board, ProjectContext $context): void
    {
        /** @var array<string, array{language?: array<string,int>, framework?: array<string,int>}> $featureMap */
        $featureMap = config('recommendation.feature_map', []);

        foreach ($context->selectedFeatures() as $feature) {
            $key = strtolower($feature);
            $hit = $featureMap[$key] ?? null;
            if ($hit === null) {
                continue;
            }

            if (isset($hit['language']) && is_array($hit['language'])) {
                $board->addMany('language', $hit['language'], "Selected feature: {$feature}.");
            }

            if (isset($hit['framework']) && is_array($hit['framework'])) {
                $board->addMany('framework', $hit['framework'], "Selected feature: {$feature}.");
            }
        }

        $text = $context->analysisText();
        foreach (array_keys($featureMap) as $featureKey) {
            if (! str_contains($text, $featureKey)) {
                continue;
            }
            $hit = $featureMap[$featureKey] ?? null;
            if ($hit === null) {
                continue;
            }

            if (isset($hit['language']) && is_array($hit['language'])) {
                $board->addMany('language', $hit['language'], "Project description implies: {$featureKey}.");
            }

            if (isset($hit['framework']) && is_array($hit['framework'])) {
                $board->addMany('framework', $hit['framework'], "Project description implies: {$featureKey}.");
            }
        }
    }

    private function applyPerformanceAdjustments(ScoreBoard $board, ProjectContext $context): void
    {
        $perf = $context->performanceRequirements();
        if ($perf === '') {
            return;
        }

        if ($perf === 'high') {
            $board->add('language', 'Go', 16, 'High performance requirements favor Go for efficient concurrency and low overhead.');
            $board->add('language', 'Java', 12, 'High performance requirements can be handled well with Java on tuned runtimes.');
            $board->add('language', 'PHP', -6, 'PHP can perform well, but ultra-high throughput often benefits from Go/Java.');
        }

        if ($perf === 'medium') {
            $board->add('language', 'TypeScript', 6, 'Medium performance requirements align well with Node/TypeScript for many APIs.');
        }
    }

    private function applyGuardrails(ScoreBoard $board, ProjectContext $context): void
    {
        if ($context->preferredPlatform() === 'mobile') {
            $board->add('language', 'PHP', -14, 'Mobile apps are not primarily built in PHP.');
        }

        if ($context->projectType() === 'ai system') {
            $board->add('language', 'Python', 12, 'AI systems benefit from Python ecosystems for model tooling and data libraries.');
        }

        if ($context->isBeginnerHeavyTeam() && $context->isShortTimeline()) {
            $board->add('language', 'Go', -10, 'Go is realistic, but risky for a short timeline with beginner teams.');
        }
    }

    private function scoreFrameworks(ScoreBoard $board, ProjectContext $context, string $recommendedLanguage): void
    {
        foreach ($this->catalog->frameworksForLanguage($recommendedLanguage) as $framework) {
            $board->add('framework', $framework, 18, "Framework matches recommended language ({$recommendedLanguage}).");
        }

        $experience = $context->developmentExperience();
        foreach ($this->catalog->frameworksForLanguage($recommendedLanguage) as $framework) {
            $traits = $this->catalog->frameworkTraits($framework);
            $penalty = $this->catalog->learningCurvePenalty($traits['learning_curve'], $experience);
            if ($penalty > 0) {
                $board->add('framework', $framework, -$penalty, "Learning curve ({$traits['learning_curve']}) may be steep for a {$experience} team.");
            } else {
                $board->add('framework', $framework, 4, "Learning curve fits a {$experience} team.");
            }

            if ($context->isShortTimeline() && $traits['speed'] === 'high') {
                $board->add('framework', $framework, 6, 'Short timeline favors high-productivity frameworks.');
            }

            if ($context->maintenanceExpectations() === 'high' && $traits['maintainability'] === 'high') {
                $board->add('framework', $framework, 6, 'High maintenance expectations favor frameworks with strong conventions and structure.');
            }
        }

        $this->scoreByFeatures($board, $context);

        foreach ($this->catalog->allFrameworks() as $framework) {
            $language = $this->catalog->frameworkLanguage($framework);
            if ($language === null || $language === $recommendedLanguage) {
                continue;
            }
            $board->add('framework', $framework, -30, "Framework language mismatch (expects {$language}, recommended language is {$recommendedLanguage}).");
        }
    }

    private function scoreSdlc(ScoreBoard $board, ProjectContext $context): void
    {
        $teamSize = $context->teamSize();
        $timeline = $context->timeline();
        $complexity = $context->complexity();
        $stability = $context->requirementsStability();
        $stakeholders = $context->stakeholderInvolvement();

        if ($timeline === 'short') {
            $board->add('sdlc', 'RAD', 18, 'Short timeline favors rapid prototyping and early user feedback (RAD).');
            $board->add('sdlc', 'Agile', 14, 'Short timeline benefits from iterative delivery (Agile).');
        }

        if ($stability === 'changing') {
            $board->add('sdlc', 'Agile', 22, 'Changing requirements are best handled with iterative planning and reprioritization.');
            $board->add('sdlc', 'Waterfall', -16, 'Waterfall is risky when requirements are changing.');
        }

        if ($stability === 'stable') {
            $board->add('sdlc', 'Waterfall', 18, 'Stable requirements can suit a structured sequential approach.');
        }

        if ($complexity === 'large' || $context->isHighSecurity() || $context->isHighScalability()) {
            $board->add('sdlc', 'Spiral', 22, 'Higher risk/complexity benefits from explicit risk assessment cycles (Spiral).');
        }

        if ($complexity === 'small' && $timeline !== 'long') {
            $board->add('sdlc', 'Agile', 10, 'Small scope projects benefit from quick iteration (Agile).');
            $board->add('sdlc', 'RAD', 8, 'Small scope projects can benefit from fast prototyping (RAD).');
        }

        if ($teamSize <= 3) {
            $board->add('sdlc', 'Agile', 8, 'Small teams coordinate well with lightweight Agile ceremonies.');
        }

        if ($teamSize >= 6) {
            $board->add('sdlc', 'Iterative', 10, 'Larger teams can benefit from iterative planning with clearer checkpoints.');
        }

        if ($stakeholders === 'high') {
            $board->add('sdlc', 'Agile', 10, 'High stakeholder involvement pairs well with frequent demos and feedback loops.');
        }
    }

    private function fallbackFramework(string $language): string
    {
        return match ($language) {
            'Python' => 'FastAPI',
            'TypeScript' => 'NestJS',
            'Java' => 'Spring Boot',
            'Go' => 'Gin',
            'Dart' => 'Flutter',
            default => 'Laravel',
        };
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildAlternativeStacks(ProjectContext $context, ScoreBoard $board, string $language, string $framework, string $sdlc): array
    {
        $alternatives = [];

        $languageCandidates = $board->topCandidates('language', 4);
        foreach ($languageCandidates as $candidateLanguage) {
            if ($candidateLanguage === $language) {
                continue;
            }

            $candidateFramework = $this->fallbackFramework($candidateLanguage);
            $score = (int) (($board->scores('language')[$candidateLanguage] ?? 0) / max(1, $board->topGap('language')['top']) * 100);
            $score = max(55, min(92, $score));

            $alternatives[] = [
                'language' => $candidateLanguage,
                'framework' => $candidateFramework,
                'score' => $score,
                'best_for' => $this->catalog->language($candidateLanguage)['typical_use'] ?? 'Alternative project fit',
                'limitation' => $this->alternativeLimitation($context, $candidateLanguage, $candidateFramework),
                'tradeoffs' => [
                    'advantages' => $this->alternativeAdvantages($context, $candidateLanguage),
                    'disadvantages' => $this->alternativeDisadvantages($context, $candidateLanguage),
                    'scalability' => $this->tradeoffScalability($candidateLanguage),
                    'maintenance' => $this->tradeoffMaintenance($candidateLanguage),
                    'learning_curve' => $this->tradeoffLearningCurve($candidateLanguage, $context->developmentExperience()),
                    'development_speed' => $this->tradeoffSpeed($candidateFramework),
                    'cost' => $this->tradeoffCost($context, $candidateLanguage),
                ],
                'recommended_sdlc' => $sdlc,
            ];
        }

        return array_slice($alternatives, 0, 3);
    }

    /**
     * @return list<string>
     */
    private function buildWhyNot(ProjectContext $context, ScoreBoard $board, string $language, string $framework, string $sdlc): array
    {
        $items = [];

        foreach (array_slice($board->topCandidates('language', 5), 0, 5) as $candidate) {
            if ($candidate === $language) {
                continue;
            }

            $items[] = $this->whyNotLanguage($context, $board, $candidate, $language);
        }

        $frameworkCandidates = array_slice($board->topCandidates('framework', 5), 0, 5);
        foreach ($frameworkCandidates as $candidateFramework) {
            if ($candidateFramework === $framework) {
                continue;
            }

            $items[] = $this->whyNotFramework($context, $candidateFramework, $language);
        }

        foreach (['Waterfall', 'Agile', 'RAD', 'Spiral', 'Iterative'] as $candidateModel) {
            if ($candidateModel === $sdlc) {
                continue;
            }

            $items[] = $this->whyNotSdlc($context, $candidateModel, $sdlc);
        }

        $items = array_values(array_unique(array_filter(array_map('trim', $items))));

        return array_slice($items, 0, 10);
    }

    private function whyNotLanguage(ProjectContext $context, ScoreBoard $board, string $candidate, string $chosen): string
    {
        $evidence = $board->evidenceFor('language', $chosen, 2);
        $evidenceText = $evidence !== [] ? ' Key drivers: '.implode(' ', $evidence) : '';

        if ($candidate === 'Java' && $context->isBeginnerHeavyTeam() && $context->isShortTimeline()) {
            return "Why not Java? Java was not selected because you indicated a short timeline and a {$context->developmentExperience()} team, and Java typically has a steeper setup/architecture ramp-up in that scenario.{$evidenceText}";
        }

        if ($candidate === 'Dart' && $context->preferredPlatform() !== 'mobile') {
            return 'Why not Dart/Flutter? Dart is strongest for mobile UI apps; your preferred platform does not indicate a mobile-first target.';
        }

        if ($candidate === 'PHP' && $context->projectType() === 'ai system') {
            return 'Why not PHP? For AI-heavy systems, Python ecosystems usually provide stronger model and data tooling, reducing integration friction.';
        }

        return "Why not {$candidate}? It scored lower than {$chosen} against your project type, feature set, and constraints (timeline, experience, scalability/security).{$evidenceText}";
    }

    private function whyNotFramework(ProjectContext $context, string $candidateFramework, string $chosenLanguage): string
    {
        $frameworkLanguage = $this->catalog->frameworkLanguage($candidateFramework);
        if ($frameworkLanguage !== null && $frameworkLanguage !== $chosenLanguage) {
            return "Why not {$candidateFramework}? It was not selected because it is primarily used with {$frameworkLanguage}, but the recommended language is {$chosenLanguage}, and the engine enforces language–framework consistency.";
        }

        if ($context->isBeginnerHeavyTeam() && in_array($candidateFramework, ['Spring Boot', 'Symfony'], true)) {
            return "Why not {$candidateFramework}? With a {$context->developmentExperience()} team and a {$context->timeline()} timeline, the engine prioritized faster-to-ship frameworks with simpler onboarding.";
        }

        return "Why not {$candidateFramework}? It provides a valid path, but it scored lower on speed, learning curve, or maintainability for your stated constraints.";
    }

    private function whyNotSdlc(ProjectContext $context, string $candidateModel, string $chosenModel): string
    {
        if ($candidateModel === 'Waterfall' && $context->requirementsStability() === 'changing') {
            return 'Why not Waterfall? You indicated changing requirements, and Waterfall increases the cost of late changes compared to iterative models.';
        }

        if ($candidateModel === 'Spiral' && $context->complexity() === 'small') {
            return 'Why not Spiral? Spiral adds formal risk cycles that can be overhead for small-scope projects.';
        }

        if ($candidateModel === 'RAD' && $context->timeline() === 'long') {
            return 'Why not RAD? RAD is optimized for short timelines and fast prototyping; your timeline does not require that trade-off.';
        }

        return "Why not {$candidateModel}? Based on your team size, requirement stability, timeline, and risk profile, {$chosenModel} is a better overall fit.";
    }

    private function alternativeLimitation(ProjectContext $context, string $language, string $framework): string
    {
        if ($context->isBeginnerHeavyTeam() && in_array($language, ['Java', 'Go'], true)) {
            return 'Steeper ramp-up could slow delivery for beginner teams.';
        }

        if ($context->preferredPlatform() === 'web' && $framework === 'Flutter') {
            return 'Flutter is mobile-first; web targets can work but add UI/SEO trade-offs for typical web apps.';
        }

        if ($context->isHighSecurity() && in_array($language, ['TypeScript', 'Python'], true)) {
            return 'High security is achievable, but may require stricter discipline and tooling compared to enterprise-first stacks.';
        }

        return 'May involve different trade-offs in learning curve, tooling setup, or long-term maintenance.';
    }

    /**
     * @return list<string>
     */
    private function alternativeAdvantages(ProjectContext $context, string $language): array
    {
        return match ($language) {
            'Python' => ['Strong AI/ML ecosystem', 'Fast prototyping', 'Readable syntax'],
            'TypeScript' => ['Great for real-time apps', 'Shared language across frontend/backend', 'Strong tooling'],
            'Java' => ['Enterprise-grade tooling', 'Strong security ecosystem', 'Scales well for large systems'],
            'Go' => ['High performance', 'Simple deployment', 'Great concurrency model'],
            'Dart' => ['Single codebase for Android/iOS', 'Fast UI iteration', 'Consistent design system'],
            default => ['Mature ecosystem', 'Widely used in common projects'],
        };
    }

    /**
     * @return list<string>
     */
    private function alternativeDisadvantages(ProjectContext $context, string $language): array
    {
        if ($context->isBeginnerHeavyTeam() && $language === 'Java') {
            return ['Higher learning curve', 'More configuration', 'Slower initial delivery'];
        }

        return match ($language) {
            'Python' => ['Performance tuning may be needed at high throughput', 'Packaging/deployment discipline required'],
            'TypeScript' => ['Node ecosystem complexity', 'Requires good async patterns for scalability'],
            'Java' => ['More boilerplate', 'Heavier runtime footprint'],
            'Go' => ['Smaller high-level web ecosystem', 'Less beginner-friendly for UI-heavy stacks'],
            'Dart' => ['Primarily mobile-oriented', 'Backend ecosystem less common'],
            default => ['May not be the fastest for your constraints'],
        };
    }

    private function tradeoffScalability(string $language): string
    {
        return match ($language) {
            'Java', 'Go' => 'Strong for high scalability and throughput.',
            'TypeScript' => 'Strong for many scalable APIs, especially real-time, with proper architecture.',
            default => 'Good for low-to-medium scalability; high scale requires careful design.',
        };
    }

    private function tradeoffMaintenance(string $language): string
    {
        return match ($language) {
            'Java' => 'Excellent for long-term maintenance with strong conventions.',
            'TypeScript' => 'Good maintainability via type safety and tooling.',
            default => 'Maintainability is good when conventions and testing are followed.',
        };
    }

    private function tradeoffLearningCurve(string $language, string $experience): string
    {
        if ($experience === 'beginner' && in_array($language, ['Java', 'Go'], true)) {
            return 'Steeper for beginners; allow time for onboarding.';
        }

        return match ($language) {
            'PHP', 'Python' => 'Low learning curve for most beginners.',
            default => 'Moderate learning curve; manageable with structured practice.',
        };
    }

    private function tradeoffSpeed(string $framework): string
    {
        $traits = $this->catalog->frameworkTraits($framework);

        return match ($traits['speed']) {
            'high' => 'Fast to build due to strong scaffolding and defaults.',
            default => 'Moderate development speed; requires more setup/decisions.',
        };
    }

    private function tradeoffCost(ProjectContext $context, string $language): string
    {
        if ($context->budgetConstraints() === 'low' && $language === 'Java') {
            return 'Possible, but may increase time cost (learning + setup) compared to simpler stacks.';
        }

        if ($language === 'PHP') {
            return 'Often cost-effective for hosting and quick delivery.';
        }

        return 'Costs depend mainly on hosting choice and team ramp-up time.';
    }
}
