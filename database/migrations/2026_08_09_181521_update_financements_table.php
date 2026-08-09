<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financements', function (Blueprint $table) {
            $table->renameColumn('projet_id', 'project_id'); // Alignement avec le Frontend
            $table->string('type_financement')->nullable()->after('project_id');
            $table->string('mode_contribution')->nullable()->after('type_financement');
            $table->text('description')->nullable()->after('date_approbation');
            $table->unsignedBigInteger('categorie_contribution_id')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('financements', function (Blueprint $table) {
            $table->renameColumn('project_id', 'projet_id');
            $table->dropColumn([
                'type_financement',
                'mode_contribution',
                'description',
                'categorie_contribution_id',
            ]);
        });
    }
};