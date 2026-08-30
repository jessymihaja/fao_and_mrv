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
        Schema::table('financement_contributions', function (Blueprint $table) {
            $table->dropColumn('montant_mga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financement_contributions', function (Blueprint $table) {
            $table->decimal('montant_mga', 20, 2)->after('devise')->nullable();
        });
    }
};