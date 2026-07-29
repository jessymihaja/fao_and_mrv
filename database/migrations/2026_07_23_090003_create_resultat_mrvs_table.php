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
        Schema::create('resultat_mrvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->references('id_projet')->on('projets')->onDelete('cascade')->constrained();
            $table->unsignedBigInteger('composante_id')->nullable();
            $table->unsignedBigInteger('activite_id')->nullable();
            $table->foreignId('indicateur_mrv_id')->constrained()->onDelete('cascade');
            $table->decimal('valeur_cible', 10, 2);
            $table->decimal('valeur_realise', 10, 2);
            $table->unsignedBigInteger('annee');
            $table->foreign('composante_id')->references('id')->on('composantes')->onDelete('set null');
            $table->foreign('activite_id')->references('id')->on('activites')->onDelete('set null');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultat_mrvs');
    }
};
