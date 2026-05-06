<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('project_type');
            $table->unsignedInteger('team_size');
            $table->string('complexity');
            $table->string('preferred_platform');
            $table->string('development_experience');
            $table->string('timeline');
            $table->text('project_goal');
            $table->string('recommended_language');
            $table->string('recommended_framework');
            $table->string('recommended_sdlc_model');
            $table->unsignedTinyInteger('confidence_score');
            $table->json('explanations');
            $table->json('alternative_stacks');
            $table->json('risk_analysis');
            $table->json('skill_gap_analysis');
            $table->json('roadmap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
