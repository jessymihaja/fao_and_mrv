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
        Schema::create('project_perspectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projets', 'id_projet')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('perspective_types')->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('zone_extension_envisagee')->nullable();
            $table->text('objectif_moyen_terme')->nullable();
            $table->text('objectif_long_terme')->nullable();
            $table->text('impact_futur_attendu')->nullable();
            $table->enum('statut', [
                'a_l_etude', 
                'planifie', 
                'en_cours', 
                'realise'
            ])->default('a_l_etude');
            $table->foreignId('created_by')->nullable()->constrained('utilisateurs', 'id_utilisateur')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_perspectives');
    }
};
