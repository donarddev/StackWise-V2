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
        if (Schema::hasColumn('recommendations', 'user_id')) {
            return;
        }

        Schema::table('recommendations', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('recommendations', 'user_id')) {
            return;
        }

        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
