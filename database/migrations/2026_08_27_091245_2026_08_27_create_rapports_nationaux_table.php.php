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
        Schema::create('rapports_nationaux', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->integer('annee')->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('secteur_climatique')->nullable();
            $table->string('accredited_entity')->nullable();
            $table->string('source_financement')->nullable();
            $table->string('statut_projet')->nullable();
            $table->enum('statut', ['brouillon', 'genere', 'publie'])->default('brouillon');
            $table->json('contenu')->nullable();
            $table->foreignId('created_by')->references('id_utilisateur')->on('utilisateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports_nationaux');
    }
};
