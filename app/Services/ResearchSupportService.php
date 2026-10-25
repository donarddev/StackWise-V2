<?php

namespace App\Services;

use App\Models\Recommendation;
use App\Models\ResearchSupport;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use JsonException;
use RuntimeException;
use Throwable;

class ResearchSupportService
{
    private const string AI_DISCLAIMER = 'This content is an AI-generated draft for academic planning only. Verify all facts, citations, and journal indexing before submission.';

    /**
     * @var list<string>
     */
    private const array EXPECTED_JSON_KEYS = [
        'title_page',
        'abstract',
        'introduction',
        'statement_of_problem',
        'objectives',
        'scope_and_limitations',
        'significance_of_study',
        'review_of_related_literature',
        'conceptual_framework',
        'methodology',
        'technology_stack_justification',
        'risk_analysis_summary',
        'skill_gap_summary',
        'roadmap_summary',
        'conclusion',
        'recommendations',
        'references',
        'research_discovery',
    ];

    public function __construct(
        private readonly OllamaService $ollamaService,
        private readonly ResearchDiscoveryBuilder $discoveryBuilder,
    ) {}

    /**
     * @param  array{study_title?: string|null, study_context?: string|null}  $validated
     */
    public function generate(User $user, Recommendation $recommendation, array $validated): ResearchSupportGenerationResult
    {
        $studyTitle = $validated['study_title'] ?? null;
        $studyContext = $validated['study_context'] ?? null;

        $prompt = $this->buildResearchPrompt($recommendation, $studyTitle, $studyContext);
        $rawAiResponse = null;
        $payload = null;
        $usedFallback = false;

        if ($this->ollamaService->isConfigured()) {
            try {
                $rawAiResponse = $this->ollamaService->chat($prompt);
                $payload = $this->parseResearchPayload($rawAiResponse, $recommendation, $studyTitle, $studyContext);
            } catch (ConnectionException|RequestException|RuntimeException|JsonException|Throwable $e) {
                report($e);
            }
        }

        if ($payload === null) {
            $usedFallback = true;
            $payload = $this->buildFallbackPayload($recommendation, $studyTitle, $studyContext);
            $rawAiResponse ??= json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $researchSupport = ResearchSupport::query()->updateOrCreate(
            ['recommendation_id' => $recommendation->id],
            [
                'user_id' => $user->id,
                'study_title' => $studyTitle,
                'study_context' => $studyContext,
                ...$this->mapPayloadToAttributes($payload),
                'raw_ai_response' => $rawAiResponse,
            ],
        );

        return new ResearchSupportGenerationResult(
            researchSupport: $researchSupport,
            usedFallback: $usedFallback,
        );
    }

    private function buildResearchPrompt(Recommendation $recommendation, ?string $studyTitle, ?string $studyContext): string
    {
        $projectContext = $this->formatProjectContext($recommendation, $studyTitle, $studyContext);
        $schema = $this->jsonSchemaTemplate($recommendation);

        return implode("\n", [
            'You are StackWise AI Research Support Assistant.',
            'Generate a formal academic research/capstone draft based only on the user\'s software project idea and recommendation report below.',
            'Use an academic tone suitable for undergraduate or graduate capstone documentation.',
            'Write complete sections with substantive depth. Avoid one-sentence sections.',
            '',
            'Project and recommendation context:',
            $projectContext,
            '',
            'Compliance rules:',
            '- Return ONLY valid JSON. No markdown fences, no commentary, no trailing text.',
            '- Base all content strictly on the project context above.',
            '- Do not invent DOI numbers or claim verified Scopus indexing.',
            '- Do not provide illegal copyrighted paper downloads.',
            '- Suggested journals must include indexing_status: "Needs manual verification" for each entry.',
            '- References must be placeholder or search guidance only unless real verified metadata is available.',
            '- RRL entries must be suggested research directions, not fabricated citations with fake authors.',
            '- For open_access_links, provide only legitimate search URLs (Google Scholar, DOAJ, OpenAlex, Crossref, Semantic Scholar, CORE).',
            '- research_discovery.search_query_groups must include categories: Google Scholar, IEEE Xplore, ACM Digital Library, Scopus, DOI / Crossref.',
            '- Each journal must include: name, scope, why_it_fits, recommended_for, indexing_status, publication_type (Journal or Conference), optional fit_score.',
            '- publication_recommendation must address undergraduate thesis/capstone fit, local conference fit, and software engineering journal fit, plus before_submission checklist.',
            '- scopus_verification must include intro and steps for ISSN checks, Scopus Sources list, publisher site, and predatory journal avoidance.',
            '',
            'Content quality requirements:',
            '- abstract: 150-250 words.',
            '- introduction: 3-5 paragraphs separated by blank lines in the string.',
            '- statement_of_problem: 1 general problem and at least 3 specific research questions in specific array.',
            '- objectives: 1 general objective and 3-5 specific objectives in specific array.',
            '- review_of_related_literature: suggested directions only, grouped into local_studies, foreign_studies, related_systems, technology_related.',
            '- methodology: explain the recommended SDLC model in formal academic style.',
            '- technology_stack_justification: justify language, framework, and SDLC for this project.',
            '- conclusion and recommendations: formal academic tone.',
            '- title_page.generated_by must be exactly: StackWise AI Research Support Assistant',
            '- title_page.generated_date: use today\'s date in YYYY-MM-DD format.',
            '',
            'Return JSON with exactly this structure and keys:',
            $schema,
        ]);
    }

    private function jsonSchemaTemplate(Recommendation $recommendation): string
    {
        $stack = $recommendation->recommended_language.' / '.$recommendation->recommended_framework;

        $template = [
            'title_page' => [
                'research_title' => '',
                'system_title' => $recommendation->project_name,
                'technology_stack' => $stack,
                'sdlc_model' => $recommendation->recommended_sdlc_model,
                'generated_by' => 'StackWise AI Research Support Assistant',
                'generated_date' => now()->toDateString(),
            ],
            'abstract' => '',
            'introduction' => '',
            'statement_of_problem' => [
                'general' => '',
                'specific' => [],
            ],
            'objectives' => [
                'general' => '',
                'specific' => [],
            ],
            'scope_and_limitations' => '',
            'significance_of_study' => [
                'students' => '',
                'researchers' => '',
                'administrators' => '',
                'future_developers' => '',
                'organization' => '',
            ],
            'review_of_related_literature' => [
                'local_studies' => [],
                'foreign_studies' => [],
                'related_systems' => [],
                'technology_related' => [],
            ],
            'conceptual_framework' => '',
            'methodology' => '',
            'technology_stack_justification' => '',
            'risk_analysis_summary' => '',
            'skill_gap_summary' => '',
            'roadmap_summary' => '',
            'conclusion' => '',
            'recommendations' => '',
            'references' => [
                'apa_placeholders' => [],
                'ieee_placeholders' => [],
                'search_guidance' => [],
            ],
            'research_discovery' => $this->discoveryBuilder->schemaFragment(),
        ];

        return json_encode($template, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function formatProjectContext(Recommendation $recommendation, ?string $studyTitle, ?string $studyContext): string
    {
        $lines = [
            'Project name: '.$recommendation->project_name,
            'Project type: '.$recommendation->project_type,
            'Project goal: '.$recommendation->project_goal,
            'Selected features: '.$this->formatList($recommendation->selected_features),
            'Team size: '.$recommendation->team_size,
            'Complexity: '.$recommendation->complexity,
            'Development experience: '.$recommendation->development_experience,
            'Timeline: '.$recommendation->timeline,
            'Recommended language: '.$recommendation->recommended_language,
            'Recommended framework: '.$recommendation->recommended_framework,
            'Recommended SDLC model: '.$recommendation->recommended_sdlc_model,
            'Confidence score: '.$recommendation->confidence_score,
        ];

        if ($this->hasArrayContent($recommendation->risk_analysis)) {
            $lines[] = 'Risk analysis from recommendation: '.$this->formatStructuredList($recommendation->risk_analysis);
        }

        if ($this->hasArrayContent($recommendation->skill_gap_analysis)) {
            $lines[] = 'Skill gap analysis from recommendation: '.$this->formatStructuredList($recommendation->skill_gap_analysis);
        }

        if ($this->hasArrayContent($recommendation->roadmap)) {
            $lines[] = 'Roadmap from recommendation: '.$this->formatStructuredList($recommendation->roadmap);
        }

        if ($this->hasContent($studyTitle)) {
            $lines[] = 'Study title (user provided): '.$studyTitle;
        }

        if ($this->hasContent($studyContext)) {
            $lines[] = 'Study context (user provided): '.$studyContext;
        }

        return implode("\n", $lines);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseResearchPayload(
        string $rawResponse,
        Recommendation $recommendation,
        ?string $studyTitle,
        ?string $studyContext,
    ): ?array {
        $decoded = json_decode($this->extractJsonString($rawResponse), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            return null;
        }

        if (! $this->hasExpectedResearchShape($decoded)) {
            return null;
        }

        return $this->normalizePayload($decoded, $recommendation, $studyTitle, $studyContext);
    }

    private function extractJsonString(string $rawResponse): string
    {
        $trimmed = trim($rawResponse);

        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/s', $trimmed, $matches) === 1) {
            return $matches[1];
        }

        $start = strpos($trimmed, '{');
        $end = strrpos($trimmed, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($trimmed, $start, $end - $start + 1);
        }

        return $trimmed;
    }

    /**
     * @param  array<string, mixed>  $decoded
     */
    private function hasExpectedResearchShape(array $decoded): bool
    {
        if ($this->isLegacyPayload($decoded)) {
            return trim((string) ($decoded['suggested_title'] ?? '')) !== ''
                || trim((string) ($decoded['abstract'] ?? '')) !== '';
        }

        foreach (self::EXPECTED_JSON_KEYS as $key) {
            if (! array_key_exists($key, $decoded)) {
                return false;
            }
        }

        $titlePage = is_array($decoded['title_page'] ?? null) ? $decoded['title_page'] : [];
        $researchTitle = trim((string) ($titlePage['research_title'] ?? ''));
        $abstract = trim((string) ($decoded['abstract'] ?? ''));

        return $abstract !== '' || $researchTitle !== '';
    }

    /**
     * @param  array<string, mixed>  $decoded
     */
    private function isLegacyPayload(array $decoded): bool
    {
        return array_key_exists('suggested_title', $decoded)
            && ! array_key_exists('title_page', $decoded);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(
        array $payload,
        Recommendation $recommendation,
        ?string $studyTitle,
        ?string $studyContext,
    ): array {
        if ($this->isLegacyPayload($payload)) {
            return $this->normalizeLegacyPayload($payload, $recommendation, $studyTitle, $studyContext);
        }

        return $this->normalizeAcademicPayload($payload, $recommendation, $studyTitle, $studyContext);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizeAcademicPayload(
        array $payload,
        Recommendation $recommendation,
        ?string $studyTitle,
        ?string $studyContext,
    ): array {
        $titlePage = $this->normalizeTitlePage($payload['title_page'] ?? null);
        $statementOfProblem = $this->normalizeStatementOfProblem($payload['statement_of_problem'] ?? null);
        $objectives = $this->normalizeObjectives($payload['objectives'] ?? null);
        $significance = $this->normalizeSignificanceOfStudy($payload['significance_of_study'] ?? null);
        $rrl = $this->normalizeReviewOfRelatedLiterature($payload['review_of_related_literature'] ?? null);
        $references = $this->normalizeReferences($payload['references'] ?? null);
        $discovery = $this->discoveryBuilder->normalize(
            is_array($payload['research_discovery'] ?? null) ? $payload['research_discovery'] : null,
            $recommendation,
            $studyTitle,
            $studyContext,
        );

        $suggestedTitle = $this->stringValue($titlePage['research_title'] ?? null);

        return [
            'title_page' => $titlePage,
            'suggested_title' => $suggestedTitle,
            'abstract' => $this->stringValue($payload['abstract'] ?? null),
            'introduction' => $this->stringValue($payload['introduction'] ?? null),
            'statement_of_problem' => $statementOfProblem,
            'objectives' => $objectives,
            'general_objective' => $this->stringValue($objectives['general'] ?? null),
            'specific_objectives' => $this->arrayValue($objectives['specific'] ?? null),
            'scope_and_limitation' => $this->stringValue($payload['scope_and_limitations'] ?? $payload['scope_and_limitation'] ?? null),
            'significance_of_study' => $significance,
            'review_of_related_literature' => $rrl,
            'conceptual_framework' => $this->stringValue($payload['conceptual_framework'] ?? null),
            'suggested_methodology' => $this->stringValue($payload['methodology'] ?? $payload['suggested_methodology'] ?? null),
            'technology_stack_justification' => $this->stringValue($payload['technology_stack_justification'] ?? null),
            'risk_analysis_summary' => $this->stringValue($payload['risk_analysis_summary'] ?? null),
            'skill_gap_summary' => $this->stringValue($payload['skill_gap_summary'] ?? null),
            'roadmap_summary' => $this->stringValue($payload['roadmap_summary'] ?? null),
            'conclusion' => $this->stringValue($payload['conclusion'] ?? null),
            'recommendations' => $this->stringValue($payload['recommendations'] ?? null),
            'references' => $references,
            'research_discovery' => $discovery,
            'related_keywords' => $this->arrayValue($discovery['keywords'] ?? null),
            'suggested_search_queries' => $this->discoveryBuilder->flattenSearchQueries($discovery),
            'suggested_journals' => $this->discoveryBuilder->formatJournalsForLegacyList($discovery['journals']),
            'open_access_links' => $this->discoveryBuilder->flattenOpenAccessUrls($discovery),
            'publication_recommendation' => $this->appendDisclaimer(
                $this->discoveryBuilder->publicationRecommendationText($discovery['publication_recommendation']),
            ),
            'scopus_note' => $this->discoveryBuilder->scopusNoteText($discovery['scopus_verification']),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizeLegacyPayload(
        array $payload,
        Recommendation $recommendation,
        ?string $studyTitle,
        ?string $studyContext,
    ): array {
        $statementOfProblem = $this->normalizeStatementOfProblem($payload['statement_of_problem'] ?? null);
        $objectives = $this->normalizeObjectives([
            'general' => $payload['general_objective'] ?? null,
            'specific' => $payload['specific_objectives'] ?? null,
        ]);
        $discovery = $this->discoveryBuilder->normalize([
            'keywords' => $payload['related_keywords'] ?? null,
            'search_queries' => $payload['suggested_search_queries'] ?? null,
            'journals' => $payload['suggested_journals'] ?? null,
            'open_access_links' => $payload['open_access_links'] ?? null,
            'publication_recommendation' => $payload['publication_recommendation'] ?? null,
            'scopus_note' => $payload['scopus_note'] ?? null,
        ], $recommendation, $studyTitle, $studyContext);

        $suggestedTitle = $this->stringValue($payload['suggested_title'] ?? null);

        return [
            'title_page' => [
                'research_title' => $suggestedTitle ?? '',
                'system_title' => '',
                'technology_stack' => '',
                'sdlc_model' => '',
                'generated_by' => 'StackWise AI Research Support Assistant',
                'generated_date' => now()->toDateString(),
            ],
            'suggested_title' => $suggestedTitle,
            'abstract' => $this->stringValue($payload['abstract'] ?? null),
            'introduction' => $this->stringValue($payload['introduction'] ?? null),
            'statement_of_problem' => $statementOfProblem,
            'objectives' => $objectives,
            'general_objective' => $this->stringValue($objectives['general'] ?? null),
            'specific_objectives' => $this->arrayValue($objectives['specific'] ?? null),
            'scope_and_limitation' => $this->stringValue($payload['scope_and_limitation'] ?? null),
            'significance_of_study' => $this->normalizeSignificanceOfStudy($payload['significance_of_study'] ?? null),
            'review_of_related_literature' => $this->normalizeReviewOfRelatedLiterature(null),
            'conceptual_framework' => null,
            'suggested_methodology' => $this->stringValue($payload['suggested_methodology'] ?? null),
            'technology_stack_justification' => null,
            'risk_analysis_summary' => null,
            'skill_gap_summary' => null,
            'roadmap_summary' => null,
            'conclusion' => null,
            'recommendations' => null,
            'references' => $this->normalizeReferences(null),
            'research_discovery' => $discovery,
            'related_keywords' => $this->arrayValue($discovery['keywords'] ?? null),
            'suggested_search_queries' => $this->discoveryBuilder->flattenSearchQueries($discovery),
            'suggested_journals' => $this->discoveryBuilder->formatJournalsForLegacyList($discovery['journals']),
            'open_access_links' => $this->discoveryBuilder->flattenOpenAccessUrls($discovery),
            'publication_recommendation' => $this->appendDisclaimer(
                $this->discoveryBuilder->publicationRecommendationText($discovery['publication_recommendation']),
            ),
            'scopus_note' => $this->discoveryBuilder->scopusNoteText($discovery['scopus_verification']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFallbackPayload(Recommendation $recommendation, ?string $studyTitle, ?string $studyContext): array
    {
        $researchTitle = $this->hasContent($studyTitle)
            ? $studyTitle
            : 'A Formal Research Study on '.$recommendation->project_name;

        $contextLine = $this->hasContent($studyContext)
            ? $studyContext
            : $recommendation->project_goal;

        $features = $this->formatList($recommendation->selected_features);
        $stack = $recommendation->recommended_language.' with '.$recommendation->recommended_framework;
        $sdlc = $recommendation->recommended_sdlc_model;
        $generatedDate = now()->toDateString();

        $payload = [
            'title_page' => [
                'research_title' => $researchTitle,
                'system_title' => $recommendation->project_name,
                'technology_stack' => $stack,
                'sdlc_model' => $sdlc,
                'generated_by' => 'StackWise AI Research Support Assistant',
                'generated_date' => $generatedDate,
            ],
            'abstract' => $this->buildFallbackAbstract($recommendation, $contextLine, $features, $stack, $sdlc),
            'introduction' => $this->buildFallbackIntroduction($recommendation, $contextLine, $features, $stack, $sdlc),
            'statement_of_problem' => [
                'general' => "Despite advances in software engineering education, student teams developing {$recommendation->project_type} projects such as {$recommendation->project_name} continue to face difficulty aligning functional scope ({$features}), delivery timelines, and an appropriate technology stack ({$stack}) with formal research documentation requirements.",
                'specific' => [
                    'What are the primary functional and non-functional requirements for implementing '.$recommendation->project_name.' using '.$stack.'?',
                    'How does the selected '.$sdlc.' model influence iteration planning, testing, and documentation for this capstone project?',
                    'What risks and skill gaps most significantly affect successful delivery of '.$recommendation->project_type.' systems in an academic setting?',
                    'Which related studies and technologies best support the design and evaluation of '.$recommendation->recommended_framework.'-based solutions?',
                ],
            ],
            'objectives' => [
                'general' => "To design, implement, and evaluate a {$recommendation->project_type} ({$recommendation->project_name}) using {$stack} guided by {$sdlc} practices and evidence-informed software engineering research.",
                'specific' => [
                    'Analyze project requirements derived from the recommendation report and selected features.',
                    'Develop core system modules using '.$recommendation->recommended_language.' and '.$recommendation->recommended_framework.'.',
                    'Apply '.$sdlc.' ceremonies and artifacts to manage scope, quality, and delivery.',
                    'Assess usability, maintainability, and identified project risks through structured evaluation.',
                    'Document findings in formal academic format suitable for capstone review.',
                ],
            ],
            'scope_and_limitations' => "This study is scoped to the recommended features, platform assumptions, and academic timeline described in the StackWise recommendation for {$recommendation->project_name}. The evaluation focuses on prototype or pilot deployment rather than enterprise-scale operations. Limitations include constrained sample size, dependence on self-reported progress data, and the requirement to manually verify external literature and journal indexing before publication.",
            'significance_of_study' => [
                'students' => 'Provides a structured capstone blueprint linking requirements, implementation, and research writing.',
                'researchers' => 'Offers a reproducible case context for studying '.$recommendation->project_type.' adoption in educational settings.',
                'administrators' => 'Supports monitoring of project risk, skill development, and milestone completion.',
                'future_developers' => 'Documents stack and SDLC rationale to guide maintenance and extension.',
                'organization' => 'Improves alignment between institutional learning outcomes and practical software delivery.',
            ],
            'review_of_related_literature' => [
                'local_studies' => [
                    'Review Philippine capstone and thesis repositories for '.$recommendation->project_type.' systems with comparable feature sets.',
                    'Examine local institutional LMS or portal studies addressing authentication and reporting modules.',
                ],
                'foreign_studies' => [
                    'Survey international literature on agile/web engineering practices for educational software projects.',
                    'Compare empirical studies on '.$recommendation->recommended_framework.' maintainability and security patterns.',
                ],
                'related_systems' => [
                    'Analyze open descriptions of student project management portals and workflow tools (feature comparison only).',
                    'Identify comparable systems cited in '.$recommendation->recommended_framework.' community case studies.',
                ],
                'technology_related' => [
                    'Investigate documentation and peer-reviewed guidance for '.$recommendation->recommended_language.', '.$recommendation->recommended_framework.', and '.$sdlc.' in small-team projects.',
                ],
            ],
            'conceptual_framework' => "The conceptual framework links user requirements, system design, {$stack} implementation, {$sdlc} process control, and evaluation outcomes. Inputs include stakeholder needs and recommendation constraints; throughputs include design artifacts, developed modules, and test evidence; outputs include a deployed prototype, documented results, and lessons learned for future iterations.",
            'methodology' => "This study adopts the {$sdlc} model as recommended by StackWise. Requirements will be elicited and prioritized, followed by iterative design and construction using {$stack}. Verification will include module testing, user-oriented evaluation, and risk review aligned with each sprint or phase gate. Documentation will capture design decisions, test results, and limitations in formal academic style.",
            'technology_stack_justification' => "{$recommendation->recommended_language} provides accessible full-stack development capabilities appropriate to the team's experience level ({$recommendation->development_experience}). {$recommendation->recommended_framework} accelerates secure web feature delivery for {$recommendation->project_type} workloads. {$sdlc} is selected to balance planning discipline with iterative feedback given project complexity ({$recommendation->complexity}) and timeline ({$recommendation->timeline}).",
            'risk_analysis_summary' => $this->buildRiskAnalysisSummary($recommendation),
            'skill_gap_summary' => $this->buildSkillGapSummary($recommendation),
            'roadmap_summary' => $this->buildRoadmapSummary($recommendation),
            'conclusion' => "In conclusion, {$recommendation->project_name} represents a feasible academic {$recommendation->project_type} initiative when requirements, {$stack}, and {$sdlc} governance are integrated with continuous risk and skill monitoring. The proposed study formalizes that integration for capstone evaluation while acknowledging that external literature and publication targets require independent verification.",
            'recommendations' => 'The researchers should finalize requirements baselines, maintain traceable test evidence, conduct periodic risk reviews, pursue verified literature through institutional databases, and confirm journal fit and indexing before any submission. Faculty mentors should align sprint or phase reviews with documented objectives and scope controls.',
            'references' => [
                'apa_placeholders' => [
                    '[Author, A. A. (Year). Title of article related to '.$recommendation->recommended_framework.'. Journal Name. Search required—do not cite without verification.]',
                ],
                'ieee_placeholders' => [
                    '[A. Author, "Title related to '.$recommendation->project_type.' systems," Journal, vol., no., pp., Year.]',
                ],
                'search_guidance' => [
                    'Use Google Scholar, IEEE Xplore, and DOAJ with queries provided in research_discovery.search_queries.',
                    'Replace placeholders only after retrieving and verifying complete bibliographic metadata.',
                ],
            ],
            'research_discovery' => $this->discoveryBuilder->build($recommendation, $studyTitle, $studyContext),
        ];

        return $this->normalizeAcademicPayload($payload, $recommendation, $studyTitle, $studyContext);
    }

    private function buildFallbackAbstract(
        Recommendation $recommendation,
        string $contextLine,
        string $features,
        string $stack,
        string $sdlc,
    ): string {
        return implode(' ', [
            'This academic draft proposes a formal capstone study for '.$recommendation->project_name.', a '.$recommendation->project_type.' designed to address '.$contextLine,
            'The investigation aligns functional scope—including '.$features.'—with a recommended technology stack of '.$stack.' and a '.$sdlc.' delivery model identified through the StackWise recommendation report.',
            'The study defines problem statements, objectives, scope, related literature directions, methodology, and evaluation criteria appropriate for undergraduate or graduate software engineering research.',
            'It further examines how requirements traceability, iterative delivery, stakeholder communication, and quality assurance practices influence the credibility of capstone findings in applied computing programs.',
            'Risk, skill, and roadmap insights from the recommendation are synthesized to support realistic planning, mitigation, and faculty review of project feasibility.',
            'The work contributes a structured documentation template connecting requirements engineering, implementation, testing, and research writing without substituting for verified external citations or fabricated bibliographic metadata.',
            'Intended outcomes include a working prototype, traceable process artifacts, defendable evaluation evidence, and conclusions regarding maintainability, security readiness, and academic preparedness.',
            'Researchers must independently validate literature, metrics, venue fit, and publication targets prior to defense or journal submission, and must replace all placeholder references with verified sources.',
            self::AI_DISCLAIMER,
        ]);
    }

    private function buildFallbackIntroduction(
        Recommendation $recommendation,
        string $contextLine,
        string $features,
        string $stack,
        string $sdlc,
    ): string {
        return implode("\n\n", [
            'Software-intensive capstone projects require students to translate stakeholder needs into dependable systems while producing scholarly documentation. In educational contexts, misalignment between feature ambition, technical skill, and process discipline frequently leads to schedule overruns and weak research narratives.',
            'The project '.$recommendation->project_name.' ('.$recommendation->project_type.') targets '.$contextLine.' Teams must therefore balance functional priorities such as '.$features.' with quality attributes including security, maintainability, and usability.',
            'StackWise recommendation outputs provide an evidence-oriented starting point by suggesting '.$stack.' and the '.$sdlc.' model based on team profile, complexity ('.$recommendation->complexity.'), and timeline ('.$recommendation->timeline.').',
            'Formal research documentation complements implementation by clarifying the problem domain, objectives, methodological rigor, and contribution claims expected in capstone review.',
            'This introduction establishes the context for a structured draft that integrates recommendation insights with academic sections including review directions, conceptual framing, methodology, and publication discovery guidance subject to manual verification.',
        ]);
    }

    private function buildRiskAnalysisSummary(Recommendation $recommendation): string
    {
        if ($this->hasArrayContent($recommendation->risk_analysis)) {
            return 'The following risks were identified in the StackWise recommendation and should be monitored throughout the study:'."\n"
                .$this->formatStructuredList($recommendation->risk_analysis);
        }

        return 'Risk analysis should be maintained throughout the project, focusing on scope volatility, integration complexity, security assumptions, and testing adequacy. Update the risk register each sprint or phase gate.';
    }

    private function buildSkillGapSummary(Recommendation $recommendation): string
    {
        if ($this->hasArrayContent($recommendation->skill_gap_analysis)) {
            return 'Skill gap insights from the recommendation:'."\n"
                .$this->formatStructuredList($recommendation->skill_gap_analysis);
        }

        return 'Given development experience level "'.$recommendation->development_experience.'", the team should plan targeted upskilling for '.$recommendation->recommended_framework.', version control, testing, and deployment practices before critical implementation milestones.';
    }

    private function buildRoadmapSummary(Recommendation $recommendation): string
    {
        if ($this->hasArrayContent($recommendation->roadmap)) {
            return 'Recommended roadmap phases from StackWise:'."\n"
                .$this->formatStructuredList($recommendation->roadmap);
        }

        return 'A phased roadmap should include requirements baseline, architecture design, iterative implementation, integrated testing, pilot deployment, and final documentation aligned with academic deadlines.';
    }

    /**
     * @return array<string, string>
     */
    private function normalizeTitlePage(mixed $value): array
    {
        if (! is_array($value)) {
            return [
                'research_title' => '',
                'system_title' => '',
                'technology_stack' => '',
                'sdlc_model' => '',
                'generated_by' => 'StackWise AI Research Support Assistant',
                'generated_date' => now()->toDateString(),
            ];
        }

        return [
            'research_title' => $this->stringValue($value['research_title'] ?? null) ?? '',
            'system_title' => $this->stringValue($value['system_title'] ?? null) ?? '',
            'technology_stack' => $this->stringValue($value['technology_stack'] ?? null) ?? '',
            'sdlc_model' => $this->stringValue($value['sdlc_model'] ?? null) ?? '',
            'generated_by' => $this->stringValue($value['generated_by'] ?? null) ?? 'StackWise AI Research Support Assistant',
            'generated_date' => $this->stringValue($value['generated_date'] ?? null) ?? now()->toDateString(),
        ];
    }

    /**
     * @return array{general: string, specific: list<string>}
     */
    private function normalizeStatementOfProblem(mixed $value): array
    {
        if (is_array($value)) {
            if (isset($value['content']) && is_string($value['content'])) {
                return [
                    'general' => trim($value['content']),
                    'specific' => [],
                ];
            }

            return [
                'general' => $this->stringValue($value['general'] ?? null) ?? '',
                'specific' => $this->arrayValue($value['specific'] ?? null),
            ];
        }

        $text = $this->stringValue($value);

        return [
            'general' => $text ?? '',
            'specific' => [],
        ];
    }

    /**
     * @return array{general: string, specific: list<string>}
     */
    private function normalizeObjectives(mixed $value): array
    {
        if (! is_array($value)) {
            $text = $this->stringValue($value);

            return [
                'general' => $text ?? '',
                'specific' => [],
            ];
        }

        return [
            'general' => $this->stringValue($value['general'] ?? null) ?? '',
            'specific' => $this->arrayValue($value['specific'] ?? null),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function normalizeSignificanceOfStudy(mixed $value): array
    {
        if (! is_array($value)) {
            $text = $this->stringValue($value) ?? '';

            return [
                'students' => $text,
                'researchers' => '',
                'administrators' => '',
                'future_developers' => '',
                'organization' => '',
            ];
        }

        return [
            'students' => $this->stringValue($value['students'] ?? null) ?? '',
            'researchers' => $this->stringValue($value['researchers'] ?? null) ?? '',
            'administrators' => $this->stringValue($value['administrators'] ?? null) ?? '',
            'future_developers' => $this->stringValue($value['future_developers'] ?? null) ?? '',
            'organization' => $this->stringValue($value['organization'] ?? null) ?? '',
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    private function normalizeReviewOfRelatedLiterature(mixed $value): array
    {
        $empty = [
            'local_studies' => [],
            'foreign_studies' => [],
            'related_systems' => [],
            'technology_related' => [],
        ];

        if (! is_array($value)) {
            return $empty;
        }

        return [
            'local_studies' => $this->arrayValue($value['local_studies'] ?? null),
            'foreign_studies' => $this->arrayValue($value['foreign_studies'] ?? null),
            'related_systems' => $this->arrayValue($value['related_systems'] ?? null),
            'technology_related' => $this->arrayValue($value['technology_related'] ?? null),
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    private function normalizeReferences(mixed $value): array
    {
        $empty = [
            'apa_placeholders' => [],
            'ieee_placeholders' => [],
            'search_guidance' => [],
        ];

        if (! is_array($value)) {
            return $empty;
        }

        return [
            'apa_placeholders' => $this->arrayValue($value['apa_placeholders'] ?? null),
            'ieee_placeholders' => $this->arrayValue($value['ieee_placeholders'] ?? null),
            'search_guidance' => $this->arrayValue($value['search_guidance'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function mapPayloadToAttributes(array $payload): array
    {
        return [
            'title_page' => $payload['title_page'],
            'suggested_title' => $payload['suggested_title'],
            'abstract' => $payload['abstract'],
            'introduction' => $payload['introduction'],
            'statement_of_problem' => $payload['statement_of_problem'],
            'objectives' => $payload['objectives'],
            'general_objective' => $payload['general_objective'],
            'specific_objectives' => $payload['specific_objectives'],
            'scope_and_limitation' => $payload['scope_and_limitation'],
            'significance_of_study' => $payload['significance_of_study'],
            'review_of_related_literature' => $payload['review_of_related_literature'],
            'conceptual_framework' => $payload['conceptual_framework'],
            'suggested_methodology' => $payload['suggested_methodology'],
            'technology_stack_justification' => $payload['technology_stack_justification'],
            'risk_analysis_summary' => $payload['risk_analysis_summary'],
            'skill_gap_summary' => $payload['skill_gap_summary'],
            'roadmap_summary' => $payload['roadmap_summary'],
            'conclusion' => $payload['conclusion'],
            'recommendations' => $payload['recommendations'],
            'references' => $payload['references'],
            'research_discovery' => $payload['research_discovery'],
            'related_keywords' => $payload['related_keywords'],
            'suggested_search_queries' => $payload['suggested_search_queries'],
            'suggested_journals' => $payload['suggested_journals'],
            'open_access_links' => $payload['open_access_links'],
            'publication_recommendation' => $payload['publication_recommendation'],
            'scopus_note' => $payload['scopus_note'],
        ];
    }

    private function appendDisclaimer(?string $value): ?string
    {
        $text = trim((string) $value);

        if ($text === '') {
            return self::AI_DISCLAIMER;
        }

        if (str_contains($text, 'AI-generated draft')) {
            return $text;
        }

        return $text.' '.self::AI_DISCLAIMER;
    }

    private function stringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value) || is_numeric($value)) {
            $string = trim((string) $value);

            return $string === '' ? null : $string;
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function arrayValue(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $items = [];

        foreach ($value as $item) {
            if (is_string($item) || is_numeric($item)) {
                $string = trim((string) $item);

                if ($string !== '') {
                    $items[] = $string;
                }
            }
        }

        return $items;
    }

    /**
     * @param  list<mixed>|null  $items
     */
    private function formatList(?array $items): string
    {
        if ($items === null || $items === []) {
            return 'Not specified';
        }

        $formatted = [];

        foreach ($items as $item) {
            if (is_string($item) && trim($item) !== '') {
                $formatted[] = trim($item);
            }
        }

        return $formatted === [] ? 'Not specified' : implode(', ', $formatted);
    }

    /**
     * @param  list<array<string, mixed>>|null  $items
     */
    private function formatStructuredList(?array $items): string
    {
        if ($items === null || $items === []) {
            return 'Not available';
        }

        $lines = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $parts = [];

            foreach ($item as $key => $value) {
                if (is_string($value) && trim($value) !== '') {
                    $parts[] = ucfirst(str_replace('_', ' ', (string) $key)).': '.$value;
                } elseif (is_array($value) && $value !== []) {
                    $parts[] = ucfirst(str_replace('_', ' ', (string) $key)).': '.$this->formatList($value);
                }
            }

            if ($parts !== []) {
                $lines[] = '- '.implode('; ', $parts);
            }
        }

        return $lines === [] ? 'Not available' : implode("\n", $lines);
    }

    private function hasContent(?string $value): bool
    {
        return $value !== null && trim($value) !== '';
    }

    /**
     * @param  list<mixed>|null  $value
     */
    private function hasArrayContent(?array $value): bool
    {
        return $value !== null && $value !== [];
    }
}
