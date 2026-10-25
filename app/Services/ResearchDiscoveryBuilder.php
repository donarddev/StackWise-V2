<?php

namespace App\Services;

use App\Models\Recommendation;

class ResearchDiscoveryBuilder
{
    public const string DEFAULT_INDEXING_STATUS = 'Needs manual verification';

    public const string OPEN_ACCESS_DISCLAIMER = 'Search links only. No automatic paper downloads.';

    /**
     * @return array<string, mixed>
     */
    public function schemaFragment(): array
    {
        return [
            'keywords' => [],
            'search_query_groups' => [
                ['category' => 'Google Scholar', 'queries' => []],
                ['category' => 'IEEE Xplore', 'queries' => []],
                ['category' => 'ACM Digital Library', 'queries' => []],
                ['category' => 'Scopus', 'queries' => []],
                ['category' => 'DOI / Crossref', 'queries' => []],
            ],
            'journals' => [
                [
                    'name' => '',
                    'scope' => '',
                    'why_it_fits' => '',
                    'recommended_for' => '',
                    'indexing_status' => self::DEFAULT_INDEXING_STATUS,
                    'publication_type' => 'Journal',
                    'fit_score' => null,
                ],
            ],
            'open_access_links' => [
                [
                    'label' => 'Google Scholar',
                    'url' => '',
                    'source' => 'Google Scholar',
                    'description' => 'Broad academic search for related studies.',
                ],
            ],
            'publication_recommendation' => [
                'undergraduate_thesis_capstone' => '',
                'local_conference' => '',
                'software_engineering_journals' => '',
                'before_submission' => [],
            ],
            'scopus_verification' => [
                'intro' => '',
                'steps' => [],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function build(Recommendation $recommendation, ?string $studyTitle = null, ?string $studyContext = null): array
    {
        $searchPhrase = $this->primarySearchPhrase($recommendation, $studyTitle, $studyContext);

        return [
            'keywords' => $this->buildKeywords($recommendation),
            'search_query_groups' => $this->buildSearchQueryGroups($recommendation, $searchPhrase),
            'journals' => $this->buildJournalSuggestions($recommendation),
            'open_access_links' => $this->buildOpenAccessLinks($searchPhrase),
            'publication_recommendation' => $this->buildPublicationRecommendation($recommendation),
            'scopus_verification' => $this->buildScopusVerification(),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $discovery
     * @return array<string, mixed>
     */
    public function normalize(?array $discovery, Recommendation $recommendation, ?string $studyTitle = null, ?string $studyContext = null): array
    {
        $fallback = $this->build($recommendation, $studyTitle, $studyContext);
        $discovery ??= [];

        return [
            'keywords' => $this->mergeKeywords(
                $fallback['keywords'],
                $this->stringList($discovery['keywords'] ?? $discovery['related_keywords'] ?? null),
            ),
            'search_query_groups' => $this->normalizeSearchQueryGroups(
                $discovery,
                $fallback['search_query_groups'],
                $recommendation,
                $studyTitle,
                $studyContext,
            ),
            'journals' => $this->normalizeJournals(
                $discovery['journals'] ?? $discovery['suggested_journals'] ?? null,
                $fallback['journals'],
            ),
            'open_access_links' => $this->normalizeOpenAccessLinks(
                $discovery['open_access_links'] ?? null,
                $fallback['open_access_links'],
                $this->primarySearchPhrase($recommendation, $studyTitle, $studyContext),
            ),
            'publication_recommendation' => $this->normalizePublicationRecommendation(
                $discovery['publication_recommendation'] ?? null,
                $fallback['publication_recommendation'],
            ),
            'scopus_verification' => $this->normalizeScopusVerification(
                $discovery['scopus_verification'] ?? $discovery['scopus_note'] ?? null,
                $fallback['scopus_verification'],
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $discovery
     * @return list<string>
     */
    public function flattenSearchQueries(array $discovery): array
    {
        $queries = [];

        foreach ($discovery['search_query_groups'] ?? [] as $group) {
            if (! is_array($group)) {
                continue;
            }

            foreach ($this->stringList($group['queries'] ?? null) as $query) {
                $queries[] = $query;
            }
        }

        return array_values(array_unique($queries));
    }

    /**
     * @param  list<array<string, mixed>>  $journals
     * @return list<string>
     */
    public function formatJournalsForLegacyList(array $journals): array
    {
        $formatted = [];

        foreach ($journals as $journal) {
            if (! is_array($journal)) {
                continue;
            }

            $name = $this->text($journal['name'] ?? null);

            if ($name === null) {
                continue;
            }

            $status = $this->text($journal['indexing_status'] ?? null) ?? self::DEFAULT_INDEXING_STATUS;
            $formatted[] = $name.' (indexing_status: '.$status.')';
        }

        return $formatted;
    }

    /**
     * @param  array<string, mixed>  $discovery
     * @return list<string>
     */
    public function flattenOpenAccessUrls(array $discovery): array
    {
        $urls = [];

        foreach ($discovery['open_access_links'] ?? [] as $link) {
            if (is_string($link) || is_numeric($link)) {
                $url = trim((string) $link);

                if ($url !== '') {
                    $urls[] = $url;
                }

                continue;
            }

            if (! is_array($link)) {
                continue;
            }

            $url = $this->text($link['url'] ?? null);

            if ($url !== null) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    public function scopusNoteText(array $scopusVerification): string
    {
        $intro = $this->text($scopusVerification['intro'] ?? null) ?? '';
        $steps = $this->stringList($scopusVerification['steps'] ?? null);

        if ($steps === []) {
            return $intro;
        }

        $lines = $intro !== '' ? [$intro, ''] : [];

        foreach ($steps as $index => $step) {
            $lines[] = ($index + 1).'. '.$step;
        }

        return implode("\n", $lines);
    }

    public function publicationRecommendationText(array $publicationRecommendation): string
    {
        if (isset($publicationRecommendation['summary']) && is_string($publicationRecommendation['summary'])) {
            return trim($publicationRecommendation['summary']);
        }

        $sections = [
            'undergraduate_thesis_capstone' => 'Undergraduate thesis / capstone',
            'local_conference' => 'Local conference',
            'software_engineering_journals' => 'Software engineering / applied computing journals',
        ];

        $parts = [];

        foreach ($sections as $key => $heading) {
            $content = $this->text($publicationRecommendation[$key] ?? null);

            if ($content !== null) {
                $parts[] = $heading.': '.$content;
            }
        }

        $checklist = $this->stringList($publicationRecommendation['before_submission'] ?? null);

        if ($checklist !== []) {
            $parts[] = 'Before submission, confirm:';
            foreach ($checklist as $item) {
                $parts[] = '- '.$item;
            }
        }

        return implode("\n\n", $parts);
    }

    private function primarySearchPhrase(Recommendation $recommendation, ?string $studyTitle, ?string $studyContext): string
    {
        if ($this->hasContent($studyTitle)) {
            return trim($studyTitle);
        }

        return trim($recommendation->project_name.' '.$recommendation->recommended_framework.' '.$recommendation->project_type);
    }

    /**
     * @return list<string>
     */
    private function buildKeywords(Recommendation $recommendation): array
    {
        return array_values(array_unique(array_filter([
            $recommendation->project_type,
            $recommendation->recommended_language,
            $recommendation->recommended_framework,
            $recommendation->recommended_sdlc_model,
            'capstone',
            'software engineering',
            'web application',
        ], fn (mixed $value): bool => is_string($value) && trim($value) !== '')));
    }

    /**
     * @return list<array{category: string, queries: list<string>}>
     */
    private function buildSearchQueryGroups(Recommendation $recommendation, string $searchPhrase): array
    {
        $encoded = rawurlencode($searchPhrase);
        $framework = $recommendation->recommended_framework;
        $language = $recommendation->recommended_language;
        $sdlc = $recommendation->recommended_sdlc_model;
        $projectType = $recommendation->project_type;

        return [
            [
                'category' => 'Google Scholar',
                'queries' => [
                    '"'.$searchPhrase.'" software engineering',
                    $framework.' '.$projectType.' case study',
                ],
            ],
            [
                'category' => 'IEEE Xplore',
                'queries' => [
                    '("'.$framework.'" OR "'.$language.'") AND "'.$projectType.'"',
                    '"software engineering education" AND "'.$sdlc.'"',
                ],
            ],
            [
                'category' => 'ACM Digital Library',
                'queries' => [
                    $framework.' web application development',
                    'agile software project management education',
                ],
            ],
            [
                'category' => 'Scopus',
                'queries' => [
                    'TITLE-ABS-KEY("'.$framework.'") AND TITLE-ABS-KEY("'.$projectType.'")',
                    'TITLE-ABS-KEY("software engineering") AND TITLE-ABS-KEY("capstone")',
                ],
            ],
            [
                'category' => 'DOI / Crossref',
                'queries' => [
                    $searchPhrase,
                    $framework.' '.$sdlc.' software project',
                ],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildJournalSuggestions(Recommendation $recommendation): array
    {
        $projectType = $recommendation->project_type;
        $framework = $recommendation->recommended_framework;

        return [
            [
                'name' => 'IEEE Software',
                'scope' => 'Software engineering practice, methods, and professional development.',
                'why_it_fits' => 'Publishes applied software engineering studies suitable for system design and process evaluation narratives.',
                'recommended_for' => 'Undergraduate capstone reports with strong engineering methodology.',
                'indexing_status' => self::DEFAULT_INDEXING_STATUS,
                'publication_type' => 'Journal',
                'fit_score' => 78,
            ],
            [
                'name' => 'Journal of Systems and Software',
                'scope' => 'Software systems development, maintenance, and empirical software engineering.',
                'why_it_fits' => 'Aligns with '.$projectType.' projects that document architecture, implementation, and evaluation.',
                'recommended_for' => 'Teams with measurable evaluation results and clear system scope.',
                'indexing_status' => self::DEFAULT_INDEXING_STATUS,
                'publication_type' => 'Journal',
                'fit_score' => 74,
            ],
            [
                'name' => 'International Conference on Software Engineering Education (ICSE-SEET style venues)',
                'scope' => 'Software engineering education, capstone practices, and computing pedagogy.',
                'why_it_fits' => 'Useful when the contribution emphasizes educational process, teamwork, and '.$framework.' delivery lessons.',
                'recommended_for' => 'Local or regional computing education conferences.',
                'indexing_status' => self::DEFAULT_INDEXING_STATUS,
                'publication_type' => 'Conference',
                'fit_score' => 70,
            ],
        ];
    }

    /**
     * @return list<array{label: string, url: string, source: string, description: string}>
     */
    private function buildOpenAccessLinks(string $searchPhrase): array
    {
        $encoded = rawurlencode($searchPhrase);

        return [
            [
                'label' => 'Google Scholar Search',
                'url' => 'https://scholar.google.com/scholar?q='.$encoded,
                'source' => 'Google Scholar',
                'description' => 'Find peer-reviewed articles, theses, and technical reports related to your study.',
            ],
            [
                'label' => 'DOAJ Search',
                'url' => 'https://doaj.org/search?q='.$encoded,
                'source' => 'DOAJ',
                'description' => 'Directory of Open Access Journals — verify journal quality before citing.',
            ],
            [
                'label' => 'OpenAlex Search',
                'url' => 'https://openalex.org/works?filter=title_and_abstract.search:'.rawurlencode($searchPhrase),
                'source' => 'OpenAlex',
                'description' => 'Open catalog of scholarly works for discovery and bibliographic leads.',
            ],
            [
                'label' => 'Crossref Metadata Search',
                'url' => 'https://search.crossref.org/?q='.$encoded,
                'source' => 'Crossref',
                'description' => 'Locate DOI metadata and publisher records before citing.',
            ],
            [
                'label' => 'Semantic Scholar Search',
                'url' => 'https://www.semanticscholar.org/search?q='.$encoded,
                'source' => 'Semantic Scholar',
                'description' => 'AI-assisted discovery with citation context for related work.',
            ],
            [
                'label' => 'CORE Search',
                'url' => 'https://core.ac.uk/search?q='.$encoded,
                'source' => 'CORE',
                'description' => 'Open access research papers aggregated from repositories worldwide.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPublicationRecommendation(Recommendation $recommendation): array
    {
        $stack = $recommendation->recommended_language.' with '.$recommendation->recommended_framework;

        return [
            'undergraduate_thesis_capstone' => 'This '.$recommendation->project_type.' project is well suited for undergraduate thesis or capstone documentation when the manuscript reports requirements, '.$stack.' implementation, testing, and defended limitations.',
            'local_conference' => 'A local computing or information systems conference is appropriate if the paper emphasizes educational delivery, prototype evaluation, and lessons learned from a student software project.',
            'software_engineering_journals' => 'Software engineering or applied computing journals may be considered only after the study includes rigorous methodology, measurable results, and verified related literature — not merely a project description.',
            'before_submission' => [
                'Confirm the venue publishes software/capstone-style applied studies, not only theoretical computer science.',
                'Verify ethics approval requirements if human participants or user testing data are included.',
                'Check plagiarism policy, author guidelines, template, and page limits.',
                'Validate every citation and DOI; replace AI placeholders with verified bibliographic records.',
                'Confirm open-access fees, copyright terms, and submission deadlines.',
            ],
        ];
    }

    /**
     * @return array{intro: string, steps: list<string>}
     */
    private function buildScopusVerification(): array
    {
        return [
            'intro' => 'Do not claim Scopus indexing unless you personally verify it using authoritative sources.',
            'steps' => [
                'Search the official Scopus Sources list (Elsevier) using the journal\'s ISSN and exact title.',
                'Cross-check the ISSN on the publisher website and Ulrichsweb or equivalent serials directory.',
                'Review the publisher\'s indexing statements and compare them with Scopus, DOAJ, or Web of Science records.',
                'Avoid predatory or questionable venues: inspect editorial boards, peer-review policy, fees, and contact details.',
                'Do not rely only on the journal homepage or email invitations claiming Scopus indexing.',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $discovery
     * @param  list<array{category: string, queries: list<string>}>  $fallbackGroups
     * @return list<array{category: string, queries: list<string>}>
     */
    private function normalizeSearchQueryGroups(
        array $discovery,
        array $fallbackGroups,
        Recommendation $recommendation,
        ?string $studyTitle,
        ?string $studyContext,
    ): array {
        if (isset($discovery['search_query_groups']) && is_array($discovery['search_query_groups'])) {
            $groups = [];

            foreach ($discovery['search_query_groups'] as $group) {
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

            if ($groups !== []) {
                return $this->ensureRequiredQueryCategories($groups, $fallbackGroups);
            }
        }

        $legacyQueries = $this->stringList($discovery['search_queries'] ?? $discovery['suggested_search_queries'] ?? null);

        if ($legacyQueries !== []) {
            return [
                [
                    'category' => 'General',
                    'queries' => $legacyQueries,
                ],
                ...$fallbackGroups,
            ];
        }

        return $fallbackGroups;
    }

    /**
     * @param  list<array{category: string, queries: list<string>}>  $groups
     * @param  list<array{category: string, queries: list<string>}>  $fallbackGroups
     * @return list<array{category: string, queries: list<string>}>
     */
    private function ensureRequiredQueryCategories(array $groups, array $fallbackGroups): array
    {
        $categories = array_map(
            fn (array $group): string => strtolower($group['category']),
            $groups,
        );

        foreach ($fallbackGroups as $fallbackGroup) {
            if (! in_array(strtolower($fallbackGroup['category']), $categories, true)) {
                $groups[] = $fallbackGroup;
            }
        }

        return $groups;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function normalizeJournals(mixed $journals, array $fallbackJournals): array
    {
        if (! is_array($journals) || $journals === []) {
            return $fallbackJournals;
        }

        $resolved = [];

        foreach ($journals as $journal) {
            if (is_string($journal) || is_numeric($journal)) {
                $name = trim((string) $journal);
                $name = preg_replace('/\s*\(indexing[_ ]status:.*$/i', '', $name) ?? $name;
                $name = trim($name);

                if ($name === '') {
                    continue;
                }

                $resolved[] = $this->journalTemplate($name);

                continue;
            }

            if (! is_array($journal)) {
                continue;
            }

            $name = $this->text($journal['name'] ?? $journal['title'] ?? $journal['journal'] ?? null);

            if ($name === null) {
                continue;
            }

            $resolved[] = [
                'name' => $name,
                'scope' => $this->text($journal['scope'] ?? $journal['field'] ?? null)
                    ?? 'Verify the venue scope on the official publisher website.',
                'why_it_fits' => $this->text($journal['why_it_fits'] ?? $journal['why_it_fits_project'] ?? null)
                    ?? 'Review aims and scope to confirm alignment with your software project contribution.',
                'recommended_for' => $this->text($journal['recommended_for'] ?? $journal['recommendedFor'] ?? null)
                    ?? 'Capstone or applied software engineering submissions with clear evaluation.',
                'indexing_status' => $this->normalizeIndexingStatus($journal['indexing_status'] ?? $journal['indexingStatus'] ?? null),
                'publication_type' => $this->normalizePublicationType($journal['publication_type'] ?? $journal['publicationType'] ?? null),
                'fit_score' => $this->normalizeFitScore($journal['fit_score'] ?? $journal['fit_percentage'] ?? $journal['fitPercentage'] ?? null),
            ];
        }

        return $resolved === [] ? $fallbackJournals : $resolved;
    }

    /**
     * @return array<string, mixed>
     */
    private function journalTemplate(string $name): array
    {
        return [
            'name' => $name,
            'scope' => 'Verify the venue scope on the official publisher website.',
            'why_it_fits' => 'Review aims and scope to confirm alignment with your software project contribution.',
            'recommended_for' => 'Capstone or applied software engineering submissions with clear evaluation.',
            'indexing_status' => self::DEFAULT_INDEXING_STATUS,
            'publication_type' => 'Journal',
            'fit_score' => null,
        ];
    }

    /**
     * @param  list<array{label: string, url: string, source: string, description: string}>  $fallbackLinks
     * @return list<array{label: string, url: string, source: string, description: ?string}>
     */
    private function normalizeOpenAccessLinks(mixed $links, array $fallbackLinks, string $searchPhrase): array
    {
        $required = $this->buildOpenAccessLinks($searchPhrase);
        $resolved = [];
        $seenUrls = [];

        foreach ([...(is_array($links) ? $links : []), ...$required, ...$fallbackLinks] as $link) {
            if (is_string($link) || is_numeric($link)) {
                $url = trim((string) $link);

                if ($url === '' || isset($seenUrls[$url])) {
                    continue;
                }

                $seenUrls[$url] = true;
                $resolved[] = [
                    'label' => 'Open access search',
                    'url' => $url,
                    'source' => $this->resolveLinkSource($url),
                    'description' => 'External search portal — verify results before citing.',
                ];

                continue;
            }

            if (! is_array($link)) {
                continue;
            }

            $url = $this->text($link['url'] ?? null) ?? '';

            if ($url === '' || isset($seenUrls[$url])) {
                continue;
            }

            $seenUrls[$url] = true;
            $resolved[] = [
                'label' => $this->text($link['label'] ?? null) ?? 'Open access search',
                'url' => $url,
                'source' => $this->text($link['source'] ?? $link['source_name'] ?? null)
                    ?? $this->resolveLinkSource($url),
                'description' => $this->text($link['description'] ?? null),
            ];
        }

        return $this->prioritizeStandardLinks($resolved, $required);
    }

    /**
     * @param  list<array{label: string, url: string, source: string, description: ?string}>  $resolved
     * @param  list<array{label: string, url: string, source: string, description: string}>  $required
     * @return list<array{label: string, url: string, source: string, description: ?string}>
     */
    private function prioritizeStandardLinks(array $resolved, array $required): array
    {
        $requiredUrls = array_column($required, 'url');
        $ordered = [];

        foreach ($requiredUrls as $url) {
            foreach ($resolved as $link) {
                if ($link['url'] === $url) {
                    $ordered[] = $link;
                    break;
                }
            }
        }

        foreach ($resolved as $link) {
            if (! in_array($link['url'], $requiredUrls, true)) {
                $ordered[] = $link;
            }
        }

        return $ordered;
    }

    /**
     * @param  array<string, mixed>  $fallback
     * @return array<string, mixed>
     */
    private function normalizePublicationRecommendation(mixed $value, array $fallback): array
    {
        if (is_string($value) || is_numeric($value)) {
            $text = trim((string) $value);

            return $text === '' ? $fallback : [
                'undergraduate_thesis_capstone' => $text,
                'local_conference' => '',
                'software_engineering_journals' => '',
                'before_submission' => $fallback['before_submission'],
            ];
        }

        if (! is_array($value)) {
            return $fallback;
        }

        return [
            'undergraduate_thesis_capstone' => $this->text($value['undergraduate_thesis_capstone'] ?? null)
                ?? $fallback['undergraduate_thesis_capstone'],
            'local_conference' => $this->text($value['local_conference'] ?? null)
                ?? $fallback['local_conference'],
            'software_engineering_journals' => $this->text($value['software_engineering_journals'] ?? null)
                ?? $fallback['software_engineering_journals'],
            'before_submission' => $this->stringList($value['before_submission'] ?? null) !== []
                ? $this->stringList($value['before_submission'] ?? null)
                : $fallback['before_submission'],
        ];
    }

    /**
     * @param  array{intro: string, steps: list<string>}  $fallback
     * @return array{intro: string, steps: list<string>}
     */
    private function normalizeScopusVerification(mixed $value, array $fallback): array
    {
        if (is_string($value) || is_numeric($value)) {
            $text = trim((string) $value);

            return $text === '' ? $fallback : [
                'intro' => $text,
                'steps' => $fallback['steps'],
            ];
        }

        if (! is_array($value)) {
            return $fallback;
        }

        if (isset($value['intro']) || isset($value['steps'])) {
            return [
                'intro' => $this->text($value['intro'] ?? null) ?? $fallback['intro'],
                'steps' => $this->stringList($value['steps'] ?? null) !== []
                    ? $this->stringList($value['steps'] ?? null)
                    : $fallback['steps'],
            ];
        }

        return $fallback;
    }

    /**
     * @param  list<string>  $fallback
     * @param  list<string>  $incoming
     * @return list<string>
     */
    private function mergeKeywords(array $fallback, array $incoming): array
    {
        return array_values(array_unique([...$fallback, ...$incoming]));
    }

    private function normalizeIndexingStatus(mixed $status): string
    {
        $text = $this->text($status);

        if ($text === null) {
            return self::DEFAULT_INDEXING_STATUS;
        }

        if (stripos($text, 'scopus') !== false && stripos($text, 'verified') !== false && stripos($text, 'manual') === false) {
            return self::DEFAULT_INDEXING_STATUS;
        }

        return $text;
    }

    private function normalizePublicationType(mixed $value): string
    {
        $text = strtolower($this->text($value) ?? 'journal');

        return str_contains($text, 'conference') ? 'Conference' : 'Journal';
    }

    private function normalizeFitScore(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return max(0, min(100, (int) round((float) $value)));
    }

    private function resolveLinkSource(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return 'Web';
        }

        return ucfirst(str_replace('www.', '', $host));
    }

    private function hasContent(?string $value): bool
    {
        return $value !== null && trim($value) !== '';
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
