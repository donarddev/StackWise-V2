<?php

namespace App\Services;

use App\Models\Recommendation;
use App\Models\ResearchSupport;

class ResearchSupportViewDataService
{
    public function __construct(
        private readonly ResearchDiscoveryBuilder $discoveryBuilder,
    ) {}

    /**
     * @return array{paper: array<string, mixed>, discovery: array<string, mixed>}|null
     */
    public function forDisplay(?ResearchSupport $researchSupport, ?Recommendation $recommendation = null): ?array
    {
        if ($researchSupport === null) {
            return null;
        }

        $recommendation ??= $researchSupport->recommendation;

        if ($recommendation === null) {
            return null;
        }

        return [
            'paper' => $this->buildPaperData($researchSupport),
            'discovery' => $this->buildDiscoveryData($researchSupport, $recommendation),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPaperData(ResearchSupport $researchSupport): array
    {
        $titlePage = $this->resolveTitlePage($researchSupport);
        $statementOfProblem = $this->resolveStatementOfProblem($researchSupport);
        $objectives = $this->resolveObjectives($researchSupport);

        return [
            'workspaceTitle' => $titlePage['research_title'] ?: ($researchSupport->suggested_title ?? 'Research Paper Draft'),
            'workspaceSubtitle' => 'Formal capstone research draft generated from your StackWise recommendation.',
            'titlePage' => $titlePage,
            'abstract' => $this->text($researchSupport->abstract),
            'introduction' => $this->text($researchSupport->introduction),
            'statementOfProblem' => $statementOfProblem,
            'objectives' => $objectives,
            'scopeAndLimitations' => $this->text($researchSupport->scope_and_limitation),
            'significanceEntries' => $this->resolveSignificanceEntries($researchSupport),
            'rrlGroups' => $this->resolveRrlGroups($researchSupport),
            'conceptualFramework' => $this->text($researchSupport->conceptual_framework),
            'methodology' => $this->text($researchSupport->suggested_methodology),
            'technologyStackJustification' => $this->text($researchSupport->technology_stack_justification),
            'riskAnalysisSummary' => $this->text($researchSupport->risk_analysis_summary),
            'skillGapSummary' => $this->text($researchSupport->skill_gap_summary),
            'roadmapSummary' => $this->text($researchSupport->roadmap_summary),
            'conclusion' => $this->text($researchSupport->conclusion),
            'recommendations' => $this->text($researchSupport->recommendations),
            'referenceGroups' => $this->resolveReferenceGroups($researchSupport),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDiscoveryData(ResearchSupport $researchSupport, Recommendation $recommendation): array
    {
        $discovery = $this->discoveryBuilder->normalize(
            $this->resolveStoredDiscovery($researchSupport),
            $recommendation,
            $researchSupport->study_title,
            $researchSupport->study_context,
        );

        return [
            'keywords' => $this->stringList($discovery['keywords'] ?? null),
            'searchQueryGroups' => $this->resolveSearchQueryGroups($discovery),
            'journals' => $this->resolveJournalsForDisplay($discovery['journals'] ?? []),
            'openAccessLinks' => $this->resolveOpenAccessLinksForDisplay($discovery['open_access_links'] ?? []),
            'publicationRecommendation' => $this->resolvePublicationRecommendationForDisplay(
                $discovery['publication_recommendation'] ?? [],
            ),
            'scopusVerification' => $this->resolveScopusVerificationForDisplay(
                $discovery['scopus_verification'] ?? [],
            ),
            'openAccessDisclaimer' => ResearchDiscoveryBuilder::OPEN_ACCESS_DISCLAIMER,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveStoredDiscovery(ResearchSupport $researchSupport): ?array
    {
        $stored = is_array($researchSupport->research_discovery)
            ? $researchSupport->research_discovery
            : [];

        if ($stored !== []) {
            return $stored;
        }

        $legacy = array_filter([
            'keywords' => $researchSupport->related_keywords,
            'search_queries' => $researchSupport->suggested_search_queries,
            'journals' => $researchSupport->suggested_journals,
            'open_access_links' => $researchSupport->open_access_links,
            'publication_recommendation' => $researchSupport->publication_recommendation,
            'scopus_note' => $researchSupport->scopus_note,
        ], fn (mixed $value): bool => $value !== null && $value !== []);

        return $legacy === [] ? null : $legacy;
    }

    /**
     * @param  array<string, mixed>  $discovery
     * @return list<array{category: string, queries: list<string>}>
     */
    private function resolveSearchQueryGroups(array $discovery): array
    {
        $groups = [];

        foreach ($discovery['search_query_groups'] ?? [] as $group) {
            if (! is_array($group)) {
                continue;
            }

            $category = $this->text($group['category'] ?? null);
            $queries = $this->stringList($group['queries'] ?? null);

            if ($category !== null && $queries !== []) {
                $groups[] = [
                    'category' => $category,
                    'queries' => $queries,
                ];
            }
        }

        return $groups;
    }

    /**
     * @param  list<array<string, mixed>>  $journals
     * @return list<array<string, mixed>>
     */
    private function resolveJournalsForDisplay(array $journals): array
    {
        $resolved = [];

        foreach ($journals as $journal) {
            if (! is_array($journal)) {
                continue;
            }

            $name = $this->text($journal['name'] ?? null);

            if ($name === null) {
                continue;
            }

            $resolved[] = [
                'name' => $name,
                'scope' => $this->text($journal['scope'] ?? null),
                'whyItFits' => $this->text($journal['why_it_fits'] ?? null),
                'recommendedFor' => $this->text($journal['recommended_for'] ?? null),
                'indexingStatus' => $this->text($journal['indexing_status'] ?? null)
                    ?? ResearchDiscoveryBuilder::DEFAULT_INDEXING_STATUS,
                'publicationType' => $this->text($journal['publication_type'] ?? null) ?? 'Journal',
                'fitScore' => is_numeric($journal['fit_score'] ?? null)
                    ? max(0, min(100, (int) round((float) $journal['fit_score'])))
                    : null,
            ];
        }

        return $resolved;
    }

    /**
     * @param  list<mixed>  $links
     * @return list<array{label: string, url: string, source: ?string, description: ?string}>
     */
    private function resolveOpenAccessLinksForDisplay(array $links): array
    {
        $resolved = [];

        foreach ($links as $link) {
            if (is_string($link) || is_numeric($link)) {
                $url = trim((string) $link);

                if ($url === '') {
                    continue;
                }

                $resolved[] = [
                    'label' => 'Open access search',
                    'url' => $url,
                    'source' => 'Web',
                    'description' => null,
                ];

                continue;
            }

            if (! is_array($link)) {
                continue;
            }

            $url = $this->text($link['url'] ?? null) ?? '';

            if ($url === '') {
                continue;
            }

            $resolved[] = [
                'label' => $this->text($link['label'] ?? null) ?? 'Open access search',
                'url' => $url,
                'source' => $this->text($link['source'] ?? $link['source_name'] ?? null),
                'description' => $this->text($link['description'] ?? null),
            ];
        }

        return $resolved;
    }

    /**
     * @param  array<string, mixed>  $publicationRecommendation
     * @return list<array{title: string, body: ?string, items: list<string>}>
     */
    private function resolvePublicationRecommendationForDisplay(array $publicationRecommendation): array
    {
        $sections = [
            ['title' => 'Undergraduate thesis / capstone', 'key' => 'undergraduate_thesis_capstone'],
            ['title' => 'Local conference', 'key' => 'local_conference'],
            ['title' => 'Software engineering / applied computing journals', 'key' => 'software_engineering_journals'],
        ];

        $resolved = [];

        foreach ($sections as $section) {
            $body = $this->text($publicationRecommendation[$section['key']] ?? null);

            if ($body !== null) {
                $resolved[] = [
                    'title' => $section['title'],
                    'body' => $body,
                    'items' => [],
                ];
            }
        }

        $checklist = $this->stringList($publicationRecommendation['before_submission'] ?? null);

        if ($checklist !== []) {
            $resolved[] = [
                'title' => 'Before submission',
                'body' => null,
                'items' => $checklist,
            ];
        }

        return $resolved;
    }

    /**
     * @param  array<string, mixed>  $scopusVerification
     * @return array{intro: string, steps: list<string>}
     */
    private function resolveScopusVerificationForDisplay(array $scopusVerification): array
    {
        return [
            'intro' => $this->text($scopusVerification['intro'] ?? null) ?? '',
            'steps' => $this->stringList($scopusVerification['steps'] ?? null),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function resolveTitlePage(ResearchSupport $researchSupport): array
    {
        $stored = is_array($researchSupport->title_page) ? $researchSupport->title_page : [];

        return [
            'research_title' => $this->text($stored['research_title'] ?? null)
                ?? $this->text($researchSupport->suggested_title)
                ?? '',
            'system_title' => $this->text($stored['system_title'] ?? null) ?? '',
            'technology_stack' => $this->text($stored['technology_stack'] ?? null) ?? '',
            'sdlc_model' => $this->text($stored['sdlc_model'] ?? null) ?? '',
            'generated_by' => $this->text($stored['generated_by'] ?? null)
                ?? 'StackWise AI Research Support Assistant',
            'generated_date' => $this->text($stored['generated_date'] ?? null) ?? '',
        ];
    }

    /**
     * @return array{general: string, specific: list<string>}
     */
    private function resolveStatementOfProblem(ResearchSupport $researchSupport): array
    {
        $value = $researchSupport->statement_of_problem;

        if (is_array($value)) {
            if (isset($value['content']) && is_string($value['content'])) {
                return [
                    'general' => trim($value['content']),
                    'specific' => [],
                ];
            }

            return [
                'general' => $this->text($value['general'] ?? null) ?? '',
                'specific' => $this->stringList($value['specific'] ?? null),
            ];
        }

        $text = $this->text($value);

        return [
            'general' => $text ?? '',
            'specific' => [],
        ];
    }

    /**
     * @return array{general: string, specific: list<string>}
     */
    private function resolveObjectives(ResearchSupport $researchSupport): array
    {
        $stored = is_array($researchSupport->objectives) ? $researchSupport->objectives : [];

        if ($stored !== []) {
            return [
                'general' => $this->text($stored['general'] ?? null) ?? '',
                'specific' => $this->stringList($stored['specific'] ?? null),
            ];
        }

        return [
            'general' => $this->text($researchSupport->general_objective) ?? '',
            'specific' => $this->stringList($researchSupport->specific_objectives),
        ];
    }

    /**
     * @return list<array{term: string, description: string}>
     */
    private function resolveSignificanceEntries(ResearchSupport $researchSupport): array
    {
        $value = $researchSupport->significance_of_study;

        if (! is_array($value)) {
            $text = $this->text($value);

            return $text === null ? [] : [
                ['term' => 'Significance', 'description' => $text],
            ];
        }

        if (isset($value['content']) && is_string($value['content'])) {
            return [
                ['term' => 'Significance', 'description' => trim($value['content'])],
            ];
        }

        $labels = [
            'students' => 'Students',
            'researchers' => 'Researchers',
            'administrators' => 'Administrators',
            'future_developers' => 'Future Developers',
            'organization' => 'Organization',
        ];

        $entries = [];

        foreach ($labels as $key => $label) {
            $description = $this->text($value[$key] ?? null);

            if ($description !== null) {
                $entries[] = [
                    'term' => $label,
                    'description' => $description,
                ];
            }
        }

        return $entries;
    }

    /**
     * @return list<array{label: string, items: list<string>}>
     */
    private function resolveRrlGroups(ResearchSupport $researchSupport): array
    {
        $rrl = is_array($researchSupport->review_of_related_literature)
            ? $researchSupport->review_of_related_literature
            : [];

        $groups = [
            ['label' => 'Local Studies', 'key' => 'local_studies'],
            ['label' => 'Foreign Studies', 'key' => 'foreign_studies'],
            ['label' => 'Related Systems', 'key' => 'related_systems'],
            ['label' => 'Technology Related', 'key' => 'technology_related'],
        ];

        $resolved = [];

        foreach ($groups as $group) {
            $items = $this->stringList($rrl[$group['key']] ?? null);

            if ($items !== []) {
                $resolved[] = [
                    'label' => $group['label'],
                    'items' => $items,
                ];
            }
        }

        return $resolved;
    }

    /**
     * @return list<array{label: string, items: list<string>}>
     */
    private function resolveReferenceGroups(ResearchSupport $researchSupport): array
    {
        $references = is_array($researchSupport->references) ? $researchSupport->references : [];

        $groups = [
            ['label' => 'APA Placeholders', 'key' => 'apa_placeholders'],
            ['label' => 'IEEE Placeholders', 'key' => 'ieee_placeholders'],
            ['label' => 'Search Guidance', 'key' => 'search_guidance'],
        ];

        $resolved = [];

        foreach ($groups as $group) {
            $items = $this->stringList($references[$group['key']] ?? null);

            if ($items !== []) {
                $resolved[] = [
                    'label' => $group['label'],
                    'items' => $items,
                ];
            }
        }

        return $resolved;
    }

    private function text(mixed $value): ?string
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
    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $items = [];

        foreach ($value as $item) {
            $text = $this->text($item);

            if ($text !== null) {
                $items[] = $text;
            }
        }

        return $items;
    }
}
