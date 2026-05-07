<?php

namespace App\Services\Recommendation;

class RiskAnalyzer
{
    /**
     * @return list<array{risk_title: string, impact_level: 'Low'|'Medium'|'High', explanation: string, suggested_solution: string}>
     */
    public function analyze(ProjectContext $context, string $language, string $framework, string $sdlc): array
    {
        $risks = [];

        if ($context->requirementsStability() === 'changing') {
            $risks[] = [
                'risk_title' => 'Changing Requirements',
                'impact_level' => 'Medium',
                'explanation' => 'Your inputs indicate evolving requirements; scope churn can cause rework, delays, and inconsistent features.',
                'suggested_solution' => 'Use a prioritized backlog, short iterations, and requirement sign-offs per sprint milestone.',
            ];
        }

        if ($context->isHighScalability()) {
            $risks[] = [
                'risk_title' => 'Scalability & Performance Bottlenecks',
                'impact_level' => $context->performanceRequirements() === 'high' ? 'High' : 'Medium',
                'explanation' => 'High scalability needs increase the chance of bottlenecks (database queries, caching, concurrency) regardless of stack.',
                'suggested_solution' => 'Plan load testing early, design for caching/queues, and define an MVP performance budget per feature.',
            ];
        }

        if ($context->isHighSecurity()) {
            $risks[] = [
                'risk_title' => 'Security & Data Protection',
                'impact_level' => 'High',
                'explanation' => 'High security requirements raise the risk of insecure authentication, access control gaps, and data exposure if rushed.',
                'suggested_solution' => 'Adopt secure defaults (RBAC, validation), run security reviews, and log/audit critical actions.',
            ];
        }

        if ($context->budgetConstraints() === 'low') {
            $risks[] = [
                'risk_title' => 'Budget Constraints',
                'impact_level' => 'Medium',
                'explanation' => 'Low budget can limit hosting choices, paid tooling, and the time available for deep optimization.',
                'suggested_solution' => 'Prioritize core features, use managed free tiers wisely, and avoid overengineering in the first release.',
            ];
        }

        if ($context->isShortTimeline()) {
            $risks[] = [
                'risk_title' => 'Time Constraint & Delivery Risk',
                'impact_level' => 'High',
                'explanation' => 'Short timelines increase the risk of incomplete features, reduced testing, and unstable deployments.',
                'suggested_solution' => 'Ship an MVP first, timebox features, and reserve the final week for stabilization and testing.',
            ];
        }

        if ($context->isBeginnerHeavyTeam() && in_array($language, ['Java', 'Go'], true)) {
            $risks[] = [
                'risk_title' => 'Team Ramp-up / Learning Curve',
                'impact_level' => 'High',
                'explanation' => "{$language} is realistic, but can slow delivery for beginner teams due to new tooling and patterns.",
                'suggested_solution' => 'Allocate onboarding time, build a small spike/prototype, and standardize templates and conventions.',
            ];
        }

        if ($context->maintenanceExpectations() === 'high' && $context->teamSize() <= 2) {
            $risks[] = [
                'risk_title' => 'Maintenance Overhead',
                'impact_level' => 'Medium',
                'explanation' => 'High maintenance expectations with a very small team can lead to burnout and inconsistent quality.',
                'suggested_solution' => 'Automate testing/CI, enforce code reviews, and keep the architecture intentionally simple.',
            ];
        }

        if ($this->hasRealtime($context) && $language !== 'TypeScript') {
            $risks[] = [
                'risk_title' => 'Real-time Complexity',
                'impact_level' => 'Medium',
                'explanation' => 'Real-time features require careful event handling and reconnection logic; implementation can become complex quickly.',
                'suggested_solution' => 'Design real-time features as a separate module and test message flow early with realistic scenarios.',
            ];
        }

        return array_slice($this->dedupe($risks), 0, 6);
    }

    private function hasRealtime(ProjectContext $context): bool
    {
        foreach ($context->selectedFeatures() as $feature) {
            if (in_array(strtolower($feature), ['real-time', 'chat'], true)) {
                return true;
            }
        }

        return str_contains($context->analysisText(), 'real-time') || str_contains($context->analysisText(), 'chat');
    }

    /**
     * @param  list<array{risk_title: string, impact_level: 'Low'|'Medium'|'High', explanation: string, suggested_solution: string}>  $items
     * @return list<array{risk_title: string, impact_level: 'Low'|'Medium'|'High', explanation: string, suggested_solution: string}>
     */
    private function dedupe(array $items): array
    {
        $seen = [];
        $out = [];

        foreach ($items as $item) {
            $key = strtolower($item['risk_title']);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $item;
        }

        return $out;
    }
}
