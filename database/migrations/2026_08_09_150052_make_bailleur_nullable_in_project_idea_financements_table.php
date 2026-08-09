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
        Schema::table('project_idea_financements', function (Blueprint $table) {
            // Rend la colonne bailleur optionnelle (NULL)
            $table->string('bailleur')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('project_idea_financements', function (Blueprint $table) {
            $table->string('bailleur')->nullable(false)->change();
        });
    }
};
