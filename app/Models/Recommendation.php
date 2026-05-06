<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recommendation extends Model
{
    protected $fillable = [
        'project_name',
        'project_type',
        'team_size',
        'complexity',
        'preferred_platform',
        'development_experience',
        'timeline',
        'project_goal',
        'recommended_language',
        'recommended_framework',
        'recommended_sdlc_model',
        'confidence_score',
        'explanations',
        'alternative_stacks',
        'risk_analysis',
        'skill_gap_analysis',
        'roadmap',
    ];

    protected $casts = [
        'team_size' => 'integer',
        'confidence_score' => 'integer',
        'explanations' => 'array',
        'alternative_stacks' => 'array',
        'risk_analysis' => 'array',
        'skill_gap_analysis' => 'array',
        'roadmap' => 'array',
    ];

    protected $appends = [
        'generated_at',
    ];

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function getGeneratedAtAttribute(): ?string
    {
        return $this->created_at?->format('M d, Y');
    }
}
