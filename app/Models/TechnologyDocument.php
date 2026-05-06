<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnologyDocument extends Model
{
    protected $fillable = [
        'category',
        'name',
        'description',
        'best_for',
        'advantages',
        'disadvantages',
        'difficulty_level',
        'related_tools',
    ];

    protected $casts = [
        'best_for' => 'array',
        'advantages' => 'array',
        'disadvantages' => 'array',
        'related_tools' => 'array',
    ];
}
