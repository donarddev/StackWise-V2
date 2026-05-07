<?php

namespace App\Services\Recommendation;

class SkillGapAnalyzer
{
    /**
     * @return list<array{
     *   skill: string,
     *   required_level: 'Beginner'|'Intermediate'|'Advanced',
     *   user_level: 'Beginner'|'Intermediate'|'Advanced',
     *   gap_level: 'No gap'|'Small gap'|'Medium gap',
     *   suggestion: string,
     *   resources?: list<string>,
     *   readiness?: string
     * }>
     */
    public function analyze(ProjectContext $context, string $language, string $framework): array
    {
        $userLevel = $this->mapUserLevel($context->developmentExperience());

        $requirements = $this->requiredSkills($language, $framework, $context);

        return array_map(function (array $skill) use ($userLevel): array {
            $required = (string) $skill['required_level'];
            $gap = $this->compareLevels($userLevel, $required);

            return [
                'skill' => (string) $skill['skill'],
                'required_level' => $required,
                'user_level' => $userLevel,
                'gap_level' => $gap,
                'suggestion' => (string) $skill['suggestion'],
                'resources' => $skill['resources'] ?? [],
                'readiness' => $gap === 'No gap' ? 'Ready' : ($gap === 'Small gap' ? 'Mostly ready' : 'Needs preparation'),
            ];
        }, $requirements);
    }

    /**
     * @return list<array{skill: string, required_level: 'Beginner'|'Intermediate'|'Advanced', suggestion: string, resources?: list<string>}>
     */
    private function requiredSkills(string $language, string $framework, ProjectContext $context): array
    {
        $base = [
            [
                'skill' => 'Core programming fundamentals',
                'required_level' => 'Beginner',
                'suggestion' => 'Practice variables, control flow, functions, debugging, and code organization.',
                'resources' => ['Official language docs', 'Small coding exercises daily'],
            ],
            [
                'skill' => 'Git workflow',
                'required_level' => 'Beginner',
                'suggestion' => 'Use branches, clear commits, and pull requests (even for school teams).',
                'resources' => ['GitHub Learning Lab', 'Conventional commits (optional)'],
            ],
        ];

        $stackSpecific = match ($language) {
            'Python' => [
                [
                    'skill' => 'Python + environment management (venv, pip)',
                    'required_level' => 'Beginner',
                    'suggestion' => 'Standardize dependency installs and lock versions early to avoid “works on my machine”.',
                ],
                [
                    'skill' => "{$framework} API design",
                    'required_level' => 'Intermediate',
                    'suggestion' => 'Design request/response schemas, validation, and error handling conventions.',
                ],
            ],
            'TypeScript' => [
                [
                    'skill' => 'TypeScript types and async patterns',
                    'required_level' => 'Intermediate',
                    'suggestion' => 'Use types/interfaces, avoid “any”, and handle async flows predictably.',
                ],
                [
                    'skill' => "{$framework} architecture (modules/services)",
                    'required_level' => 'Intermediate',
                    'suggestion' => 'Keep controllers thin, use services, and separate domain logic from transport.',
                ],
            ],
            'Java' => [
                [
                    'skill' => 'Java OOP + dependency injection basics',
                    'required_level' => 'Intermediate',
                    'suggestion' => 'Be comfortable with classes, interfaces, and DI patterns for maintainable services.',
                ],
                [
                    'skill' => "{$framework} configuration and security basics",
                    'required_level' => 'Advanced',
                    'suggestion' => 'Learn configuration profiles, security setup, and project structure conventions.',
                ],
            ],
            'Go' => [
                [
                    'skill' => 'Go concurrency fundamentals',
                    'required_level' => 'Intermediate',
                    'suggestion' => 'Understand goroutines, channels, and safe shared state for scalable services.',
                ],
                [
                    'skill' => "{$framework} routing + middleware patterns",
                    'required_level' => 'Intermediate',
                    'suggestion' => 'Implement clean middleware and consistent error responses early.',
                ],
            ],
            'Dart' => [
                [
                    'skill' => 'Flutter widgets and layout',
                    'required_level' => 'Intermediate',
                    'suggestion' => 'Build responsive UI components and reuse widgets consistently.',
                ],
                [
                    'skill' => 'State management',
                    'required_level' => 'Intermediate',
                    'suggestion' => 'Start simple, then adopt a consistent state approach once the UI grows.',
                ],
            ],
            default => [
                [
                    'skill' => "{$framework} fundamentals (routing, validation, auth)",
                    'required_level' => 'Beginner',
                    'suggestion' => 'Build a small CRUD module end-to-end before expanding features.',
                ],
                [
                    'skill' => 'SQL + database modeling',
                    'required_level' => 'Beginner',
                    'suggestion' => 'Design tables, relationships, and indexes for your core entities.',
                ],
            ],
        };

        if ($context->isHighSecurity()) {
            $stackSpecific[] = [
                'skill' => 'Secure authentication & authorization (RBAC)',
                'required_level' => 'Intermediate',
                'suggestion' => 'Plan access control and audit logging early; avoid “patching security later”.',
            ];
        }

        if ($context->isHighScalability()) {
            $stackSpecific[] = [
                'skill' => 'Caching / queues / background jobs',
                'required_level' => 'Intermediate',
                'suggestion' => 'Use caching, queues, and async work to keep the app responsive under load.',
            ];
        }

        return array_values(array_merge($base, $stackSpecific));
    }

    private function mapUserLevel(string $developmentExperience): string
    {
        return match (strtolower($developmentExperience)) {
            'advanced' => 'Advanced',
            'intermediate' => 'Intermediate',
            default => 'Beginner',
        };
    }

    private function compareLevels(string $userLevel, string $requiredLevel): string
    {
        $levels = [
            'Beginner' => 1,
            'Intermediate' => 2,
            'Advanced' => 3,
        ];

        $difference = ($levels[$requiredLevel] ?? 1) - ($levels[$userLevel] ?? 1);

        return match (true) {
            $difference <= 0 => 'No gap',
            $difference === 1 => 'Small gap',
            default => 'Medium gap',
        };
    }
}
