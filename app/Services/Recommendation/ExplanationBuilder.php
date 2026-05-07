<?php

namespace App\Services\Recommendation;

class ExplanationBuilder
{
    /**
     * @return array{language_reason: string, framework_reason: string, sdlc_reason: string}
     */
    public function buildReasons(
        ProjectContext $context,
        ScoreBoard $board,
        string $language,
        string $framework,
        string $sdlc,
    ): array {
        $languageEvidence = $board->evidenceFor('language', $language, 4);
        $frameworkEvidence = $board->evidenceFor('framework', $framework, 4);
        $sdlcEvidence = $board->evidenceFor('sdlc', $sdlc, 4);
        $requirementsStability = $context->requirementsStability() !== '' ? $context->requirementsStability() : 'not specified';

        $languageReason = $this->formatReason(
            lead: "{$language} fits your {$context->projectType()} given your platform target ({$context->preferredPlatform()}), experience ({$context->developmentExperience()}), and constraints.",
            evidence: $languageEvidence,
            context: $context,
        );

        $frameworkReason = $this->formatReason(
            lead: "{$framework} was selected because it matches {$language} and supports your requested features while keeping delivery realistic for a {$context->timeline()} timeline.",
            evidence: $frameworkEvidence,
            context: $context,
        );

        $sdlcReason = $this->formatReason(
            lead: "{$sdlc} fits your team size ({$context->teamSize()}), complexity ({$context->complexity()}), and requirement stability ({$requirementsStability}).",
            evidence: $sdlcEvidence,
            context: $context,
        );

        return [
            'language_reason' => $languageReason,
            'framework_reason' => $frameworkReason,
            'sdlc_reason' => $sdlcReason,
        ];
    }

    /**
     * @param  list<string>  $evidence
     */
    private function formatReason(string $lead, array $evidence, ProjectContext $context): string
    {
        $details = [];

        if ($context->scalabilityNeeds() !== '') {
            $details[] = "Scalability: {$context->scalabilityNeeds()}.";
        }
        if ($context->securityRequirements() !== '') {
            $details[] = "Security: {$context->securityRequirements()}.";
        }
        if ($context->performanceRequirements() !== '') {
            $details[] = "Performance: {$context->performanceRequirements()}.";
        }

        $evidenceText = $evidence !== [] ? implode(' ', $evidence) : null;
        $detailText = $details !== [] ? implode(' ', $details) : null;

        return trim(implode(' ', array_filter([$lead, $evidenceText, $detailText])));
    }
}
