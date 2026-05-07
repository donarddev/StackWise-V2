<?php

namespace App\Services\Recommendation;

use Illuminate\Support\Arr;

class ScoreBoard
{
    /**
     * @var array<string, array<string, int>>
     */
    private array $scores = [
        'language' => [],
        'framework' => [],
        'sdlc' => [],
    ];

    /**
     * @var array<string, array<string, list<string>>>
     */
    private array $evidence = [
        'language' => [],
        'framework' => [],
        'sdlc' => [],
    ];

    /**
     * @param  'language'|'framework'|'sdlc'  $dimension
     */
    public function add(string $dimension, string $candidate, int $points, string $evidence): void
    {
        $candidate = trim($candidate);
        if ($candidate === '' || $points === 0) {
            return;
        }

        $this->scores[$dimension][$candidate] = ($this->scores[$dimension][$candidate] ?? 0) + $points;
        $this->evidence[$dimension][$candidate] ??= [];
        $this->evidence[$dimension][$candidate][] = $evidence;
    }

    /**
     * @param  'language'|'framework'|'sdlc'  $dimension
     * @param  array<string, int>  $candidatePoints
     */
    public function addMany(string $dimension, array $candidatePoints, string $evidence): void
    {
        foreach ($candidatePoints as $candidate => $points) {
            $this->add($dimension, (string) $candidate, (int) $points, $evidence);
        }
    }

    /**
     * @param  'language'|'framework'|'sdlc'  $dimension
     * @return array<string, int>
     */
    public function scores(string $dimension): array
    {
        $scores = $this->scores[$dimension] ?? [];
        arsort($scores);

        return $scores;
    }

    /**
     * @param  'language'|'framework'|'sdlc'  $dimension
     * @return list<string>
     */
    public function topCandidates(string $dimension, int $limit = 3): array
    {
        return array_slice(array_keys($this->scores($dimension)), 0, $limit);
    }

    /**
     * @param  'language'|'framework'|'sdlc'  $dimension
     */
    public function topScore(string $dimension): int
    {
        $scores = $this->scores($dimension);
        if ($scores === []) {
            return 0;
        }

        return (int) Arr::first($scores);
    }

    /**
     * @param  'language'|'framework'|'sdlc'  $dimension
     * @return array{candidate: string, score: int}|null
     */
    public function top(string $dimension): ?array
    {
        $scores = $this->scores($dimension);
        if ($scores === []) {
            return null;
        }

        $candidate = array_key_first($scores);

        return [
            'candidate' => (string) $candidate,
            'score' => (int) $scores[$candidate],
        ];
    }

    /**
     * @param  'language'|'framework'|'sdlc'  $dimension
     * @return list<string>
     */
    public function evidenceFor(string $dimension, string $candidate, int $limit = 5): array
    {
        $items = $this->evidence[$dimension][$candidate] ?? [];
        $items = array_values(array_unique(array_filter(array_map('trim', $items))));

        return array_slice($items, 0, $limit);
    }

    /**
     * @param  'language'|'framework'|'sdlc'  $dimension
     * @return array{top: int, runner_up: int, gap: int}
     */
    public function topGap(string $dimension): array
    {
        $scores = array_values($this->scores($dimension));
        $top = $scores[0] ?? 0;
        $runnerUp = $scores[1] ?? 0;

        return [
            'top' => (int) $top,
            'runner_up' => (int) $runnerUp,
            'gap' => (int) ($top - $runnerUp),
        ];
    }
}
