<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchSupport extends Model
{
    protected $fillable = [
        'user_id',
        'recommendation_id',
        'study_title',
        'study_context',
        'suggested_title',
        'abstract',
        'introduction',
        'title_page',
        'statement_of_problem',
        'general_objective',
        'objectives',
        'specific_objectives',
        'scope_and_limitation',
        'significance_of_study',
        'conceptual_framework',
        'review_of_related_literature',
        'suggested_methodology',
        'technology_stack_justification',
        'risk_analysis_summary',
        'skill_gap_summary',
        'roadmap_summary',
        'conclusion',
        'recommendations',
        'references',
        'related_keywords',
        'suggested_search_queries',
        'suggested_journals',
        'open_access_links',
        'publication_recommendation',
        'research_discovery',
        'scopus_note',
        'raw_ai_response',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'recommendation_id' => 'integer',
        'title_page' => 'array',
        'statement_of_problem' => 'array',
        'objectives' => 'array',
        'significance_of_study' => 'array',
        'review_of_related_literature' => 'array',
        'references' => 'array',
        'research_discovery' => 'array',
        'specific_objectives' => 'array',
        'related_keywords' => 'array',
        'suggested_search_queries' => 'array',
        'suggested_journals' => 'array',
        'open_access_links' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }
}
