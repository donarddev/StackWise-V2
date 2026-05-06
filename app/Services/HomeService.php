<?php

namespace App\Services;

class HomeService
{
    /**
     * @return array<string, mixed>
     */
    public function getHomePageData(): array
    {
        return [
            'hero' => $this->heroSection(),
            'decisionPanel' => $this->decisionIntelligencePanel(),
            'process' => $this->processSection(),
            'modules' => $this->coreModulesSection(),
            'benefits' => $this->benefitsSection(),
            'cta' => $this->finalCallToAction(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function heroSection(): array
    {
        return [
            'badge' => 'Student Decision Support System',
            'headline' => 'Choose the right language, framework, and SDLC model for your project.',
            'description' => 'StackWise AI analyzes project type, complexity, team size, preferred platform, development experience, timeline, and project goal to generate explainable recommendations for student projects and class presentations.',
            'supportingText' => 'Rule-based recommendation engine today, AI-assisted scoring planned next.',
            'highlights' => [
                'Explainable outputs',
                'Built for beginners',
                'Saved recommendation history',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decisionIntelligencePanel(): array
    {
        return [
            'title' => 'Decision Intelligence Panel',
            'description' => 'See the compact signals StackWise AI weighs before preparing a recommendation report.',
            'items' => [
                [
                    'title' => 'Language Recommendation',
                    'description' => 'Matches the project shape with a language that fits the team and timeline.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Framework Matching',
                    'description' => 'Connects the selected language to a practical framework choice.',
                    'badge' => 'Active',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'SDLC Model Selection',
                    'description' => 'Chooses a delivery model that fits the project pace and complexity.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Confidence Score',
                    'description' => 'Communicates how strong the recommendation match is for the input.',
                    'badge' => 'Preview',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'Risk Analysis',
                    'description' => 'Highlights the tradeoffs, blockers, and implementation concerns to expect.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Skill Gap Review',
                    'description' => 'Summarizes what a student team may still need to learn or prepare.',
                    'badge' => 'Preview',
                    'badgeTone' => 'slate',
                ],
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
            'description' => 'A short, student-friendly process that turns project requirements into a clear recommendation report.',
            'steps' => [
                [
                    'step' => '01',
                    'title' => 'Enter Project Details',
                    'description' => 'Share the project type, team size, timeline, platform, and your experience level.',
                ],
                [
                    'step' => '02',
                    'title' => 'Analyze Requirements',
                    'description' => 'StackWise AI interprets the context and identifies the best-fit decision signals.',
                ],
                [
                    'step' => '03',
                    'title' => 'Generate Recommendation',
                    'description' => 'The engine produces a language, framework, and SDLC model with explanation.',
                ],
                [
                    'step' => '04',
                    'title' => 'Review Roadmap and Risks',
                    'description' => 'Check the alternatives, risk notes, skill gaps, and roadmap guidance before starting.',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function coreModulesSection(): array
    {
        return [
            'eyebrow' => 'Platform',
            'title' => 'Core Modules',
            'description' => 'The current product surface already covers the main parts of the student decision-support flow.',
            'items' => [
                [
                    'title' => 'Recommendation Engine',
                    'description' => 'Generates the stack suggestion and stores the result for later review.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Dashboard Analytics',
                    'description' => 'Summarizes recommendation trends, confidence, and feedback activity.',
                    'badge' => 'Active',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'Recommendation History',
                    'description' => 'Lets users revisit previous submissions and compare outcomes.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Documentation Explorer',
                    'description' => 'Provides learning references for languages, frameworks, and SDLC models.',
                    'badge' => 'Active',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'Chatbot Assistant',
                    'description' => 'Offers a temporary rule-based helper for basic StackWise AI questions.',
                    'badge' => 'Preview',
                    'badgeTone' => 'amber',
                ],
                [
                    'title' => 'Feedback Collection',
                    'description' => 'Captures ratings and comments tied to saved recommendations.',
                    'badge' => 'Active',
                    'badgeTone' => 'emerald',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function benefitsSection(): array
    {
        return [
            'eyebrow' => 'Value',
            'title' => 'Why Use StackWise AI?',
            'description' => 'It gives students and beginners a structured way to compare choices without turning the answer into a black box.',
            'items' => [
                [
                    'title' => 'Beginner-friendly explanations',
                    'description' => 'Each recommendation includes plain-language reasoning that is easy to present in class.',
                    'badge' => 'Readable',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Explainable stack suggestions',
                    'description' => 'The result explains why a language, framework, and SDLC model fit the project.',
                    'badge' => 'Explainable',
                    'badgeTone' => 'teal',
                ],
                [
                    'title' => 'Student project focused',
                    'description' => 'The flow is tuned for academic projects, prototypes, and learning scenarios.',
                    'badge' => 'Focused',
                    'badgeTone' => 'emerald',
                ],
                [
                    'title' => 'Alternative stack comparison',
                    'description' => 'Users can see practical alternatives instead of settling on only one answer.',
                    'badge' => 'Compare',
                    'badgeTone' => 'slate',
                ],
                [
                    'title' => 'Risk and skill gap awareness',
                    'description' => 'The report points out likely blockers, missing knowledge, and effort gaps early.',
                    'badge' => 'Aware',
                    'badgeTone' => 'amber',
                ],
                [
                    'title' => 'Roadmap guidance',
                    'description' => 'The output suggests a practical next-step roadmap for project execution.',
                    'badge' => 'Guided',
                    'badgeTone' => 'teal',
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function finalCallToAction(): array
    {
        return [
            'title' => 'Ready to generate your first project stack?',
            'description' => 'Start with your project details and let StackWise AI prepare a clear recommendation report.',
            'button' => 'Start Recommendation',
        ];
    }
}
