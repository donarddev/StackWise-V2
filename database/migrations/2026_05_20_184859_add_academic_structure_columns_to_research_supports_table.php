<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private const array JSON_COLUMNS = [
        'title_page',
        'objectives',
        'review_of_related_literature',
        'references',
        'research_discovery',
    ];

    /**
     * @var list<string>
     */
    private const array LONG_TEXT_COLUMNS = [
        'conceptual_framework',
        'technology_stack_justification',
        'risk_analysis_summary',
        'skill_gap_summary',
        'roadmap_summary',
        'conclusion',
        'recommendations',
    ];

    /**
     * @var list<string>
     */
    private const array LEGACY_TEXT_TO_JSON_COLUMNS = [
        'statement_of_problem',
        'significance_of_study',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('research_supports', function (Blueprint $table) {
            foreach (self::JSON_COLUMNS as $column) {
                if (! Schema::hasColumn('research_supports', $column)) {
                    $table->json($column)->nullable();
                }
            }

            foreach (self::LONG_TEXT_COLUMNS as $column) {
                if (! Schema::hasColumn('research_supports', $column)) {
                    $table->longText($column)->nullable();
                }
            }
        });

        $this->normalizeLegacyTextColumnsToJson();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columnsToDrop = array_merge(
            self::JSON_COLUMNS,
            self::LONG_TEXT_COLUMNS,
        );

        Schema::table('research_supports', function (Blueprint $table) use ($columnsToDrop) {
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('research_supports', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function normalizeLegacyTextColumnsToJson(): void
    {
        if (! Schema::hasTable('research_supports')) {
            return;
        }

        foreach (self::LEGACY_TEXT_TO_JSON_COLUMNS as $column) {
            if (! Schema::hasColumn('research_supports', $column)) {
                continue;
            }

            DB::table('research_supports')
                ->select(['id', $column])
                ->whereNotNull($column)
                ->orderBy('id')
                ->each(function (object $row) use ($column): void {
                    $value = $row->{$column};

                    if (! is_string($value) || $this->isJson($value)) {
                        return;
                    }

                    $trimmed = trim($value);

                    if ($trimmed === '') {
                        return;
                    }

                    DB::table('research_supports')
                        ->where('id', $row->id)
                        ->update([
                            $column => json_encode(
                                ['content' => $trimmed],
                                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
                            ),
                        ]);
                });
        }
    }

    private function isJson(string $value): bool
    {
        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }
};
