<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_ideas', function (Blueprint $table) {
            $table->id();
            
            // Onglet 1 & 2 : Informations Générales & Contexte
            $table->string('titre');
            $table->string('lien')->nullable();
            $table->string('acronyme')->nullable();
            $table->text('description')->nullable();
            $table->text('contexte')->nullable();
            $table->text('justification')->nullable();
            $table->text('objectif_general')->nullable();
            $table->text('objectifs_specifiques')->nullable();
            $table->text('resultats_attendus')->nullable();
            $table->integer('duree_prevue_mois')->nullable();
            $table->date('date_debut_estimee')->nullable();
            $table->date('date_fin_estimee')->nullable();
            $table->string('porteur_projet')->nullable();

            // Onglet 3 : Localisation Géographique
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->constrained('communes')->nullOnDelete();
            $table->foreignId('fokontany_id')->nullable()->constrained('fokontany')->nullOnDelete();
            $table->text('zone_description')->nullable();
            $table->string('geo_address')->nullable();

            // Onglet 4 : Bénéficiaires
            $table->unsignedBigInteger('nombre_beneficiaires')->nullable();
            $table->unsignedBigInteger('beneficiaires_hommes')->nullable();
            $table->unsignedBigInteger('beneficiaires_femmes')->nullable();
            $table->unsignedBigInteger('beneficiaires_jeunes')->nullable();
            $table->unsignedBigInteger('beneficiaires_vulnerables')->nullable();

            // Onglet 5 : Budget & Contributions
            $table->decimal('budget_total_estime', 15, 2)->nullable();
            $table->string('devise', 10)->default('USD'); // e.g. MGA, USD, EUR
            $table->decimal('contribution_nationale', 15, 2)->nullable();
            $table->decimal('contribution_partenaires', 15, 2)->nullable();
            $table->decimal('cofinancement_prive', 15, 2)->nullable();
            $table->decimal('autres_financements', 15, 2)->nullable();

            // Workflow & Conversion
            $table->enum('statut', ['brouillon', 'soumis', 'en_etude', 'approuve', 'converti'])->default('brouillon');
            $table->unsignedBigInteger('converted_project_id')->nullable(); // ID du projet cible si converti
            $table->timestamp('converted_at')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Table pivot pour la relation N-N avec Secteurs
        Schema::create('project_idea_secteur', function (Blueprint $table) {
            $table->foreignId('project_idea_id')->constrained('project_ideas')->cascadeOnDelete();
            $table->foreignId('secteur_id')->constrained('secteurs')->cascadeOnDelete();
            $table->primary(['project_idea_id', 'secteur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_idea_secteur');
        Schema::dropIfExists('project_ideas');
    }
};