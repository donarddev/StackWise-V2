<?php

namespace App\Services;

class ChatbotService
{
    private const string SESSION_KEY = 'stackwise_assistant_conversation';

    private const int MAX_CONVERSATION_MESSAGES = 10;

    /**
     * @return list<array{role: string, content: string}>
     */
    public function getConversation(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function appendExchange(string $userMessage, string $assistantReply): void
    {
        /** @var list<array{role: string, content: string}> $conversation */
        $conversation = session()->get(self::SESSION_KEY, []);
        $conversation[] = ['role' => 'user', 'content' => $userMessage];
        $conversation[] = ['role' => 'assistant', 'content' => $assistantReply];

        if (count($conversation) > self::MAX_CONVERSATION_MESSAGES) {
            $conversation = array_slice($conversation, -self::MAX_CONVERSATION_MESSAGES);
        }

        session()->put(self::SESSION_KEY, $conversation);
    }

    public function clearConversation(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function generateResponse(string $message): string
    {
        $normalized = $this->normalize($message);

        if ($normalized === '') {
            return $this->fallbackResponse();
        }

        if ($this->matchesAgileWaterfallComparison($normalized)) {
            return $this->agileVersusWaterfallResponse();
        }

        if ($this->matchesChangingRequirements($normalized)) {
            return $this->changingRequirementsSdlcResponse();
        }

        if ($this->matchesStudentWebStack($normalized)) {
            return $this->studentWebStackResponse();
        }

        if (str_contains($normalized, 'stackwise')) {
            if (str_contains($normalized, 'fastapi')) {
                return $this->stackwiseFastApiResponse();
            }

            return $this->stackwiseOverviewResponse();
        }

        if (str_contains($normalized, 'spiral')) {
            return $this->spiralResponse();
        }

        if (str_contains($normalized, 'iterative')) {
            return $this->iterativeResponse();
        }

        if (str_contains($normalized, 'sdlc')) {
            return $this->sdlcOverviewResponse();
        }

        if (str_contains($normalized, 'waterfall')) {
            return $this->waterfallResponse();
        }

        if (str_contains($normalized, 'agile')) {
            return $this->agileResponse();
        }

        if (str_contains($normalized, 'fastapi')) {
            return $this->fastApiResponse();
        }

        if (str_contains($normalized, 'laravel')) {
            return $this->laravelResponse();
        }

        if (str_contains($normalized, 'react')) {
            return $this->reactResponse();
        }

        if (str_contains($normalized, 'dart') || str_contains($normalized, 'flutter')) {
            return $this->dartFlutterResponse();
        }

        if ($this->matchesJavaScript($normalized)) {
            return $this->javascriptResponse();
        }

        if ($this->matchesJava($normalized)) {
            return $this->javaResponse();
        }

        if (str_contains($normalized, 'python')) {
            return $this->pythonResponse();
        }

        if ($this->matchesPhp($normalized)) {
            return $this->phpResponse();
        }

        if ($this->matchesMobile($normalized)) {
            return $this->mobileResponse();
        }

        if ($this->matchesWebApplication($normalized)) {
            return $this->webApplicationResponse();
        }

        if ($this->matchesAiProject($normalized)) {
            return $this->aiProjectResponse();
        }

        if (str_contains($normalized, 'recommend')) {
            return $this->recommendationFlowResponse();
        }

        if (str_contains($normalized, 'framework')) {
            return $this->genericFrameworkResponse();
        }

        if ($this->matchesProgrammingLanguage($normalized)) {
            return $this->genericProgrammingLanguageResponse();
        }

        return $this->fallbackResponse();
    }

    public function getAssistantGreeting(): string
    {
        return "Hi, I'm the StackWise Assistant. Ask me about Python, Laravel, FastAPI, Agile, Waterfall, or what stack may fit your project.";
    }

    /**
     * @return list<string>
     */
    public function getSuggestedQuestions(): array
    {
        return [
            'Why is Python recommended for AI projects?',
            'When should I use Laravel?',
            'What is the difference between Agile and Waterfall?',
            'Why does StackWise AI recommend FastAPI?',
            'What stack is good for a student web system?',
            'What SDLC model is best for changing requirements?',
        ];
    }

    /**
     * @return list<array{label: string, question: string}>
     */
    public function getTopicChips(): array
    {
        return [
            ['label' => 'Python', 'question' => 'What should I know about Python for my project?'],
            ['label' => 'PHP', 'question' => 'What should I know about PHP for web development?'],
            ['label' => 'Laravel', 'question' => 'What should I know about Laravel?'],
            ['label' => 'FastAPI', 'question' => 'What should I know about FastAPI?'],
            ['label' => 'JavaScript', 'question' => 'What should I know about JavaScript?'],
            ['label' => 'React', 'question' => 'What should I know about React?'],
            ['label' => 'Agile', 'question' => 'Why is Agile useful?'],
            ['label' => 'Waterfall', 'question' => 'When is Waterfall a good choice?'],
            ['label' => 'Mobile App', 'question' => 'What should I consider for a mobile app project?'],
            ['label' => 'Web App', 'question' => 'What should I know about building a web application?'],
            ['label' => 'AI Project', 'question' => 'What should I know about starting an AI project?'],
        ];
    }

    /**
     * @return list<string>
     */
    public function getAssistantCapabilities(): array
    {
        return [
            'Explain programming languages',
            'Compare frameworks',
            'Explain SDLC models',
            'Clarify recommendation results',
            'Suggest beginner-friendly project direction',
            'Prepare users before generating a recommendation',
        ];
    }

    private function normalize(string $message): string
    {
        return strtolower(trim($message));
    }

    private function matchesAgileWaterfallComparison(string $normalized): bool
    {
        if (str_contains($normalized, 'difference') && str_contains($normalized, 'agile') && str_contains($normalized, 'waterfall')) {
            return true;
        }

        return str_contains($normalized, 'agile') && str_contains($normalized, 'waterfall');
    }

    private function matchesChangingRequirements(string $normalized): bool
    {
        return (str_contains($normalized, 'changing') && str_contains($normalized, 'requirement'))
            || str_contains($normalized, 'change often')
            || str_contains($normalized, 'requirements change');
    }

    private function matchesStudentWebStack(string $normalized): bool
    {
        return (str_contains($normalized, 'student') && str_contains($normalized, 'web'))
            || str_contains($normalized, 'student web system')
            || str_contains($normalized, 'good stack')
            || (str_contains($normalized, 'stack') && str_contains($normalized, 'student'));
    }

    private function matchesJavaScript(string $normalized): bool
    {
        return str_contains($normalized, 'javascript')
            || str_contains($normalized, 'typescript')
            || preg_match('/\bjs\b/', $normalized) === 1;
    }

    private function matchesJava(string $normalized): bool
    {
        if ($this->matchesJavaScript($normalized)) {
            return false;
        }

        return preg_match('/\bjava\b/', $normalized) === 1;
    }

    private function matchesPhp(string $normalized): bool
    {
        return str_contains($normalized, 'php');
    }

    private function matchesMobile(string $normalized): bool
    {
        return str_contains($normalized, 'mobile')
            || str_contains($normalized, 'android')
            || str_contains($normalized, 'ios')
            || str_contains($normalized, 'flutter');
    }

    private function matchesWebApplication(string $normalized): bool
    {
        return str_contains($normalized, 'web app')
            || str_contains($normalized, 'web application')
            || str_contains($normalized, 'website')
            || str_contains($normalized, 'crud');
    }

    private function matchesAiProject(string $normalized): bool
    {
        return str_contains($normalized, 'ai project')
            || str_contains($normalized, 'machine learning')
            || str_contains($normalized, 'data science')
            || (bool) preg_match('/\bai\b/', $normalized);
    }

    private function matchesProgrammingLanguage(string $normalized): bool
    {
        return str_contains($normalized, 'programming language')
            || str_contains($normalized, 'coding language');
    }

    private function agileVersusWaterfallResponse(): string
    {
        return 'Agile works in short iterations with frequent feedback, so it is recommended when requirements may change or you want to ship small improvements often. Waterfall moves through clear phases one after another, so it is recommended when requirements are stable and documented up front. A simple warning: Agile needs good communication so scope does not balloon, while Waterfall is harder to change later if you discover new needs.';
    }

    private function changingRequirementsSdlcResponse(): string
    {
        return 'For changing requirements, Agile is usually the best fit because it expects feedback and reprioritization each iteration. Iterative and Spiral can also work when you refine the product over multiple cycles or when risk is high. Waterfall is less ideal when things change often, because design and scope are mostly fixed early.';
    }

    private function studentWebStackResponse(): string
    {
        return 'A student web system often fits Laravel with PHP or JavaScript with React on the front end—Laravel gives you auth, routing, validation, and databases quickly, while React helps if you need a rich interactive UI with a separate API (for example FastAPI or Laravel as the backend). StackWise AI weighs your features, timeline, and experience, but a classic beginner-friendly path is Laravel for full-stack web or React plus a small API if you want to split front and back ends.';
    }

    private function stackwiseFastApiResponse(): string
    {
        return 'StackWise AI may recommend FastAPI when your project is API-first, needs fast Python endpoints, or connects to AI, models, or data services. It is great for clear automatic docs and async-friendly services. A limitation is that it is not a full UI framework—you still need a front end or a separate client for complete web pages.';
    }

    private function stackwiseOverviewResponse(): string
    {
        return 'StackWise AI is a decision support tool for students: you describe your project, and it suggests a programming language, framework, and SDLC model that fit your requirements, timeline, and complexity. It is meant to explain the “why” behind a stack, not replace your course requirements. Later, deeper AI (such as Ollama) can plug into the same service layer for richer answers.';
    }

    private function spiralResponse(): string
    {
        return 'Spiral blends planning, prototyping, and risk analysis in repeating cycles. It is recommended for complex or high-risk projects where you need early prototypes and careful risk control. For small class assignments with a clear scope, Spiral can feel heavy and paperwork-heavy compared with Agile or Iterative.';
    }

    private function iterativeResponse(): string
    {
        return 'Iterative development improves the product through repeated cycles, each time refining features based on what you learned. It is recommended when you expect several versions or milestones, which is common in student prototypes. Watch out: without a clear goal per iteration, the product vision can drift, so keep short written goals for each cycle.';
    }

    private function sdlcOverviewResponse(): string
    {
        return 'SDLC (Software Development Life Cycle) is the structured path from planning and design through building, testing, deployment, and maintenance. Models like Waterfall, Agile, Iterative, and Spiral are different ways to organize those steps. Choosing an SDLC is about how much change you expect and how formally you need to plan before coding.';
    }

    private function waterfallResponse(): string
    {
        return 'Waterfall finishes each phase (requirements, design, build, test) before moving on. It is recommended when requirements are fixed and stakeholders want a clear upfront plan—common in small internal tools with a signed-off spec. The main limitation is late feedback: changes after design are expensive, so it is weaker when your idea will evolve a lot.';
    }

    private function agileResponse(): string
    {
        return 'Agile delivers work in small increments with regular review. It is recommended when requirements may change, you want steady demos, or you work closely with a mentor or client. You should still guard scope: Agile is flexible, but without priorities, the project can grow faster than your semester allows.';
    }

    private function fastApiResponse(): string
    {
        return 'FastAPI is a modern Python framework for building high-performance APIs with automatic OpenAPI docs. It is recommended for AI backends, data services, and microservices where Python ecosystem matters. It is less focused on server-rendered pages than Django, so you usually pair it with a separate front end for full web apps.';
    }

    private function laravelResponse(): string
    {
        return 'Laravel is best for web applications, CRUD systems, dashboards, and student projects that need authentication, routing, validation, and database features through a clear MVC structure. It is beginner-friendly for PHP developers and speeds up common web tasks. It is not the best choice for native mobile apps on its own—use APIs plus a mobile stack if you need app-store clients.';
    }

    private function reactResponse(): string
    {
        return 'React helps you build interactive web UIs with reusable components. It is recommended for single-page apps, dashboards, and rich front ends that talk to an API. You still need routing, state, and build tooling around it, and it does not replace a backend—pair it with Laravel, FastAPI, or Node for data and auth.';
    }

    private function dartFlutterResponse(): string
    {
        return 'Dart (often with Flutter) is strong for mobile and cross-platform UIs from one codebase. It is recommended when you need iOS and Android with a polished interface. The tradeoff is a smaller ecosystem than JavaScript for general web, and you will invest time learning widgets and Dart basics.';
    }

    private function javascriptResponse(): string
    {
        return 'JavaScript powers interactive browsers and, with runtimes like Node, can run on servers too. It is recommended for web front ends, real-time features, and full-stack JS projects. Without TypeScript or strict patterns, large projects can get harder to maintain, so plan structure early for coursework-sized apps that might grow.';
    }

    private function javaResponse(): string
    {
        return 'Java is widely used for enterprise backends, Android apps, and large maintainable systems. It is recommended when you need strong typing, mature tooling, or Android native paths. It can feel verbose for tiny prototypes, so for very small class scripts, Python or PHP might be faster to start.';
    }

    private function pythonResponse(): string
    {
        return 'Python is recommended for AI, data science, automation, teaching, and quick API prototypes because it reads clearly and has rich libraries. StackWise AI often highlights Python when your brief mentions models, data, or experimentation. A limitation is that raw CPU performance can lag compiled languages, and mobile GUI work is usually not Python’s main strength.';
    }

    private function phpResponse(): string
    {
        return 'PHP remains a practical choice for server-rendered web apps, CMS-style systems, and Laravel projects. It is recommended when hosting and tutorials target classic web stacks. It is less common for native mobile or GPU-heavy AI training—those usually lean on other languages with stronger ecosystems for those jobs.';
    }

    private function mobileResponse(): string
    {
        return 'Mobile applications often use Flutter/Dart, Kotlin/Java on Android, Swift on iOS, or React Native/JavaScript for cross-platform work. StackWise AI considers whether you need one shared codebase or native performance. A heads-up: mobile adds app store and device-testing overhead compared with a simple web assignment.';
    }

    private function webApplicationResponse(): string
    {
        return 'A web application usually combines a browser UI, a server or API, and a database. Laravel or Django fits traditional multi-page or API-backed apps, while React plus an API suits highly interactive pages. StackWise AI matches your feature list—auth, roles, real-time, reports—to a stack that is realistic for students.';
    }

    private function aiProjectResponse(): string
    {
        return 'An AI project often needs Python for models and notebooks, plus an API layer (such as FastAPI) to serve predictions. StackWise AI may suggest that pairing when you describe training, inference, or data pipelines. Remember: data quality, evaluation metrics, and compute matter as much as the language—plan how you will demo results in class.';
    }

    private function recommendationFlowResponse(): string
    {
        return 'Recommendations in StackWise AI come from your project description: goals, users, timeline, and constraints. The assistant can clarify terms (what Agile means, why an API framework fits) so the suggested stack feels understandable. For the official pick, use Start Recommendation on the recommendation page—this chatbot preview stays rule-based until deeper AI is wired in.';
    }

    private function genericFrameworkResponse(): string
    {
        return 'A framework gives you structure—routing, templates, ORM, auth helpers—so you do not start from zero. Laravel suits classic web apps, FastAPI suits Python APIs, and React suits rich front ends. Tell me which domain you mean (web, mobile, AI API), and I can narrow it down; StackWise AI will still combine your full brief into one recommendation.';
    }

    private function genericProgrammingLanguageResponse(): string
    {
        return 'A programming language is the syntax and runtime you use to implement your system. Picking one depends on where the app runs (browser, server, mobile), libraries you need, and what your team already knows. StackWise AI compares languages like Python, PHP, JavaScript, Java, and Dart against your requirements so the choice is not random.';
    }

    private function fallbackResponse(): string
    {
        return 'I’m not sure yet, but you can ask me about programming languages, frameworks, SDLC models, or StackWise AI recommendations.';
    }
}
