<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Annoncé (Pledges)
        Schema::create('budget_pledges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->onDelete('cascade');
            $table->foreignId('composante_id')->nullable()->constrained('composantes')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->foreignId('bailleur_id')->nullable()->constrained('organisme_contributeurs')->nullOnDelete();
            $table->date('date_annonce');
            $table->decimal('montant', 15, 2);
            $table->string('devise', 10)->default('MGA');
            $table->decimal('montant_mga', 15, 2);
            $table->text('description')->nullable();
            $table->string('source')->nullable();
            $table->string('justificatif_path')->nullable();
            $table->string('justificatif_name')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Mobilisé (Mobilisations / FinancementContributions)
        Schema::create('budget_mobilisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->onDelete('cascade');
            $table->foreignId('composante_id')->nullable()->constrained('composantes')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->foreignId('organisme_contributeur_id')->constrained('organisme_contributeurs');
            $table->string('mode_contribution'); 
            $table->enum('type_mobilisation', ['public', 'prive', 'cofinancement', 'effet_levier'])->nullable();
            $table->decimal('montant', 15, 2);
            $table->string('devise', 10)->default('MGA');
            $table->decimal('montant_mga', 15, 2);
            $table->date('date_contribution');
            $table->foreignId('categorie_contribution_id')->nullable()->constrained('contribution_categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('commentaire')->nullable();
            $table->string('justificatif_path')->nullable();
            $table->string('justificatif_name')->nullable();
            $table->timestamps();
        });

        // 3. Engagé (Engagements)
        Schema::create('budget_engagements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->onDelete('cascade');
            $table->foreignId('composante_id')->nullable()->constrained('composantes')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->foreignId('bailleur_id')->nullable()->constrained('organisme_contributeurs')->nullOnDelete();
            $table->date('date');
            $table->decimal('montant', 15, 2);
            $table->string('devise', 10)->default('MGA');
            $table->decimal('montant_mga', 15, 2);
            $table->string('reference_accord')->nullable();
            $table->text('description')->nullable();
            $table->string('justificatif_path')->nullable();
            $table->string('justificatif_name')->nullable();
            $table->timestamps();
        });

        // 4. Approuvé (Approbations)
        Schema::create('budget_approbations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->onDelete('cascade');
            $table->foreignId('composante_id')->nullable()->constrained('composantes')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->foreignId('organisme_id')->nullable()->constrained('organisme_contributeurs')->nullOnDelete();
            $table->date('date_approbation');
            $table->decimal('montant_approuve', 15, 2);
            $table->string('devise', 10)->default('MGA');
            $table->decimal('montant_mga', 15, 2);
            $table->string('reference')->nullable();
            $table->text('decision')->nullable();
            $table->string('justificatif_path')->nullable();
            $table->string('justificatif_name')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Programmé (Plans de décaissement)
        Schema::create('budget_programmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->onDelete('cascade');
            $table->foreignId('composante_id')->nullable()->constrained('composantes')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->date('date_prevue');
            $table->string('exercice_budgetaire')->nullable();
            $table->integer('annee')->nullable();
            $table->decimal('montant_prevu', 15, 2);
            $table->string('devise', 10)->default('MGA');
            $table->decimal('montant_mga', 15, 2);
            $table->enum('statut', ['prevu', 'effectue'])->default('prevu');
            $table->text('description')->nullable();
            $table->string('justificatif_path')->nullable();
            $table->string('justificatif_name')->nullable();
            $table->timestamps();
        });

        // 6. Décaissé (Décaissements réels)
        Schema::create('budget_decaissements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->onDelete('cascade');
            $table->foreignId('composante_id')->nullable()->constrained('composantes')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->date('date');
            $table->decimal('montant', 15, 2);
            $table->string('devise', 10)->default('MGA');
            $table->decimal('montant_mga', 15, 2);
            $table->string('reference')->nullable();
            $table->string('beneficiaire')->nullable();
            $table->text('commentaire')->nullable();
            $table->string('justificatif_path')->nullable();
            $table->string('justificatif_name')->nullable();
            $table->timestamps();
        });

        // 7. Audité / Dépensé
        Schema::create('budget_depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projets', 'id_projet')->onDelete('cascade');
            $table->foreignId('financement_id')->nullable()->constrained('financements')->nullOnDelete();
            $table->foreignId('composante_id')->nullable()->constrained('composantes')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->string('designation');
            $table->text('note')->nullable();
            $table->decimal('montant', 15, 2);
            $table->string('devise', 10)->default('MGA');
            $table->decimal('montant_mga', 15, 2);
            $table->date('date');
            $table->string('beneficiaire');
            $table->string('categorie')->nullable();
            $table->string('reference')->nullable();
            $table->string('justification_path')->nullable();
            $table->string('justification_name')->nullable();
            
            // Audit
            $table->enum('statut', ['depense', 'audite'])->default('depense');
            $table->decimal('montant_audite', 15, 2)->nullable();
            $table->string('organisme_audit')->nullable();
            $table->date('date_audit')->nullable();
            $table->string('rapport_audit_path')->nullable();
            $table->string('rapport_audit_name')->nullable();
            $table->text('observation_audit')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_depenses');
        Schema::dropIfExists('budget_decaissements');
        Schema::dropIfExists('budget_programmations');
        Schema::dropIfExists('budget_approbations');
        Schema::dropIfExists('budget_engagements');
        Schema::dropIfExists('budget_mobilisations');
        Schema::dropIfExists('budget_pledges');
    }
};