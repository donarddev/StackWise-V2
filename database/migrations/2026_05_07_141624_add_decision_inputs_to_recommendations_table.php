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
        Schema::table('recommendations', function (Blueprint $table) {
            $table->json('selected_features')->nullable()->after('project_type');
            $table->string('scalability_needs')->nullable()->after('project_goal');
            $table->string('security_requirements')->nullable()->after('scalability_needs');
            $table->string('performance_requirements')->nullable()->after('security_requirements');
            $table->string('budget_constraints')->nullable()->after('performance_requirements');
            $table->string('maintenance_expectations')->nullable()->after('budget_constraints');
            $table->string('deployment_preference')->nullable()->after('maintenance_expectations');
            $table->string('requirements_stability')->nullable()->after('deployment_preference');
            $table->string('stakeholder_involvement')->nullable()->after('requirements_stability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropColumn([
                'selected_features',
                'scalability_needs',
                'security_requirements',
                'performance_requirements',
                'budget_constraints',
                'maintenance_expectations',
                'deployment_preference',
                'requirements_stability',
                'stakeholder_involvement',
            ]);
        });
    }
};
