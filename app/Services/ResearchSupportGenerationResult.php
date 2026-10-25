<?php

namespace App\Services;

use App\Models\ResearchSupport;

readonly class ResearchSupportGenerationResult
{
    public function __construct(
        public ResearchSupport $researchSupport,
        public bool $usedFallback,
    ) {}
}
