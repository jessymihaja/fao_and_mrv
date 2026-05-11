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
        Schema::create('projets', function (Blueprint $table) {
            $table->id('id_projet');
            $table->string('titre');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('classification_id');    
            $table->unsignedBigInteger('entite_accreditee_id');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('domaine_intervention_id');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('latitude',10,8);
            $table->decimal('longitude',10,8);
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('fokontany_id')->nullable()->constrained('fokontany')->nullOnDelete();
            $table->string('zone_description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('created_at')->useCurrent();

            // Clés étrangères
            $table->foreign('classification_id')->references('id_classification')->on('classifications')->onDelete('set null');
            $table->foreign('status_id')->references('id_status')->on('statuses')->onDelete('set null');
            $table->foreign('entite_accreditee_id')->references('id_entite_accreditee')->on('entite_accreditees')->onDelete('set null');
            $table->foreign('domaine_intervention_id')->references('id_domaine_intervention')->on('domaine_interventions')->onDelete('set null');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
