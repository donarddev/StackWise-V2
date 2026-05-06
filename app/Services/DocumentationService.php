<?php

namespace App\Services;

class DocumentationService
{
    /**
     * @param  array{search?: string, category?: string}  $filters
     * @return array{
     *     languages: array<int, array<string, mixed>>,
     *     frameworks: array<int, array<string, mixed>>,
     *     sdlcModels: array<int, array<string, mixed>>,
     *     summary: array{languages: int, frameworks: int, sdlc_models: int},
     *     filters: array{search: string, category: string},
     *     hasResults: bool
     * }
     */
    public function getFilteredExplorerData(array $filters): array
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $category = (string) ($filters['category'] ?? 'all');

        $allLanguages = $this->languagesCatalog();
        $allFrameworks = $this->frameworksCatalog();
        $allSdlc = $this->sdlcCatalog();

        $languages = $this->filterItemsBySearch($allLanguages, $search);
        $frameworks = $this->filterItemsBySearch($allFrameworks, $search);
        $sdlcModels = $this->filterItemsBySearch($allSdlc, $search);

        [$languages, $frameworks, $sdlcModels] = $this->applyCategoryScope(
            $category,
            $languages,
            $frameworks,
            $sdlcModels,
        );

        return [
            'languages' => $languages,
            'frameworks' => $frameworks,
            'sdlcModels' => $sdlcModels,
            'summary' => [
                'languages' => count($allLanguages),
                'frameworks' => count($allFrameworks),
                'sdlc_models' => count($allSdlc),
            ],
            'filters' => [
                'search' => $search,
                'category' => $category,
            ],
            'hasResults' => $languages !== [] || $frameworks !== [] || $sdlcModels !== [],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function filterItemsBySearch(array $items, string $search): array
    {
        if ($search === '') {
            return $items;
        }

        return array_values(array_filter(
            $items,
            fn (array $item): bool => $this->itemMatchesSearch($item, $search),
        ));
    }

    private function itemMatchesSearch(array $item, string $search): bool
    {
        $needle = mb_strtolower($search);
        $haystack = mb_strtolower($this->flattenItemSearchText($item));

        return str_contains($haystack, $needle);
    }

    private function flattenItemSearchText(array $item): string
    {
        $segments = [
            (string) ($item['name'] ?? ''),
            (string) ($item['description'] ?? ''),
            (string) ($item['best_for'] ?? ''),
            (string) ($item['recommended_when'] ?? ''),
            (string) ($item['avoid_when'] ?? ''),
            (string) ($item['recommendation_note'] ?? ''),
            (string) ($item['related_language'] ?? ''),
            (string) ($item['example_project'] ?? ''),
            (string) ($item['difficulty'] ?? ''),
        ];

        foreach (['advantages', 'disadvantages', 'limitations', 'common_frameworks', 'best_fit_labels'] as $key) {
            if (! empty($item[$key]) && is_array($item[$key])) {
                $segments[] = implode(' ', array_map('strval', $item[$key]));
            }
        }

        return implode(' ', $segments);
    }

    /**
     * @param  array<int, array<string, mixed>>  $languages
     * @param  array<int, array<string, mixed>>  $frameworks
     * @param  array<int, array<string, mixed>>  $sdlcModels
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>, 2: array<int, array<string, mixed>>}
     */
    private function applyCategoryScope(
        string $category,
        array $languages,
        array $frameworks,
        array $sdlcModels,
    ): array {
        return match ($category) {
            'languages' => [$languages, [], []],
            'frameworks' => [[], $frameworks, []],
            'sdlc_models' => [[], [], $sdlcModels],
            default => [$languages, $frameworks, $sdlcModels],
        };
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function languagesCatalog(): array
    {
        return [
            $this->languageEntry(
                name: 'Python',
                difficulty: 'Beginner',
                description: 'A beginner-friendly language that is widely used for AI, automation, data analysis, and backend development.',
                bestFor: 'AI projects, data science, scripting, and API development',
                advantages: [
                    'Easy to read and learn',
                    'Strong AI and data ecosystem',
                    'Large community support',
                ],
                disadvantages: [
                    'Can be slower than compiled languages',
                    'Not ideal for every mobile or frontend task',
                ],
                commonFrameworks: ['FastAPI', 'Django', 'Flask'],
                bestFitLabels: ['Best for AI/Data', 'Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want fast prototyping for data, APIs, or ML coursework.',
                avoidWhen: 'You need native mobile UI or the lightest possible deployment footprint.',
                recommendationNote: 'Often matched with AI-focused or API-based projects.',
            ),
            $this->languageEntry(
                name: 'PHP',
                difficulty: 'Beginner',
                description: 'A popular server-side language used for building dynamic web applications and content-driven systems.',
                bestFor: 'Web applications, CRUD systems, and student projects',
                advantages: [
                    'Easy setup with Laravel',
                    'Very common for web development',
                    'Good for rapid prototypes',
                ],
                disadvantages: [
                    'Less suitable for native mobile apps',
                    'Needs good structure to stay organized',
                ],
                commonFrameworks: ['Laravel', 'Symfony', 'CodeIgniter'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You are building a traditional web system with forms, auth, and databases.',
                avoidWhen: 'Your project is primarily a native mobile app without a web backend.',
                recommendationNote: 'May be recommended for beginner web systems.',
            ),
            $this->languageEntry(
                name: 'JavaScript',
                difficulty: 'Beginner to Intermediate',
                description: 'The main language of the web, used for interactive websites and many server-side applications with Node.js.',
                bestFor: 'Interactive websites, real-time apps, and full-stack JavaScript projects',
                advantages: [
                    'Runs in every browser',
                    'Works on both front end and back end',
                    'Huge ecosystem of libraries',
                ],
                disadvantages: [
                    'Can become complex in large projects',
                    'Weak typing unless TypeScript is used',
                ],
                commonFrameworks: ['React', 'Vue', 'Node.js'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want one language across browser and server, or a rich interactive UI.',
                avoidWhen: 'You prefer a strongly typed enterprise stack without a JS toolchain.',
                recommendationNote: 'Commonly paired with React or Node for full-stack student apps.',
            ),
            $this->languageEntry(
                name: 'Java',
                difficulty: 'Intermediate',
                description: 'A strong object-oriented language that is common in enterprise systems, Android development, and large applications.',
                bestFor: 'Enterprise systems, Android apps, and large business software',
                advantages: [
                    'Very stable and scalable',
                    'Strong object-oriented structure',
                    'Common in enterprise environments',
                ],
                disadvantages: [
                    'More verbose than some newer languages',
                    'Can feel heavy for small student projects',
                ],
                commonFrameworks: ['Spring Boot', 'JavaFX', 'Hibernate'],
                bestFitLabels: ['Best for Enterprise', 'Best for Mobile Apps'],
                recommendedWhen: 'Your brief mentions Android, corporate standards, or long-lived backends.',
                avoidWhen: 'You need the quickest path for a tiny static site or one-file prototype.',
                recommendationNote: 'Often suggested when the scenario looks enterprise- or Android-heavy.',
            ),
            $this->languageEntry(
                name: 'Dart',
                difficulty: 'Beginner to Intermediate',
                description: 'A modern language created by Google and commonly used with Flutter for cross-platform app development.',
                bestFor: 'Mobile apps, cross-platform UI, and simple app prototypes',
                advantages: [
                    'Works well with Flutter',
                    'Supports one codebase for multiple platforms',
                    'Good for UI-focused development',
                ],
                disadvantages: [
                    'Smaller ecosystem than JavaScript or Python',
                    'Less common outside Flutter development',
                ],
                commonFrameworks: ['Flutter', 'Flame'],
                bestFitLabels: ['Best for Mobile Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want polished mobile UIs from a single codebase.',
                avoidWhen: 'You only need a simple website with no app store delivery.',
                recommendationNote: 'Typically appears when mobile or Flutter is highlighted in requirements.',
            ),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function frameworksCatalog(): array
    {
        return [
            $this->frameworkEntry(
                name: 'Laravel',
                relatedLanguage: 'PHP',
                difficulty: 'Beginner',
                description: 'A clean and powerful framework for building web applications using MVC architecture.',
                bestFor: 'Student web apps, dashboards, CRUD systems, and admin panels',
                advantages: [
                    'Easy routing and validation',
                    'Great developer experience',
                    'Strong ecosystem',
                ],
                limitations: [
                    'Not ideal for mobile apps directly',
                    'Requires good structure as the project grows',
                ],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You need auth, MVC structure, and rapid backend features.',
                avoidWhen: 'You only need a static site or a serverless function with no framework.',
                recommendationNote: 'May be recommended for beginner web systems.',
            ),
            $this->frameworkEntry(
                name: 'FastAPI',
                relatedLanguage: 'Python',
                difficulty: 'Intermediate',
                description: 'A modern Python framework for building fast APIs with clear documentation support.',
                bestFor: 'AI services, API backends, and data-driven systems',
                advantages: [
                    'Very fast for APIs',
                    'Automatic API docs',
                    'Easy to connect with Python tools',
                ],
                limitations: [
                    'Smaller full-stack features than Django',
                    'Needs a separate front end for complete apps',
                ],
                bestFitLabels: ['Best for AI/Data', 'Best for Web Apps'],
                recommendedWhen: 'You are exposing models or AI pipelines through REST APIs.',
                avoidWhen: 'You need Django-style admin and batteries-included ORM conventions on day one.',
                recommendationNote: 'Often matched with AI-focused or API-based projects.',
            ),
            $this->frameworkEntry(
                name: 'React',
                relatedLanguage: 'JavaScript',
                difficulty: 'Intermediate',
                description: 'A front-end library used to build interactive user interfaces with reusable components.',
                bestFor: 'Dynamic web interfaces and single-page applications',
                advantages: [
                    'Component-based design',
                    'Large ecosystem',
                    'Great for interactive UIs',
                ],
                limitations: [
                    'Needs extra tools for routing and state management',
                    'Not a complete backend solution',
                ],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'Your users need rich interactions, dashboards, or SPA-style flows.',
                avoidWhen: 'You only need basic server-rendered pages without a JS build step.',
                recommendationNote: 'Frequently recommended alongside API backends for modern web apps.',
            ),
            $this->frameworkEntry(
                name: 'Node.js',
                relatedLanguage: 'JavaScript / TypeScript',
                difficulty: 'Intermediate',
                description: 'A JavaScript runtime used to build fast server-side applications and real-time systems.',
                bestFor: 'Chat apps, APIs, live updates, and event-driven systems',
                advantages: [
                    'Uses one language across the stack',
                    'Strong for real-time features',
                    'Large package ecosystem',
                ],
                limitations: [
                    'Can become complex without good structure',
                    'Not always the easiest option for beginners',
                ],
                bestFitLabels: ['Best for Web Apps', 'Best for Changing Requirements'],
                recommendedWhen: 'You mention websockets, live dashboards, or heavy I/O concurrency.',
                avoidWhen: 'Your team is brand new to async programming and the scope is very small.',
                recommendationNote: 'Shows up when real-time or full-stack JavaScript fits the brief.',
            ),
            $this->frameworkEntry(
                name: 'Flutter',
                relatedLanguage: 'Dart',
                difficulty: 'Beginner to Intermediate',
                description: 'A UI toolkit for building cross-platform mobile, web, and desktop applications from one codebase.',
                bestFor: 'Mobile apps and cross-platform student projects',
                advantages: [
                    'One codebase for multiple platforms',
                    'Beautiful UI development',
                    'Fast development with widgets',
                ],
                limitations: [
                    'Requires learning Dart',
                    'May be larger than a simple native app',
                ],
                bestFitLabels: ['Best for Mobile Apps', 'Best for Student Projects'],
                recommendedWhen: 'You need iOS and Android from one project codebase.',
                avoidWhen: 'You only target web with no mobile requirement.',
                recommendationNote: 'Common pick when mobile delivery is explicit in your requirements.',
            ),
            $this->frameworkEntry(
                name: 'Spring Boot',
                relatedLanguage: 'Java',
                difficulty: 'Intermediate to Advanced',
                description: 'A Java framework that simplifies enterprise backend development and production-ready services.',
                bestFor: 'Enterprise applications, APIs, and large business systems',
                advantages: [
                    'Very scalable',
                    'Good for enterprise architecture',
                    'Strong ecosystem',
                ],
                limitations: [
                    'More setup than Laravel or FastAPI',
                    'Can feel heavy for small student projects',
                ],
                bestFitLabels: ['Best for Enterprise', 'Best for Web Apps'],
                recommendedWhen: 'Requirements read like regulated, large-team, or long-term services.',
                avoidWhen: 'You need the lightest possible stack for a weekend prototype.',
                recommendationNote: 'Often matched with enterprise-style Java scenarios.',
            ),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sdlcCatalog(): array
    {
        return [
            $this->sdlcEntry(
                name: 'Agile',
                description: 'An iterative model that delivers work in small steps and welcomes feedback during development.',
                bestFor: 'Projects with changing requirements or short timelines',
                advantages: [
                    'Flexible and adaptive',
                    'Good for teamwork and feedback',
                    'Helps deliver working features early',
                ],
                disadvantages: [
                    'Needs good communication',
                    'Scope can grow if not controlled',
                ],
                exampleProject: 'A student web app that changes features during development',
                bestFitLabels: ['Best for Changing Requirements', 'Best for Student Projects'],
                recommendedWhen: 'Stakeholders expect frequent changes or demos every sprint.',
                avoidWhen: 'Everything is fixed upfront with legal sign-off on a frozen spec.',
                recommendationNote: 'Useful when requirements are expected to change.',
            ),
            $this->sdlcEntry(
                name: 'Waterfall',
                description: 'A step-by-step model where each phase is completed before the next one starts.',
                bestFor: 'Stable, well-defined projects with clear requirements',
                advantages: [
                    'Simple to understand',
                    'Easy to plan early',
                    'Works well for fixed scopes',
                ],
                disadvantages: [
                    'Harder to change later',
                    'Feedback comes late in the process',
                ],
                exampleProject: 'A small internal system with fixed requirements',
                bestFitLabels: ['Best for Fixed Requirements', 'Best for Student Projects'],
                recommendedWhen: 'Your scope, budget, and timeline are already locked.',
                avoidWhen: 'You know the idea will pivot weekly.',
                recommendationNote: 'Fits classroom projects with a frozen rubric or charter.',
            ),
            $this->sdlcEntry(
                name: 'Iterative',
                description: 'A model that improves the project through repeated cycles and gradual refinement.',
                bestFor: 'Projects that need improvements over multiple versions',
                advantages: [
                    'Allows gradual improvement',
                    'Useful for learning and experimentation',
                    'Reduces risk through repeated reviews',
                ],
                disadvantages: [
                    'May take more planning time',
                    'Final structure can shift often',
                ],
                exampleProject: 'A prototype that is improved after each class review',
                bestFitLabels: ['Best for Changing Requirements', 'Best for Student Projects'],
                recommendedWhen: 'You plan multiple revisions based on mentor feedback.',
                avoidWhen: 'You must ship once with no rework cycles.',
                recommendationNote: 'Helpful for coursework with staged submissions.',
            ),
            $this->sdlcEntry(
                name: 'Spiral',
                description: 'A risk-focused model that combines planning, prototyping, testing, and review in repeated cycles.',
                bestFor: 'High-risk or complex projects',
                advantages: [
                    'Strong focus on risk control',
                    'Good for large or uncertain projects',
                    'Supports prototyping',
                ],
                disadvantages: [
                    'More complex to manage',
                    'Can be too heavy for small projects',
                ],
                exampleProject: 'A complex AI or enterprise system with uncertain requirements',
                bestFitLabels: ['Best for Enterprise', 'Best for Changing Requirements'],
                recommendedWhen: 'Risk is high—new tech, unclear data, or strict compliance.',
                avoidWhen: 'The project is a single CRUD app with a known stack.',
                recommendationNote: 'Shows up when uncertainty or risk dominates the brief.',
            ),
        ];
    }

    /**
     * @param  list<string>  $advantages
     * @param  list<string>  $disadvantages
     * @param  list<string>  $commonFrameworks
     * @param  list<string>  $bestFitLabels
     * @return array<string, mixed>
     */
    private function languageEntry(
        string $name,
        string $difficulty,
        string $description,
        string $bestFor,
        array $advantages,
        array $disadvantages,
        array $commonFrameworks,
        array $bestFitLabels,
        string $recommendedWhen,
        string $avoidWhen,
        string $recommendationNote,
    ): array {
        return [
            'name' => $name,
            'category' => 'language',
            'difficulty' => $difficulty,
            'description' => $description,
            'best_for' => $bestFor,
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'limitations' => $disadvantages,
            'common_frameworks' => $commonFrameworks,
            'related_language' => null,
            'example_project' => null,
            'best_fit_labels' => $bestFitLabels,
            'recommended_when' => $recommendedWhen,
            'avoid_when' => $avoidWhen,
            'recommendation_note' => $recommendationNote,
        ];
    }

    /**
     * @param  list<string>  $advantages
     * @param  list<string>  $limitations
     * @param  list<string>  $bestFitLabels
     * @return array<string, mixed>
     */
    private function frameworkEntry(
        string $name,
        string $relatedLanguage,
        string $difficulty,
        string $description,
        string $bestFor,
        array $advantages,
        array $limitations,
        array $bestFitLabels,
        string $recommendedWhen,
        string $avoidWhen,
        string $recommendationNote,
    ): array {
        return [
            'name' => $name,
            'category' => 'framework',
            'difficulty' => $difficulty,
            'description' => $description,
            'best_for' => $bestFor,
            'advantages' => $advantages,
            'disadvantages' => $limitations,
            'limitations' => $limitations,
            'common_frameworks' => [],
            'related_language' => $relatedLanguage,
            'example_project' => null,
            'best_fit_labels' => $bestFitLabels,
            'recommended_when' => $recommendedWhen,
            'avoid_when' => $avoidWhen,
            'recommendation_note' => $recommendationNote,
        ];
    }

    /**
     * @param  list<string>  $advantages
     * @param  list<string>  $disadvantages
     * @param  list<string>  $bestFitLabels
     * @return array<string, mixed>
     */
    private function sdlcEntry(
        string $name,
        string $description,
        string $bestFor,
        array $advantages,
        array $disadvantages,
        string $exampleProject,
        array $bestFitLabels,
        string $recommendedWhen,
        string $avoidWhen,
        string $recommendationNote,
    ): array {
        return [
            'name' => $name,
            'category' => 'sdlc_model',
            'difficulty' => 'Concept',
            'description' => $description,
            'best_for' => $bestFor,
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'limitations' => $disadvantages,
            'common_frameworks' => [],
            'related_language' => null,
            'example_project' => $exampleProject,
            'best_fit_labels' => $bestFitLabels,
            'recommended_when' => $recommendedWhen,
            'avoid_when' => $avoidWhen,
            'recommendation_note' => $recommendationNote,
        ];
    }
}
