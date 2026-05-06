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
        Schema::create('technology_documents', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name');
            $table->text('description');
            $table->json('best_for');
            $table->json('advantages');
            $table->json('disadvantages');
            $table->string('difficulty_level')->nullable();
            $table->json('related_tools')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technology_documents');
    }
};
