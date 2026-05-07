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
            (string) ($item['category'] ?? ''),
            (string) ($item['short_description'] ?? ''),
            (string) ($item['description'] ?? ''),
            (string) ($item['best_for'] ?? ''),
            (string) ($item['recommended_when'] ?? ''),
            (string) ($item['avoid_when'] ?? ''),
            (string) ($item['recommendation_note'] ?? ''),
            (string) ($item['related_language'] ?? ''),
            (string) ($item['example_project'] ?? ''),
            (string) ($item['difficulty'] ?? ''),
            (string) ($item['best_fit_label'] ?? ''),
        ];

        foreach (['advantages', 'disadvantages', 'limitations', 'common_frameworks', 'best_fit_labels', 'reference_sources'] as $key) {
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
                difficulty: 'Beginner-Friendly',
                shortDescription: 'A readable language often used for AI, automation, data, and backend APIs.',
                description: 'Python focuses on clear syntax and a huge ecosystem, making it a common choice for student projects that involve AI features, data processing, scripts, or API backends.',
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
                limitations: [
                    'Performance tuning may require extra tools or native extensions',
                ],
                commonFrameworks: ['FastAPI', 'Django', 'Flask'],
                bestFitLabels: ['Best for AI/Data', 'Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want fast prototyping for data, APIs, or ML coursework.',
                avoidWhen: 'You need native mobile UI or the lightest possible deployment footprint.',
                recommendationNote: 'Often matched with AI-focused or API-based projects.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'PHP',
                difficulty: 'Beginner-Friendly',
                shortDescription: 'A server-side web language commonly used for dynamic websites and database-driven systems.',
                description: 'PHP is widely used for backend web development. With frameworks like Laravel, it becomes structured and productive for building dashboards, portals, and CRUD applications.',
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
                limitations: [
                    'Best used with a framework to keep large projects maintainable',
                ],
                commonFrameworks: ['Laravel', 'Symfony', 'CodeIgniter'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You are building a traditional web system with forms, auth, and databases.',
                avoidWhen: 'Your project is primarily a native mobile app without a web backend.',
                recommendationNote: 'May be recommended for beginner web systems.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'JavaScript',
                difficulty: 'Beginner to Intermediate',
                shortDescription: 'The language of the web, used for interactive UIs and (with Node.js) server-side apps.',
                description: 'JavaScript runs in browsers to make pages interactive, and it can also run on servers using Node.js. It is popular for full-stack projects and modern web interfaces.',
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
                limitations: [
                    'Large projects benefit from conventions, linting, and strong tooling',
                ],
                commonFrameworks: ['React', 'Vue', 'Node.js'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want one language across browser and server, or a rich interactive UI.',
                avoidWhen: 'You prefer a strongly typed enterprise stack without a JS toolchain.',
                recommendationNote: 'Commonly paired with React or Node for full-stack student apps.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'TypeScript',
                difficulty: 'Intermediate',
                shortDescription: 'A typed version of JavaScript that helps catch errors earlier and scale projects.',
                description: 'TypeScript adds types to JavaScript, which can make large front-end or Node.js codebases easier to maintain. It compiles to JavaScript, so it runs wherever JavaScript runs.',
                bestFor: 'Large web apps, team projects, safer JavaScript development, and Node.js APIs',
                advantages: [
                    'Helps prevent common runtime bugs',
                    'Improves editor autocomplete and refactoring',
                    'Scales better for bigger codebases',
                ],
                disadvantages: [
                    'Adds a build step and type learning curve',
                    'Setup can feel heavier for tiny prototypes',
                ],
                limitations: [
                    'Types improve safety but do not replace testing',
                ],
                commonFrameworks: ['Next.js', 'Angular', 'NestJS'],
                bestFitLabels: ['Best for Web Apps', 'Best for Team Projects'],
                recommendedWhen: 'You expect the project to grow, or multiple people will maintain the code.',
                avoidWhen: 'You want the simplest possible setup for a small demo.',
                recommendationNote: 'Often selected when maintainability and fewer bugs are priorities.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'Java',
                difficulty: 'Intermediate',
                shortDescription: 'A widely used object-oriented language for enterprise systems and many backend services.',
                description: 'Java is known for its portability, strong ecosystem, and use in large systems. It is common in enterprise backends, and it is a foundation for many JVM tools and frameworks.',
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
                limitations: [
                    'May require more boilerplate for simple apps compared to scripting languages',
                ],
                commonFrameworks: ['Spring Boot', 'JavaFX', 'Hibernate'],
                bestFitLabels: ['Best for Enterprise', 'Best for Mobile Apps'],
                recommendedWhen: 'Your brief mentions Android, corporate standards, or long-lived backends.',
                avoidWhen: 'You need the quickest path for a tiny static site or one-file prototype.',
                recommendationNote: 'Often suggested when the scenario looks enterprise- or Android-heavy.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'C#',
                difficulty: 'Intermediate',
                shortDescription: 'A modern language from Microsoft used for Windows apps, web APIs, and enterprise systems.',
                description: 'C# is commonly used with the .NET ecosystem. It works well for building web applications, APIs, desktop tools, and services, and it has strong tooling support.',
                bestFor: 'Enterprise apps, web APIs, Windows tools, and system dashboards',
                advantages: [
                    'Strong tooling and IDE support',
                    'Good performance and modern language features',
                    'Great fit with .NET and Azure ecosystems',
                ],
                disadvantages: [
                    'Learning the .NET ecosystem can take time',
                    'May feel “enterprise-heavy” for small projects',
                ],
                limitations: [
                    'Cross-platform is good today, but some legacy libraries can still be Windows-focused',
                ],
                commonFrameworks: ['ASP.NET Core', 'Blazor', '.NET MAUI'],
                bestFitLabels: ['Best for Enterprise', 'Best for Web Apps'],
                recommendedWhen: 'You want a structured backend with strong tooling and a clear architecture.',
                avoidWhen: 'You only need a lightweight script or a simple static site.',
                recommendationNote: 'A good fit when the project brief resembles enterprise systems or Microsoft stacks.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'Dart',
                difficulty: 'Beginner to Intermediate',
                shortDescription: 'A language used mainly with Flutter for cross-platform app development.',
                description: 'Dart is most popular as the language behind Flutter. If your goal is to build one mobile app for both Android and iOS, Dart + Flutter is a common student-friendly path.',
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
                limitations: [
                    'Career/project alignment is best when Flutter is part of the requirements',
                ],
                commonFrameworks: ['Flutter', 'Flame'],
                bestFitLabels: ['Best for Mobile Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want polished mobile UIs from a single codebase.',
                avoidWhen: 'You only need a simple website with no app store delivery.',
                recommendationNote: 'Typically appears when mobile or Flutter is highlighted in requirements.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'Kotlin',
                difficulty: 'Intermediate',
                shortDescription: 'A modern JVM language widely used for Android development.',
                description: 'Kotlin is commonly used for Android apps and can also be used for backend services. It keeps Java compatibility while offering cleaner syntax and many modern features.',
                bestFor: 'Android apps, mobile-first student projects, and JVM backend services',
                advantages: [
                    'Great for Android development',
                    'Modern language features and concise syntax',
                    'Interoperates with Java libraries',
                ],
                disadvantages: [
                    'Android tooling and architecture can be complex for beginners',
                    'Smaller ecosystem than Java (but growing)',
                ],
                limitations: [
                    'Best results usually require Android platform knowledge',
                ],
                commonFrameworks: ['Ktor', 'Spring Boot'],
                bestFitLabels: ['Best for Mobile Apps', 'Best for Enterprise'],
                recommendedWhen: 'Your project explicitly targets Android or needs tight mobile integration.',
                avoidWhen: 'You need a quick web-only prototype and have no Android requirement.',
                recommendationNote: 'Typically chosen when Android is a clear deliverable.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'Swift',
                difficulty: 'Intermediate',
                shortDescription: 'Apple’s language for building iOS and macOS apps.',
                description: 'Swift is used to create native Apple apps with smooth performance and platform features. It’s a strong choice when the project must be an iOS app or needs Apple-specific integrations.',
                bestFor: 'iOS apps, Apple ecosystem projects, and native mobile UI',
                advantages: [
                    'Excellent for native iOS performance',
                    'Good tooling in Xcode',
                    'Access to Apple platform features',
                ],
                disadvantages: [
                    'Requires macOS for development in most cases',
                    'Less suitable for cross-platform goals',
                ],
                limitations: [
                    'Team members without Macs may struggle to contribute',
                ],
                commonFrameworks: ['SwiftUI', 'UIKit'],
                bestFitLabels: ['Best for Mobile Apps'],
                recommendedWhen: 'Your project must run on iOS with native UI and device features.',
                avoidWhen: 'You need one codebase for Android + iOS and you have limited Apple hardware access.',
                recommendationNote: 'Best when iOS is a hard requirement.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'C++',
                difficulty: 'Advanced',
                shortDescription: 'A fast, low-level language often used for systems, games, and performance-heavy tasks.',
                description: 'C++ gives fine control over memory and performance. It is common in game engines, desktop software, and system-level programming, but it can be harder for beginners due to complexity.',
                bestFor: 'Game development, performance-critical apps, and systems programming',
                advantages: [
                    'High performance',
                    'Great for low-level control',
                    'Used in many engines and system tools',
                ],
                disadvantages: [
                    'Steeper learning curve',
                    'Memory management and debugging can be challenging',
                ],
                limitations: [
                    'Building web apps typically requires additional tooling and is less beginner-friendly',
                ],
                commonFrameworks: ['Qt', 'Unreal Engine'],
                bestFitLabels: ['Best for Performance'],
                recommendedWhen: 'You need maximum performance or you are building desktop/game components.',
                avoidWhen: 'You want the fastest path to a web CRUD system or an API.',
                recommendationNote: 'Usually selected only when performance or low-level requirements are explicit.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'Go',
                difficulty: 'Intermediate',
                shortDescription: 'A simple compiled language designed for fast, reliable backend services.',
                description: 'Go (Golang) is often used for APIs, microservices, and tools that need good performance and easy deployment. Its standard library helps with networking and concurrency.',
                bestFor: 'APIs, backend services, tooling, and scalable network applications',
                advantages: [
                    'Fast builds and good performance',
                    'Great concurrency support',
                    'Simple deployment (single binary in many cases)',
                ],
                disadvantages: [
                    'Less flexible for UI work',
                    'Ecosystem for some domains is smaller than Python/JS',
                ],
                limitations: [
                    'Not typically used for front-end UI; you will pair it with a web frontend',
                ],
                commonFrameworks: ['Gin', 'Fiber'],
                bestFitLabels: ['Best for Web Apps', 'Best for Performance'],
                recommendedWhen: 'You want a clean API backend that is easy to deploy and run.',
                avoidWhen: 'Your project needs heavy data-science libraries or rapid scripting.',
                recommendationNote: 'Fits backend-focused projects that care about performance and simplicity.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->languageEntry(
                name: 'Ruby',
                difficulty: 'Beginner to Intermediate',
                shortDescription: 'A developer-friendly language often used for rapid web development.',
                description: 'Ruby is known for readable syntax and fast development workflows. It is popular for building web applications quickly, especially with Ruby on Rails.',
                bestFor: 'Rapid web prototypes, CRUD apps, and learning MVC web development',
                advantages: [
                    'Readable and expressive syntax',
                    'Great for rapid prototyping',
                    'Strong conventions with Rails',
                ],
                disadvantages: [
                    'Can be slower than some compiled languages',
                    'Smaller community in some regions compared to JS/Python',
                ],
                limitations: [
                    'Hosting and deployment options may be less common depending on your environment',
                ],
                commonFrameworks: ['Ruby on Rails', 'Sinatra'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want to learn MVC quickly or build a web app fast with strong conventions.',
                avoidWhen: 'Your institution or team requires a specific enterprise stack (Java/.NET) only.',
                recommendationNote: 'A good option for fast web development when Rails is acceptable.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
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
                difficulty: 'Beginner to Intermediate',
                shortDescription: 'A PHP web framework for structured, secure, database-driven applications.',
                description: 'Laravel helps you build web apps faster by providing routing, validation, authentication helpers, and database tools. It follows MVC patterns and fits many student CRUD and dashboard systems.',
                bestFor: 'Student web apps, dashboards, CRUD systems, and admin panels',
                advantages: [
                    'Easy routing and validation',
                    'Great developer experience',
                    'Strong ecosystem',
                ],
                disadvantages: [
                    'Requires learning Laravel conventions',
                    'Not intended for native mobile apps directly',
                ],
                limitations: ['Best suited for backend + server-rendered web apps (or APIs paired with a frontend).'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You need auth, MVC structure, and rapid backend features.',
                avoidWhen: 'You only need a static site or a serverless function with no framework.',
                recommendationNote: 'May be recommended for beginner web systems.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'FastAPI',
                relatedLanguage: 'Python',
                difficulty: 'Intermediate',
                shortDescription: 'A modern Python framework for building fast APIs with automatic docs.',
                description: 'FastAPI is built for creating API backends. It is a strong fit when your project needs REST endpoints for an AI model, mobile app, or dashboard frontend.',
                bestFor: 'AI services, API backends, and data-driven systems',
                advantages: [
                    'Very fast for APIs',
                    'Automatic API docs',
                    'Easy to connect with Python tools',
                ],
                disadvantages: ['Not “full-stack” by default (you usually pair it with a frontend).'],
                limitations: ['You will typically add your own auth, admin UI, and frontend stack choices.'],
                bestFitLabels: ['Best for AI/Data', 'Best for Web Apps'],
                recommendedWhen: 'You are exposing models or AI pipelines through REST APIs.',
                avoidWhen: 'You need Django-style admin and batteries-included ORM conventions on day one.',
                recommendationNote: 'Often matched with AI-focused or API-based projects.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'Django',
                relatedLanguage: 'Python',
                difficulty: 'Intermediate',
                shortDescription: 'A “batteries-included” Python web framework for full web applications.',
                description: 'Django provides an ORM, authentication helpers, and an admin panel. It is useful when you want a complete web framework and a strong structure from the start.',
                bestFor: 'Full web apps, admin dashboards, and database-driven systems',
                advantages: [
                    'Built-in admin interface',
                    'Strong structure for large apps',
                    'Good security defaults',
                ],
                disadvantages: [
                    'Can feel heavy for tiny apps',
                    'Learning the Django “way” takes time',
                ],
                limitations: ['If you only need an API, FastAPI may feel simpler.'],
                bestFitLabels: ['Best for Web Apps'],
                recommendedWhen: 'You want a full web system with database models, admin tools, and authentication.',
                avoidWhen: 'You only need a small API service with minimal features.',
                recommendationNote: 'A good default when you want a full Python web framework.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'React',
                relatedLanguage: 'JavaScript',
                difficulty: 'Intermediate',
                shortDescription: 'A UI library for building interactive web interfaces with components.',
                description: 'React helps you build dynamic user interfaces by composing reusable components. It is commonly used for dashboards, SPAs, and frontends that consume APIs.',
                bestFor: 'Dynamic web interfaces and single-page applications',
                advantages: [
                    'Component-based design',
                    'Large ecosystem',
                    'Great for interactive UIs',
                ],
                disadvantages: ['Requires a build toolchain and ecosystem choices.'],
                limitations: ['React is UI-only; you still need a backend or API for data.'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'Your users need rich interactions, dashboards, or SPA-style flows.',
                avoidWhen: 'You only need basic server-rendered pages without a JS build step.',
                recommendationNote: 'Frequently recommended alongside API backends for modern web apps.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'Vue.js',
                relatedLanguage: 'JavaScript',
                difficulty: 'Beginner to Intermediate',
                shortDescription: 'A progressive JavaScript framework for building web UIs.',
                description: 'Vue is often chosen for approachable component-based UI development. It works well for SPAs and can also enhance server-rendered pages with interactive widgets.',
                bestFor: 'Interactive web UIs, SPAs, and dashboards',
                advantages: [
                    'Beginner-friendly learning curve',
                    'Component-based UI design',
                    'Good documentation and ecosystem',
                ],
                disadvantages: [
                    'Still requires a JS build toolchain for larger apps',
                    'Ecosystem choices can vary by project',
                ],
                limitations: ['Vue focuses on the frontend; you still need a backend/API for data.'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want a friendly frontend framework for interactive pages.',
                avoidWhen: 'You do not want any frontend build step at all.',
                recommendationNote: 'Common alternative to React for student-friendly frontends.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'Next.js',
                relatedLanguage: 'JavaScript / TypeScript',
                difficulty: 'Intermediate',
                shortDescription: 'A React framework that supports routing, SSR, and full-stack patterns.',
                description: 'Next.js adds app routing, server-side rendering, and backend capabilities to React. It is useful for SEO-friendly pages, dashboards, and projects that want frontend + backend in one codebase.',
                bestFor: 'Web apps with SSR, SEO needs, and full-stack React projects',
                advantages: [
                    'Built-in routing and SSR support',
                    'Good performance patterns',
                    'Works well with TypeScript',
                ],
                disadvantages: [
                    'More concepts than plain React',
                    'Can be overkill for simple sites',
                ],
                limitations: ['Still benefits from a clear data layer and architecture planning.'],
                bestFitLabels: ['Best for Web Apps', 'Best for Team Projects'],
                recommendedWhen: 'You want React plus routing/SSR and a structured project layout.',
                avoidWhen: 'You only need a small UI without SSR or routing complexity.',
                recommendationNote: 'Often picked when SEO, SSR, or full-stack React is desired.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'Node.js',
                relatedLanguage: 'JavaScript / TypeScript',
                difficulty: 'Intermediate',
                shortDescription: 'A runtime that lets JavaScript run on the server (APIs, tools, real-time apps).',
                description: 'Node.js enables JavaScript/TypeScript on the backend. It is popular for APIs, real-time systems, and full-stack JavaScript projects, especially when paired with Express.',
                bestFor: 'Chat apps, APIs, live updates, and event-driven systems',
                advantages: [
                    'Uses one language across the stack',
                    'Strong for real-time features',
                    'Large package ecosystem',
                ],
                disadvantages: ['Async patterns can be confusing for beginners without guidance.'],
                limitations: ['Good structure (layers, validation, tests) is essential as projects grow.'],
                bestFitLabels: ['Best for Web Apps', 'Best for Changing Requirements'],
                recommendedWhen: 'You mention websockets, live dashboards, or heavy I/O concurrency.',
                avoidWhen: 'Your team is brand new to async programming and the scope is very small.',
                recommendationNote: 'Shows up when real-time or full-stack JavaScript fits the brief.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'Express.js',
                relatedLanguage: 'JavaScript / TypeScript',
                difficulty: 'Beginner to Intermediate',
                shortDescription: 'A minimal Node.js web framework for building APIs and web servers.',
                description: 'Express is a lightweight framework for routing and handling HTTP requests in Node.js. It’s common for student API projects and can be kept simple or scaled with good structure.',
                bestFor: 'REST APIs, simple backends, and server-side web apps',
                advantages: [
                    'Simple and flexible',
                    'Huge ecosystem of middleware',
                    'Easy to start small',
                ],
                disadvantages: [
                    'You must choose your own structure and libraries',
                    'Can become messy without conventions',
                ],
                limitations: ['For very large apps, opinionated frameworks may reduce decision fatigue.'],
                bestFitLabels: ['Best for Web Apps', 'Best for Student Projects'],
                recommendedWhen: 'You want a straightforward Node.js API backend.',
                avoidWhen: 'You want a highly opinionated framework with many built-in features.',
                recommendationNote: 'Often paired with React/Vue frontends for full-stack projects.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'Flutter',
                relatedLanguage: 'Dart',
                difficulty: 'Beginner to Intermediate',
                shortDescription: 'A UI toolkit for building cross-platform apps (Android/iOS/web/desktop) from one codebase.',
                description: 'Flutter uses widgets to build polished UIs quickly. It’s a popular student choice when you need Android and iOS apps without maintaining two separate codebases.',
                bestFor: 'Mobile apps and cross-platform student projects',
                advantages: [
                    'One codebase for multiple platforms',
                    'Beautiful UI development',
                    'Fast development with widgets',
                ],
                disadvantages: ['Requires learning Dart and Flutter’s widget approach.'],
                limitations: ['Some platform-specific features may require native plugins.'],
                bestFitLabels: ['Best for Mobile Apps', 'Best for Student Projects'],
                recommendedWhen: 'You need iOS and Android from one project codebase.',
                avoidWhen: 'You only target web with no mobile requirement.',
                recommendationNote: 'Common pick when mobile delivery is explicit in your requirements.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'React Native',
                relatedLanguage: 'JavaScript / TypeScript',
                difficulty: 'Intermediate',
                shortDescription: 'A framework for building mobile apps using React and JavaScript/TypeScript.',
                description: 'React Native lets you build mobile apps with React-style components. It is useful when you already know JavaScript/TypeScript and want a mobile app without fully switching to native stacks.',
                bestFor: 'Cross-platform mobile apps with a React/JS skillset',
                advantages: [
                    'Leverages React knowledge',
                    'One codebase for multiple mobile platforms',
                    'Large community and ecosystem',
                ],
                disadvantages: [
                    'Native modules may be required for some features',
                    'Debugging can be harder than pure web apps',
                ],
                limitations: ['Performance can vary depending on app complexity and native integrations.'],
                bestFitLabels: ['Best for Mobile Apps', 'Best for Student Projects'],
                recommendedWhen: 'Your team already uses React and needs a mobile app deliverable.',
                avoidWhen: 'You need a fully native iOS/Android experience with heavy device-specific features.',
                recommendationNote: 'Good bridge from web React skills to mobile delivery.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'Spring Boot',
                relatedLanguage: 'Java',
                difficulty: 'Intermediate to Advanced',
                shortDescription: 'A Java framework for building production-ready APIs and enterprise backends.',
                description: 'Spring Boot helps you create Java services with common backend needs like configuration, dependency injection, and production-ready patterns. It’s widely used for enterprise APIs.',
                bestFor: 'Enterprise applications, APIs, and large business systems',
                advantages: [
                    'Very scalable',
                    'Good for enterprise architecture',
                    'Strong ecosystem',
                ],
                disadvantages: ['More setup and concepts than lightweight stacks.'],
                limitations: ['May be too heavy if the project is small and requirements are simple.'],
                bestFitLabels: ['Best for Enterprise', 'Best for Web Apps'],
                recommendedWhen: 'Requirements read like regulated, large-team, or long-term services.',
                avoidWhen: 'You need the lightest possible stack for a weekend prototype.',
                recommendationNote: 'Often matched with enterprise-style Java scenarios.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'ASP.NET Core',
                relatedLanguage: 'C#',
                difficulty: 'Intermediate',
                shortDescription: 'A .NET framework for building fast web apps and APIs with C#.',
                description: 'ASP.NET Core is a popular choice for building backend services, REST APIs, and web applications in C#. It has strong performance and tooling support.',
                bestFor: 'Enterprise web apps, APIs, dashboards, and services',
                advantages: [
                    'Strong performance',
                    'Excellent tooling and ecosystem',
                    'Good for clean architecture and testing',
                ],
                disadvantages: [
                    'Learning curve if you are new to .NET',
                    'Can feel heavy if the project is tiny',
                ],
                limitations: ['Best when your team is comfortable with C# and .NET conventions.'],
                bestFitLabels: ['Best for Enterprise', 'Best for Web Apps'],
                recommendedWhen: 'You want a strongly structured backend with good performance and tooling.',
                avoidWhen: 'Your team only knows PHP/Python/JS and needs to deliver fast.',
                recommendationNote: 'Fits Microsoft/.NET-aligned projects and enterprise requirements.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
            ),
            $this->frameworkEntry(
                name: 'Angular',
                relatedLanguage: 'TypeScript',
                difficulty: 'Intermediate',
                shortDescription: 'A TypeScript-based framework for building large, structured web apps.',
                description: 'Angular provides a full framework experience with strong conventions, routing, forms, and dependency injection. It’s useful for enterprise-style frontends and large team projects.',
                bestFor: 'Large web applications, enterprise dashboards, and structured frontends',
                advantages: [
                    'Strong structure and conventions',
                    'Built-in routing and forms',
                    'TypeScript-first',
                ],
                disadvantages: [
                    'Steeper learning curve',
                    'More boilerplate than lighter frameworks',
                ],
                limitations: ['For small projects, Angular may be heavier than needed.'],
                bestFitLabels: ['Best for Enterprise', 'Best for Web Apps'],
                recommendedWhen: 'You need a structured frontend for a complex app with many screens.',
                avoidWhen: 'You want a very lightweight UI layer or a quick prototype.',
                recommendationNote: 'Often chosen for enterprise-like web UI requirements.',
                referenceSources: ['W3Schools', 'GeeksforGeeks'],
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
                difficulty: 'Beginner-Friendly',
                shortDescription: 'A flexible approach that delivers work in small improvements with continuous feedback.',
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
                limitations: [
                    'Works best when stakeholders can give frequent feedback',
                ],
                exampleProject: 'A student web app that changes features during development',
                bestFitLabels: ['Best for Changing Requirements', 'Best for Student Projects'],
                recommendedWhen: 'Stakeholders expect frequent changes or demos every sprint.',
                avoidWhen: 'Everything is fixed upfront with legal sign-off on a frozen spec.',
                recommendationNote: 'Useful when requirements are expected to change.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'Waterfall',
                difficulty: 'Beginner-Friendly',
                shortDescription: 'A sequential approach where each phase finishes before the next starts.',
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
                limitations: [
                    'Late testing and feedback can increase rework if requirements change',
                ],
                exampleProject: 'A small internal system with fixed requirements',
                bestFitLabels: ['Best for Fixed Requirements', 'Best for Student Projects'],
                recommendedWhen: 'Your scope, budget, and timeline are already locked.',
                avoidWhen: 'You know the idea will pivot weekly.',
                recommendationNote: 'Fits classroom projects with a frozen rubric or charter.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'Iterative',
                difficulty: 'Beginner-Friendly',
                shortDescription: 'A model that improves the system through repeated cycles and refinement.',
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
                limitations: [
                    'Requires discipline to keep each iteration focused and measurable',
                ],
                exampleProject: 'A prototype that is improved after each class review',
                bestFitLabels: ['Best for Changing Requirements', 'Best for Student Projects'],
                recommendedWhen: 'You plan multiple revisions based on mentor feedback.',
                avoidWhen: 'You must ship once with no rework cycles.',
                recommendationNote: 'Helpful for coursework with staged submissions.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'Spiral',
                difficulty: 'Intermediate',
                shortDescription: 'A risk-focused model that combines planning, prototyping, and review in cycles.',
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
                limitations: [
                    'Needs experienced planning to track risks and iterations',
                ],
                exampleProject: 'A complex AI or enterprise system with uncertain requirements',
                bestFitLabels: ['Best for Enterprise', 'Best for Changing Requirements'],
                recommendedWhen: 'Risk is high—new tech, unclear data, or strict compliance.',
                avoidWhen: 'The project is a single CRUD app with a known stack.',
                recommendationNote: 'Shows up when uncertainty or risk dominates the brief.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'RAD',
                difficulty: 'Beginner-Friendly',
                shortDescription: 'Rapid Application Development focuses on fast delivery using prototyping and reuse.',
                description: 'RAD prioritizes speed by building prototypes, gathering feedback early, and reusing components. It fits projects where getting a working version quickly is more important than perfect upfront documentation.',
                bestFor: 'Short-deadline projects, prototypes, and UI-driven apps',
                advantages: [
                    'Fast development cycle',
                    'Early user feedback',
                    'Works well with reusable components',
                ],
                disadvantages: [
                    'Less suitable for very large systems',
                    'Can produce technical debt if rushed',
                ],
                limitations: [
                    'Requires active stakeholder involvement and quick decisions',
                ],
                exampleProject: 'A student dashboard prototype built in weeks with constant feedback',
                bestFitLabels: ['Best for Student Projects', 'Best for Changing Requirements'],
                recommendedWhen: 'Time is short and you need a usable prototype quickly.',
                avoidWhen: 'You need strict documentation, compliance, or highly predictable delivery.',
                recommendationNote: 'Great when rapid prototypes and frequent feedback are expected.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'Prototype Model',
                difficulty: 'Beginner-Friendly',
                shortDescription: 'Build a quick prototype first, then improve it based on feedback.',
                description: 'The Prototype Model starts by creating an early version of the system to clarify requirements. After users review it, the team refines the design and builds the final system with better understanding.',
                bestFor: 'Projects with unclear requirements and UI/UX-heavy systems',
                advantages: [
                    'Clarifies requirements early',
                    'Improves user satisfaction',
                    'Reduces misunderstanding of features',
                ],
                disadvantages: [
                    'Prototype may be mistaken as “final” too early',
                    'Can cause scope growth without control',
                ],
                limitations: [
                    'Needs time for feedback cycles and careful scope management',
                ],
                exampleProject: 'A mobile app mockup tested with classmates before final build',
                bestFitLabels: ['Best for Changing Requirements', 'Best for Student Projects'],
                recommendedWhen: 'Users are unsure what they want until they see a working example.',
                avoidWhen: 'Requirements are already fixed and well documented from the start.',
                recommendationNote: 'Useful for UI/feature discovery before committing to a final design.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'Incremental Model',
                difficulty: 'Beginner-Friendly',
                shortDescription: 'Deliver the system in small increments, each adding more features.',
                description: 'In the Incremental Model, you build and deliver the product in parts. Each increment adds useful features, so stakeholders see progress early while the system grows over time.',
                bestFor: 'Projects that can be delivered feature-by-feature',
                advantages: [
                    'Early delivery of usable features',
                    'Easier testing per increment',
                    'Supports changing priorities',
                ],
                disadvantages: [
                    'Architecture must be planned well',
                    'Integration between increments needs care',
                ],
                limitations: [
                    'If architecture is weak, later increments become harder and slower',
                ],
                exampleProject: 'A student portal delivered as login → profiles → reports → admin tools',
                bestFitLabels: ['Best for Student Projects', 'Best for Web Apps'],
                recommendedWhen: 'You can define a “minimum viable version” and add features gradually.',
                avoidWhen: 'You must deliver everything at once with no partial releases.',
                recommendationNote: 'Works well for student projects with milestones and staged grading.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'V-Model',
                difficulty: 'Intermediate',
                shortDescription: 'A model that pairs each development phase with a matching testing phase.',
                description: 'The V-Model emphasizes verification and validation by connecting requirements and design steps directly to test planning. It is useful when testing and quality checks must be planned early.',
                bestFor: 'Projects with strict quality needs and clear requirements',
                advantages: [
                    'Testing is planned early',
                    'Clear mapping between phases and tests',
                    'Good for quality-focused work',
                ],
                disadvantages: [
                    'Less flexible when requirements change',
                    'Can be heavy on documentation',
                ],
                limitations: [
                    'Works best when requirements are stable and well understood',
                ],
                exampleProject: 'A regulated-style system where tests are defined per requirement',
                bestFitLabels: ['Best for Fixed Requirements', 'Best for Enterprise'],
                recommendedWhen: 'You need strong testing alignment and stable requirements.',
                avoidWhen: 'The project will change often or requirements are still unclear.',
                recommendationNote: 'Good for quality-focused projects with strict acceptance criteria.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'Scrum',
                difficulty: 'Beginner-Friendly',
                shortDescription: 'An Agile framework that organizes work into short sprints with regular reviews.',
                description: 'Scrum is an Agile framework that uses sprints (often 1–4 weeks), a prioritized backlog, and regular ceremonies like planning and retrospectives to deliver work in small, reviewable chunks.',
                bestFor: 'Team projects with iterative development and regular demos',
                advantages: [
                    'Clear sprint goals and timelines',
                    'Regular feedback and improvement',
                    'Good visibility of progress',
                ],
                disadvantages: [
                    'Needs disciplined ceremonies and roles',
                    'Can feel rigid if overdone',
                ],
                limitations: [
                    'Works best with a committed team and consistent sprint cadence',
                ],
                exampleProject: 'A capstone app delivered in weekly sprints with demos',
                bestFitLabels: ['Best for Student Projects', 'Best for Changing Requirements'],
                recommendedWhen: 'Your team can meet regularly and wants sprint-based planning.',
                avoidWhen: 'The team cannot commit to ceremonies or the work is mostly ad-hoc tasks.',
                recommendationNote: 'A common Agile choice for student teams with clear sprint milestones.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'Kanban',
                difficulty: 'Beginner-Friendly',
                shortDescription: 'A visual workflow method that limits work-in-progress and improves flow.',
                description: 'Kanban uses a board to visualize tasks (To Do → Doing → Done) and focuses on steady delivery. It helps teams manage work by limiting how many tasks are in progress at once.',
                bestFor: 'Ongoing maintenance, support work, and flexible task streams',
                advantages: [
                    'Simple workflow visualization',
                    'Flexible priorities',
                    'Encourages finishing work before starting more',
                ],
                disadvantages: [
                    'Less timeboxed than Scrum (deadlines may need extra planning)',
                    'Can drift without clear prioritization',
                ],
                limitations: [
                    'Needs discipline to keep WIP limits and priorities meaningful',
                ],
                exampleProject: 'A student team managing tasks weekly without strict sprint planning',
                bestFitLabels: ['Best for Student Projects', 'Best for Changing Requirements'],
                recommendedWhen: 'Your work arrives continuously or priorities change frequently.',
                avoidWhen: 'You need strict sprint-based commitments and timeboxing.',
                recommendationNote: 'Great for small teams that want flexibility and visibility.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
            $this->sdlcEntry(
                name: 'DevOps Model',
                difficulty: 'Intermediate',
                shortDescription: 'A culture and practices that unify development and operations for faster delivery.',
                description: 'DevOps focuses on automation and collaboration across development and deployment. It emphasizes CI/CD, monitoring, and reliable releases so teams can ship updates more frequently and safely.',
                bestFor: 'Projects that need frequent releases, automation, and reliable deployment',
                advantages: [
                    'Faster delivery with automation',
                    'Improved collaboration between teams',
                    'More reliable releases and monitoring',
                ],
                disadvantages: [
                    'Requires tooling and setup time',
                    'Needs team discipline and ownership',
                ],
                limitations: [
                    'Overkill for very small one-off projects without deployment needs',
                ],
                exampleProject: 'A web app deployed with CI/CD and monitoring for continuous improvements',
                bestFitLabels: ['Best for Web Apps', 'Best for Team Projects'],
                recommendedWhen: 'You plan continuous updates and want automated build/test/deploy pipelines.',
                avoidWhen: 'You only need a one-time offline deliverable with no real deployment.',
                recommendationNote: 'Best when deployment, reliability, and automation are part of the scope.',
                referenceSources: ['GeeksforGeeks', 'Software Engineering references'],
            ),
        ];
    }

    /**
     * @param  list<string>  $advantages
     * @param  list<string>  $disadvantages
     * @param  list<string>  $limitations
     * @param  list<string>  $commonFrameworks
     * @param  list<string>  $bestFitLabels
     * @return array<string, mixed>
     */
    private function languageEntry(
        string $name,
        string $difficulty,
        string $shortDescription,
        string $description,
        string $bestFor,
        array $advantages,
        array $disadvantages,
        array $limitations,
        array $commonFrameworks,
        array $bestFitLabels,
        string $recommendedWhen,
        string $avoidWhen,
        string $recommendationNote,
        array $referenceSources,
    ): array {
        return [
            'name' => $name,
            'category' => 'language',
            'difficulty' => $difficulty,
            'short_description' => $shortDescription,
            'description' => $description,
            'best_for' => $bestFor,
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'limitations' => $limitations,
            'common_frameworks' => $commonFrameworks,
            'related_language' => null,
            'example_project' => null,
            'best_fit_label' => $bestFitLabels[0] ?? null,
            'best_fit_labels' => $bestFitLabels,
            'recommended_when' => $recommendedWhen,
            'avoid_when' => $avoidWhen,
            'recommendation_note' => $recommendationNote,
            'reference_sources' => $referenceSources,
        ];
    }

    /**
     * @param  list<string>  $advantages
     * @param  list<string>  $disadvantages
     * @param  list<string>  $limitations
     * @param  list<string>  $bestFitLabels
     * @return array<string, mixed>
     */
    private function frameworkEntry(
        string $name,
        string $relatedLanguage,
        string $difficulty,
        string $shortDescription,
        string $description,
        string $bestFor,
        array $advantages,
        array $disadvantages,
        array $limitations,
        array $bestFitLabels,
        string $recommendedWhen,
        string $avoidWhen,
        string $recommendationNote,
        array $referenceSources,
    ): array {
        return [
            'name' => $name,
            'category' => 'framework',
            'difficulty' => $difficulty,
            'short_description' => $shortDescription,
            'description' => $description,
            'best_for' => $bestFor,
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'limitations' => $limitations,
            'common_frameworks' => [],
            'related_language' => $relatedLanguage,
            'example_project' => null,
            'best_fit_label' => $bestFitLabels[0] ?? null,
            'best_fit_labels' => $bestFitLabels,
            'recommended_when' => $recommendedWhen,
            'avoid_when' => $avoidWhen,
            'recommendation_note' => $recommendationNote,
            'reference_sources' => $referenceSources,
        ];
    }

    /**
     * @param  list<string>  $advantages
     * @param  list<string>  $disadvantages
     * @param  list<string>  $limitations
     * @param  list<string>  $bestFitLabels
     * @return array<string, mixed>
     */
    private function sdlcEntry(
        string $name,
        string $difficulty,
        string $shortDescription,
        string $description,
        string $bestFor,
        array $advantages,
        array $disadvantages,
        array $limitations,
        string $exampleProject,
        array $bestFitLabels,
        string $recommendedWhen,
        string $avoidWhen,
        string $recommendationNote,
        array $referenceSources,
    ): array {
        return [
            'name' => $name,
            'category' => 'sdlc_model',
            'difficulty' => $difficulty,
            'short_description' => $shortDescription,
            'description' => $description,
            'best_for' => $bestFor,
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'limitations' => $limitations,
            'common_frameworks' => [],
            'related_language' => null,
            'example_project' => $exampleProject,
            'best_fit_label' => $bestFitLabels[0] ?? null,
            'best_fit_labels' => $bestFitLabels,
            'recommended_when' => $recommendedWhen,
            'avoid_when' => $avoidWhen,
            'recommendation_note' => $recommendationNote,
            'reference_sources' => $referenceSources,
        ];
    }
}
