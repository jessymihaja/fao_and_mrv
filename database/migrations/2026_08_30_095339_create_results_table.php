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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            
            // Exactement comme le TypeScript : project_id
            $table->foreignId('project_id')->references('id_projet')->on('projets')->onDelete('cascade')->constrained();
            $table->foreignId('composante_id')->nullable()->constrained('composantes')->onDelete('set null');
            $table->foreignId('activite_id')->nullable()->constrained('activites')->onDelete('set null');
            $table->foreignId('indicateur_id')->nullable()->constrained('resultat_mrvs')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('result_type_id')->constrained('result_types')->onDelete('cascade');
            
            $table->string('titre');
            $table->text('description')->nullable();
            $table->integer('reference_year');
            $table->integer('target_year');
            $table->enum('statut', ['prevu', 'en_cours', 'atteint', 'partiellement_atteint', 'non_atteint'])->default('prevu');
            $table->decimal('valeur_reference', 15, 2)->nullable();
            $table->text('source_verification')->nullable();
            $table->text('methode_collecte')->nullable();
            $table->text('observations')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
