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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();

            // Relations principales
            $table->foreignId('project_id')->constrained('projets', 'id_projet')->onDelete('cascade');
            $table->foreignId('beneficiary_type_id')->constrained('beneficiary_types')->onDelete('cascade');
            $table->foreignId('beneficiary_category_id')->constrained('beneficiary_categories')->onDelete('cascade');

            // Zone géographique (Optionnelles)
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->constrained('communes')->nullOnDelete();
            $table->foreignId('fokontany_id')->nullable()->constrained('fokontany')->nullOnDelete();

            $table->text('description')->nullable();

            // Métriques numériques
            $table->integer('planned_count')->default(0);
            $table->integer('achieved_count')->default(0);
            $table->integer('women_count')->nullable();
            $table->integer('men_count')->nullable();
            $table->integer('youth_count')->nullable();
            $table->integer('vulnerable_count')->nullable();

            // Années
            $table->integer('reference_year');
            $table->integer('monitoring_year')->nullable();

            // Informations complémentaires
            $table->string('source')->nullable();
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
