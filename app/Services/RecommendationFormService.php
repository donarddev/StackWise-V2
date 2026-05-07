<?php

namespace App\Services;

class RecommendationFormService
{
    /**
     * @return array<string, mixed>
     */
    public function getFormPageData(): array
    {
        return [
            'header' => $this->headerData(),
            'sections' => $this->sectionsData(),
            'guidance' => $this->guidanceData(),
            'trustCards' => $this->trustCardsData(),
            'errorNotice' => 'Please review the highlighted fields before generating a recommendation.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function headerData(): array
    {
        return [
            'badge' => 'Project Assessment',
            'title' => 'Tell StackWise AI about your project',
            'description' => 'Provide your project details so the system can recommend a suitable programming language, framework, and SDLC model.',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sectionsData(): array
    {
        return [
            [
                'step' => '01',
                'title' => 'Project Profile',
                'description' => 'Describe what you are building and what outcome you want the project to achieve.',
                'gridClass' => 'grid gap-4 lg:grid-cols-2',
                'fields' => [
                    [
                        'name' => 'project_name',
                        'label' => 'Project Name',
                        'type' => 'text',
                        'placeholder' => 'Example: Online Enrollment System',
                        'hint' => 'Use a clear title so the recommendation report stays easy to read.',
                    ],
                    [
                        'name' => 'project_type',
                        'label' => 'Project Type',
                        'type' => 'select',
                        'placeholder' => 'Select project type',
                        'hint' => 'Choose the closest project category.',
                        'options' => $this->projectTypeOptions(),
                    ],
                    [
                        'name' => 'selected_features',
                        'label' => 'Selected Features',
                        'type' => 'multi_select',
                        'placeholder' => 'Select one or more features (Ctrl/Cmd + Click)',
                        'hint' => 'Pick the most important features so the engine can score realistically.',
                        'options' => $this->featureOptions(),
                        'columnSpan' => 'lg:col-span-2',
                    ],
                    [
                        'name' => 'project_goal',
                        'label' => 'Project Goal',
                        'type' => 'textarea',
                        'placeholder' => 'Describe the main purpose, features, and expected outcome of your project...',
                        'hint' => 'Mention the main features, intended users, and the final result you want to achieve.',
                        'columnSpan' => 'lg:col-span-2',
                        'rows' => 6,
                    ],
                ],
            ],
            [
                'step' => '02',
                'title' => 'Development Context',
                'description' => 'Share your team size, complexity, and delivery timeline.',
                'gridClass' => 'grid gap-4 md:grid-cols-3',
                'fields' => [
                    [
                        'name' => 'team_size',
                        'label' => 'Team Size',
                        'type' => 'number',
                        'placeholder' => 'Example: 3',
                        'hint' => 'Enter the number of people working on the project.',
                        'min' => 1,
                    ],
                    [
                        'name' => 'complexity',
                        'label' => 'Complexity',
                        'type' => 'select',
                        'placeholder' => 'Select complexity',
                        'options' => $this->complexityOptions(),
                    ],
                    [
                        'name' => 'timeline',
                        'label' => 'Timeline',
                        'type' => 'select',
                        'placeholder' => 'Select timeline',
                        'hint' => 'Choose the timeframe that best matches your project deadline.',
                        'options' => $this->timelineOptions(),
                    ],
                    [
                        'name' => 'requirements_stability',
                        'label' => 'Requirements Stability',
                        'type' => 'select',
                        'placeholder' => 'Select stability',
                        'hint' => 'Helps decide between Agile, Waterfall, and risk-driven SDLC models.',
                        'options' => $this->requirementsStabilityOptions(),
                    ],
                    [
                        'name' => 'stakeholder_involvement',
                        'label' => 'Stakeholder Involvement',
                        'type' => 'select',
                        'placeholder' => 'Select involvement',
                        'hint' => 'More stakeholder feedback often favors iterative SDLC models.',
                        'options' => $this->stakeholderOptions(),
                    ],
                ],
            ],
            [
                'step' => '03',
                'title' => 'Technical Preference',
                'description' => 'Tell StackWise AI about the platform and your current development experience.',
                'gridClass' => 'grid gap-4 lg:grid-cols-2',
                'fields' => [
                    [
                        'name' => 'preferred_platform',
                        'label' => 'Preferred Platform',
                        'type' => 'select',
                        'placeholder' => 'Select preferred platform',
                        'options' => $this->platformOptions(),
                    ],
                    [
                        'name' => 'development_experience',
                        'label' => 'Development Experience',
                        'type' => 'select',
                        'placeholder' => 'Select experience level',
                        'hint' => 'Be honest about your current level so the explanation stays practical.',
                        'options' => $this->experienceOptions(),
                    ],
                    [
                        'name' => 'scalability_needs',
                        'label' => 'Scalability Needs',
                        'type' => 'select',
                        'placeholder' => 'Select scalability needs',
                        'options' => $this->scalabilityOptions(),
                    ],
                    [
                        'name' => 'performance_requirements',
                        'label' => 'Performance Requirements',
                        'type' => 'select',
                        'placeholder' => 'Select performance needs',
                        'options' => $this->performanceOptions(),
                    ],
                    [
                        'name' => 'security_requirements',
                        'label' => 'Security Requirements',
                        'type' => 'select',
                        'placeholder' => 'Select security level',
                        'options' => $this->securityOptions(),
                    ],
                    [
                        'name' => 'budget_constraints',
                        'label' => 'Budget Constraints',
                        'type' => 'select',
                        'placeholder' => 'Select budget constraints',
                        'options' => $this->budgetOptions(),
                    ],
                    [
                        'name' => 'maintenance_expectations',
                        'label' => 'Maintenance Expectations',
                        'type' => 'select',
                        'placeholder' => 'Select maintenance expectations',
                        'options' => $this->maintenanceOptions(),
                    ],
                    [
                        'name' => 'deployment_preference',
                        'label' => 'Deployment Preference',
                        'type' => 'select',
                        'placeholder' => 'Select deployment preference',
                        'options' => $this->deploymentOptions(),
                    ],
                ],
            ],
            [
                'step' => '04',
                'title' => 'Generate Decision Report',
                'description' => 'Your submission will produce an explainable report that is ready for review or presentation.',
                'summary' => [
                    'title' => 'What the report includes',
                    'description' => 'Language recommendation, framework matching, SDLC model selection, confidence score, alternatives, risk analysis, skill gaps, and a roadmap.',
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function guidanceData(): array
    {
        return [
            [
                'title' => 'Project type',
                'description' => 'Helps identify whether the project is web, mobile, desktop, API, or AI-focused.',
            ],
            [
                'title' => 'Complexity',
                'description' => 'Signals how much structure and risk management the report should recommend.',
            ],
            [
                'title' => 'Team size',
                'description' => 'Affects the pace, division of work, and practical delivery recommendations.',
            ],
            [
                'title' => 'Preferred platform',
                'description' => 'Aligns the stack with the environment you actually want to build for.',
            ],
            [
                'title' => 'Development experience',
                'description' => 'Keeps the recommendation beginner-friendly or more advanced as needed.',
            ],
            [
                'title' => 'Timeline',
                'description' => 'Helps decide between iterative and more sequential delivery approaches.',
            ],
            [
                'title' => 'Project goal keywords',
                'description' => 'The description text helps the engine detect intent, features, and expected outcomes.',
            ],
            [
                'title' => 'Scalability + security',
                'description' => 'Non-functional requirements strongly influence technology fit and SDLC model selection.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function trustCardsData(): array
    {
        return [
            [
                'title' => 'Explainable Results',
                'description' => 'Each recommendation includes reasons so you can present the logic clearly.',
            ],
            [
                'title' => 'Saved Recommendation',
                'description' => 'The generated report is stored for history, review, and later feedback.',
            ],
            [
                'title' => 'Roadmap Included',
                'description' => 'You get a practical next-step roadmap to support project planning.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function projectTypeOptions(): array
    {
        return [
            ['value' => 'web application', 'label' => 'Web Application'],
            ['value' => 'mobile application', 'label' => 'Mobile Application'],
            ['value' => 'desktop application', 'label' => 'Desktop Application'],
            ['value' => 'api system', 'label' => 'API System'],
            ['value' => 'ai system', 'label' => 'AI System'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function featureOptions(): array
    {
        return [
            ['value' => 'crud', 'label' => 'CRUD (Create/Read/Update/Delete)'],
            ['value' => 'authentication', 'label' => 'Authentication / User Accounts'],
            ['value' => 'api', 'label' => 'API / Integrations'],
            ['value' => 'real-time', 'label' => 'Real-time Updates'],
            ['value' => 'chat', 'label' => 'Chat / Messaging'],
            ['value' => 'analytics', 'label' => 'Analytics / Reports'],
            ['value' => 'payments', 'label' => 'Payments'],
            ['value' => 'ai/ml', 'label' => 'AI / ML Features'],
            ['value' => 'offline-first', 'label' => 'Offline-first Mode'],
            ['value' => 'push notifications', 'label' => 'Push Notifications'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function complexityOptions(): array
    {
        return [
            ['value' => 'small', 'label' => 'Small'],
            ['value' => 'medium', 'label' => 'Medium'],
            ['value' => 'large', 'label' => 'Large'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function platformOptions(): array
    {
        return [
            ['value' => 'web', 'label' => 'Web'],
            ['value' => 'mobile', 'label' => 'Mobile'],
            ['value' => 'desktop', 'label' => 'Desktop'],
            ['value' => 'mixed', 'label' => 'Mixed'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function experienceOptions(): array
    {
        return [
            ['value' => 'beginner', 'label' => 'Beginner'],
            ['value' => 'intermediate', 'label' => 'Intermediate'],
            ['value' => 'advanced', 'label' => 'Advanced'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function timelineOptions(): array
    {
        return [
            ['value' => 'short', 'label' => 'Short'],
            ['value' => 'medium', 'label' => 'Medium'],
            ['value' => 'long', 'label' => 'Long'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function scalabilityOptions(): array
    {
        return [
            ['value' => 'low', 'label' => 'Low (small users / classroom demo)'],
            ['value' => 'medium', 'label' => 'Medium (department / campus)'],
            ['value' => 'high', 'label' => 'High (enterprise / large public users)'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function securityOptions(): array
    {
        return [
            ['value' => 'basic', 'label' => 'Basic (standard login + validation)'],
            ['value' => 'standard', 'label' => 'Standard (roles + common protections)'],
            ['value' => 'high', 'label' => 'High (sensitive data / strict controls)'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function performanceOptions(): array
    {
        return [
            ['value' => 'low', 'label' => 'Low (normal usage)'],
            ['value' => 'medium', 'label' => 'Medium (busy hours / many requests)'],
            ['value' => 'high', 'label' => 'High (real-time / high throughput)'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function budgetOptions(): array
    {
        return [
            ['value' => 'low', 'label' => 'Low (free/cheap hosting, minimal paid tools)'],
            ['value' => 'medium', 'label' => 'Medium (some paid services are okay)'],
            ['value' => 'high', 'label' => 'High (budget available for tools/infra)'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function maintenanceOptions(): array
    {
        return [
            ['value' => 'low', 'label' => 'Low (short-term / demo only)'],
            ['value' => 'medium', 'label' => 'Medium (maintain for a semester)'],
            ['value' => 'high', 'label' => 'High (long-term, handover-ready)'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function deploymentOptions(): array
    {
        return [
            ['value' => 'shared_hosting', 'label' => 'Shared Hosting'],
            ['value' => 'vps', 'label' => 'VPS / Dedicated Server'],
            ['value' => 'container', 'label' => 'Docker / Containers'],
            ['value' => 'serverless', 'label' => 'Serverless'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function requirementsStabilityOptions(): array
    {
        return [
            ['value' => 'stable', 'label' => 'Stable (mostly fixed requirements)'],
            ['value' => 'changing', 'label' => 'Changing (still evolving)'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function stakeholderOptions(): array
    {
        return [
            ['value' => 'low', 'label' => 'Low (few reviews)'],
            ['value' => 'medium', 'label' => 'Medium (regular check-ins)'],
            ['value' => 'high', 'label' => 'High (frequent demos/approvals)'],
        ];
    }
}
