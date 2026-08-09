<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_idea_financements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_idea_id')->constrained('project_ideas')->cascadeOnDelete();
            $table->foreignId('organisme_contributeur_id')->nullable()->constrained('organisme_contributeurs')->nullOnDelete();
            $table->string('bailleur');
            $table->string('bailleur_autre')->nullable();
            $table->decimal('montant_demande', 15, 2)->nullable();
            $table->string('devise', 10)->default('USD');
            $table->enum('type_financement', ['don', 'pret', 'cofinancement', 'assistance_technique']);
            $table->enum('statut', ['en_preparation', 'soumis', 'en_negociation'])->default('en_preparation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_idea_financements');
    }
};