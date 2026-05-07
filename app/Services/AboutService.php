<?php

namespace App\Services;

class AboutService
{
    /**
     * @return array<string, mixed>
     */
    public function getAboutPageData(): array
    {
        return [
            'hero' => $this->heroSection(),
            'problemSolution' => $this->problemSolutionSection(),
            'process' => $this->processSection(),
            'features' => $this->featuresSection(),
            'architecture' => $this->architectureSection(),
            'output' => $this->recommendationOutputSection(),
            'futureGrowth' => $this->futureGrowthSection(),
            'cta' => $this->finalCallToAction(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function heroSection(): array
    {
        return [
            'badge' => 'Student Decision Support Platform',
            'title' => 'About StackWise AI',
            'description' => 'StackWise AI helps students and beginner developers choose a suitable programming language, framework, and SDLC model by analyzing project requirements and generating explainable recommendations.',
            'highlights' => [
                [
                    'title' => 'Explainable recommendations',
                    'description' => 'Clear reasoning you can defend in class presentations and project proposals.',
                ],
                [
                    'title' => 'Beginner-friendly guidance',
                    'description' => 'Simple prompts, structured outputs, and learning resources built into the workflow.',
                ],
                [
                    'title' => 'Service-driven Laravel architecture',
                    'description' => 'Controllers stay thin with Form Requests for validation and Services for business logic.',
                ],
            ],
        ];
    }

    /**
     * @return array{problem: array{title: string, content: string}, solution: array{title: string, content: string}}
     */
    private function problemSolutionSection(): array
    {
        return [
            'problem' => [
                'title' => 'The Problem',
                'content' => 'Students and beginner developers often struggle to choose the right programming language, framework, and development process for their software projects. This can lead to mismatched technologies, unclear planning, and difficulty defending project decisions.',
            ],
            'solution' => [
                'title' => 'The StackWise Solution',
                'content' => 'StackWise AI guides users through project details such as project type, complexity, team size, platform, experience level, timeline, and project goal, then generates a structured recommendation report.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function processSection(): array
    {
        return [
            'eyebrow' => 'Workflow',
            'title' => 'How StackWise AI Works',
            'description' => 'A simple process that turns project requirements into a clear, presentation-ready decision report.',
            'steps' => [
                [
                    'step' => '01',
                    'title' => 'Enter Project Details',
                    'description' => 'Provide context like project type, platform, timeline, team size, and experience level.',
                ],
                [
                    'step' => '02',
                    'title' => 'Analyze Requirements',
                    'description' => 'StackWise AI interprets the inputs to identify the most important decision signals.',
                ],
                [
                    'step' => '03',
                    'title' => 'Generate Recommendations',
                    'description' => 'Receive a recommended language, framework, and SDLC model with explanations and trade-offs.',
                ],
                [
                    'step' => '04',
                    'title' => 'Review Report and History',
                    'description' => 'Compare alternatives, track past outputs, and refine decisions with saved history.',
                ],
            ],
        ];
    }

    /**
     * @return array{eyebrow: string, title: string, description: string, items: array<int, array{title: string, description: string, badge: string, badgeTone: string}>}
     */
    private function featuresSection(): array
    {
        return [
            'eyebrow' => 'Workspace',
            'title' => 'Core Features',
            'description' => 'Everything you need to generate and defend a project technology stack decision.',
            'items' => [
                [
                    'title' => 'Recommendation Engine',
                    'description' => 'Generates an explainable stack recommendation based on project requirements.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Dashboard Analytics',
                    'description' => 'Quick snapshot of activity and recent outputs for reporting and review.',
                    'badge' => 'Active',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'Recommendation History',
                    'description' => 'Saved recommendation reports to revisit, compare, and cite later.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Documentation Explorer',
                    'description' => 'Learn the basics of languages, frameworks, and SDLC models in one place.',
                    'badge' => 'Active',
                    'badgeTone' => 'slate',
                ],
                [
                    'title' => 'StackWise Chatbot',
                    'description' => 'Ask questions about stack choices, SDLC models, and next steps.',
                    'badge' => 'Preview',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'Feedback Collection',
                    'description' => 'Capture user feedback to improve recommendation quality over time.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
            ],
        ];
    }

    /**
     * @return array{eyebrow: string, title: string, description: string, layers: array<int, array{title: string, description: string}>}
     */
    private function architectureSection(): array
    {
        return [
            'eyebrow' => 'Architecture',
            'title' => 'Built the Laravel way',
            'description' => 'StackWise AI follows Laravel MVC with a service-driven structure to keep controllers clean and the codebase maintainable.',
            'layers' => [
                [
                    'title' => 'Controller layer (HTTP)',
                    'description' => 'Handles request/response flow and delegates work to Services.',
                ],
                [
                    'title' => 'Form Requests (validation)',
                    'description' => 'Centralizes validation rules and authorization checks.',
                ],
                [
                    'title' => 'Service layer (business logic)',
                    'description' => 'Recommendation generation, chatbot orchestration, and page data builders.',
                ],
                [
                    'title' => 'Model layer (persistence)',
                    'description' => 'Stores recommendations, feedback, and user data.',
                ],
                [
                    'title' => 'Blade + Tailwind UI',
                    'description' => 'Responsive views with reusable components and a consistent dark theme.',
                ],
                [
                    'title' => 'MySQL storage',
                    'description' => 'Database-backed history and analytics for reliable persistence.',
                ],
            ],
        ];
    }

    /**
     * @return array{eyebrow: string, title: string, description: string, items: array<int, array{title: string, description: string}>}
     */
    private function recommendationOutputSection(): array
    {
        return [
            'eyebrow' => 'Output',
            'title' => 'What the recommendation report includes',
            'description' => 'The generated report is structured to help students and beginner developers explain the “why” behind each decision.',
            'items' => [
                [
                    'title' => 'Recommended language',
                    'description' => 'A best-fit language based on the project context and student team constraints.',
                ],
                [
                    'title' => 'Recommended framework',
                    'description' => 'A practical framework choice aligned to the selected language and project shape.',
                ],
                [
                    'title' => 'Recommended SDLC model',
                    'description' => 'A delivery process that matches timeline, scope uncertainty, and complexity.',
                ],
                [
                    'title' => 'Confidence score',
                    'description' => 'A compact indicator of recommendation fit strength based on the inputs.',
                ],
                [
                    'title' => 'Explanation',
                    'description' => 'Clear reasoning and decision factors to defend in presentations.',
                ],
                [
                    'title' => 'Alternative stacks',
                    'description' => 'Other viable options when priorities or constraints change.',
                ],
                [
                    'title' => 'Risk analysis',
                    'description' => 'Known trade-offs, implementation risks, and planning considerations.',
                ],
                [
                    'title' => 'Skill gap analysis',
                    'description' => 'What the team may need to learn to deliver successfully.',
                ],
                [
                    'title' => 'Suggested roadmap',
                    'description' => 'A high-level next-steps plan to move from decision to execution.',
                ],
            ],
        ];
    }

    /**
     * @return array{eyebrow: string, title: string, description: string, items: array<int, array{title: string, description: string, badge: string, badgeTone: string}>}
     */
    private function futureGrowthSection(): array
    {
        return [
            'eyebrow' => 'Roadmap',
            'title' => 'Future growth',
            'description' => 'Planned improvements to make StackWise AI even more helpful and personalized.',
            'items' => [
                [
                    'title' => 'AI-assisted recommendation scoring',
                    'description' => 'Use AI signals to complement rule-based logic and improve match quality.',
                    'badge' => 'Planned',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'FastAPI integration for AI services',
                    'description' => 'External AI microservices for advanced scoring and structured explanations.',
                    'badge' => 'Planned',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Ollama-powered chatbot responses',
                    'description' => 'More context-aware guidance using local LLM tooling where available.',
                    'badge' => 'Planned',
                    'badgeTone' => 'slate',
                ],
                [
                    'title' => 'More advanced analytics',
                    'description' => 'Deeper insights into recommendations, feedback trends, and learning progress.',
                    'badge' => 'Planned',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'Personalized recommendations per user',
                    'description' => 'Adapt outputs to history, preferences, and the user’s skill growth over time.',
                    'badge' => 'Planned',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Admin tools for documentation and feedback',
                    'description' => 'Curate content and moderate feedback to improve the learning experience.',
                    'badge' => 'Planned',
                    'badgeTone' => 'slate',
                ],
            ],
        ];
    }

    /**
     * @return array{title: string, description: string}
     */
    private function finalCallToAction(): array
    {
        return [
            'title' => 'Ready to build your project recommendation?',
            'description' => 'Start by entering your project details and let StackWise AI prepare a clear decision report.',
        ];
    }
}
