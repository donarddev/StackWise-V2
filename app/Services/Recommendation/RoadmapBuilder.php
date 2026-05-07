<?php

namespace App\Services\Recommendation;

class RoadmapBuilder
{
    /**
     * @return list<array<string, mixed>>
     */
    public function build(ProjectContext $context, string $sdlcModel): array
    {
        $timeline = $context->timeline();
        $teamSize = $context->teamSize();
        $complexity = $context->complexity();
        $features = $context->selectedFeatures();
        $deploymentPreference = $context->deploymentPreference() !== '' ? $context->deploymentPreference() : 'not specified';

        $intensity = match ($timeline) {
            'short' => 'tight',
            'long' => 'relaxed',
            default => 'balanced',
        };

        $focus = $this->focusDistribution($timeline, $complexity);

        return [
            $this->phase(
                phase: 'Phase 1',
                task: 'Requirement Gathering',
                description: "Clarify scope, users, and success criteria. ({$sdlcModel}, {$intensity} schedule)",
                objectives: ['Define problem statement', 'Identify core users', 'List features and constraints'],
                deliverables: ['SRS or scope document', 'Feature list (MoSCoW)', 'Risk list (initial)'],
                priorities: ['Confirm must-have features', 'Define non-functional requirements (security/scalability)'],
                focus: $focus['requirements'],
            ),
            $this->phase(
                phase: 'Phase 2',
                task: 'Planning & Design',
                description: 'Design architecture, database schema, and UI flows aligned with the recommended stack.',
                objectives: ['System architecture diagram', 'Database ERD', 'UI wireframes'],
                deliverables: ['Architecture + ERD', 'API contracts (if any)', 'Milestone plan'],
                priorities: ['Define modules and responsibilities', "Split tasks for {$teamSize} teammates"],
                focus: $focus['design'],
            ),
            $this->phase(
                phase: 'Phase 3',
                task: 'Development',
                description: 'Build features in prioritized order, keeping code modular and testable.',
                objectives: ['Implement core flows', 'Integrate selected features', 'Maintain coding standards'],
                deliverables: ['Working MVP', 'Feature increments', 'Changelog'],
                priorities: $this->developmentPriorities($features),
                focus: $focus['development'],
            ),
            $this->phase(
                phase: 'Phase 4',
                task: 'Testing',
                description: 'Validate behavior, performance expectations, and security basics before deployment.',
                objectives: ['Feature tests', 'Bug fixing', 'Basic security review'],
                deliverables: ['Test report', 'Bug list + fixes', 'Demo script'],
                priorities: ['Test core flows first', 'Add regression tests for critical modules'],
                focus: $focus['testing'],
            ),
            $this->phase(
                phase: 'Phase 5',
                task: 'Deployment',
                description: "Prepare deployment based on your preference ({$deploymentPreference}).",
                objectives: ['Deploy to target environment', 'Configure environment variables', 'Smoke test'],
                deliverables: ['Deployed app', 'Deployment notes', 'Monitoring/logging basics'],
                priorities: ['Use a staging-like deploy first', 'Verify backups and rollback steps'],
                focus: $focus['deployment'],
            ),
            $this->phase(
                phase: 'Phase 6',
                task: 'Maintenance',
                description: 'Plan post-release improvements, documentation, and future iterations.',
                objectives: ['Collect feedback', 'Fix defects', 'Plan next version'],
                deliverables: ['Maintenance checklist', 'Backlog for v2', 'Documentation updates'],
                priorities: ['Address high-severity issues', 'Improve performance for bottlenecks'],
                focus: $focus['maintenance'],
            ),
        ];
    }

    /**
     * @param  list<string>  $objectives
     * @param  list<string>  $deliverables
     * @param  list<string>  $priorities
     * @return array<string, mixed>
     */
    private function phase(
        string $phase,
        string $task,
        string $description,
        array $objectives,
        array $deliverables,
        array $priorities,
        int $focus,
    ): array {
        return [
            'phase' => $phase,
            'task' => $task,
            'description' => $description,
            'objectives' => $objectives,
            'deliverables' => $deliverables,
            'priorities' => $priorities,
            'estimated_focus' => $focus, // percentage-ish
        ];
    }

    /**
     * @return array{requirements:int, design:int, development:int, testing:int, deployment:int, maintenance:int}
     */
    private function focusDistribution(string $timeline, string $complexity): array
    {
        $base = match ($timeline) {
            'short' => ['requirements' => 12, 'design' => 14, 'development' => 42, 'testing' => 16, 'deployment' => 8, 'maintenance' => 8],
            'long' => ['requirements' => 16, 'design' => 18, 'development' => 34, 'testing' => 16, 'deployment' => 8, 'maintenance' => 8],
            default => ['requirements' => 14, 'design' => 16, 'development' => 38, 'testing' => 16, 'deployment' => 8, 'maintenance' => 8],
        };

        if ($complexity === 'large') {
            $base['design'] += 4;
            $base['testing'] += 4;
            $base['development'] -= 6;
            $base['maintenance'] -= 2;
        }

        return $base;
    }

    /**
     * @param  list<string>  $features
     * @return list<string>
     */
    private function developmentPriorities(array $features): array
    {
        $priorities = ['Implement core CRUD / main user flows', 'Add authentication and authorization if needed'];

        foreach ($features as $feature) {
            $key = strtolower($feature);
            if ($key === 'api') {
                $priorities[] = 'Define API contract and implement endpoints early';
            }
            if ($key === 'real-time' || $key === 'chat') {
                $priorities[] = 'Build a minimal real-time spike first, then expand';
            }
            if ($key === 'ai/ml') {
                $priorities[] = 'Prototype the AI/ML integration with a small, testable demo';
            }
        }

        return array_values(array_unique($priorities));
    }
}
