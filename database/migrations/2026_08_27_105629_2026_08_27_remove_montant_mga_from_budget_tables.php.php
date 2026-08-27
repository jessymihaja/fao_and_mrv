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
        // 1. Table financements
        if (Schema::hasColumn('financements', 'montant_mga')) {
            Schema::table('financements', function (Blueprint $table) {
                $table->dropColumn('montant_mga');
            });
        }

        // 2. Budget Pledges
        if (Schema::hasColumn('budget_pledges', 'montant_mga')) {
            Schema::table('budget_pledges', function (Blueprint $table) {
                $table->dropColumn('montant_mga');
            });
        }

        // 3. Budget Mobilisations
        if (Schema::hasColumn('budget_mobilisations', 'montant_mga')) {
            Schema::table('budget_mobilisations', function (Blueprint $table) {
                $table->dropColumn('montant_mga');
            });
        }

        // 4. Budget Engagements
        if (Schema::hasColumn('budget_engagements', 'montant_mga')) {
            Schema::table('budget_engagements', function (Blueprint $table) {
                $table->dropColumn('montant_mga');
            });
        }

        // 5. Budget Approbations
        if (Schema::hasColumn('budget_approbations', 'montant_mga')) {
            Schema::table('budget_approbations', function (Blueprint $table) {
                $table->dropColumn('montant_mga');
            });
        }

        // 6. Budget Programmations
        if (Schema::hasColumn('budget_programmations', 'montant_mga')) {
            Schema::table('budget_programmations', function (Blueprint $table) {
                $table->dropColumn('montant_mga');
            });
        }

        // 7. Budget Décaissements
        if (Schema::hasColumn('budget_decaissements', 'montant_mga')) {
            Schema::table('budget_decaissements', function (Blueprint $table) {
                $table->dropColumn('montant_mga');
            });
        }

        // 8. Budget Dépenses
        if (Schema::hasColumn('budget_depenses', 'montant_mga')) {
            Schema::table('budget_depenses', function (Blueprint $table) {
                $table->dropColumn('montant_mga');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'financements',
            'budget_pledges',
            'budget_mobilisations',
            'budget_engagements',
            'budget_approbations',
            'budget_programmations',
            'budget_decaissements',
            'budget_depenses',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'montant_mga')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('montant_mga', 15, 2)->nullable()->after('devise');
                });
            }
        }
    }
};