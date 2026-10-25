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
        Schema::create('research_supports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recommendation_id')->constrained()->cascadeOnDelete();
            $table->string('study_title')->nullable();
            $table->longText('study_context')->nullable();
            $table->string('suggested_title')->nullable();
            $table->longText('abstract')->nullable();
            $table->longText('introduction')->nullable();
            $table->longText('statement_of_problem')->nullable();
            $table->longText('general_objective')->nullable();
            $table->json('specific_objectives')->nullable();
            $table->longText('scope_and_limitation')->nullable();
            $table->longText('significance_of_study')->nullable();
            $table->longText('suggested_methodology')->nullable();
            $table->json('related_keywords')->nullable();
            $table->json('suggested_search_queries')->nullable();
            $table->json('suggested_journals')->nullable();
            $table->json('open_access_links')->nullable();
            $table->longText('publication_recommendation')->nullable();
            $table->longText('scopus_note')->nullable();
            $table->longText('raw_ai_response')->nullable();
            $table->timestamps();

            $table->unique('recommendation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_supports');
    }
};
