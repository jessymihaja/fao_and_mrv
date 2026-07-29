<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Suppression des anciennes relations 1-N
        |--------------------------------------------------------------------------
        */

        Schema::table('projets', function (Blueprint $table) {
            $table->dropForeign(['classification_id']);
            $table->dropForeign(['entite_accreditee_id']);
            $table->dropForeign(['domaine_intervention_id']);

            $table->dropColumn([
                'classification_id',
                'entite_accreditee_id',
                'domaine_intervention_id',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Projet <-> Classification
        |--------------------------------------------------------------------------
        */

        Schema::create('classification_projet', function (Blueprint $table) {

            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('classification_id');

            $table->foreign('projet_id')
                ->references('id_projet')
                ->on('projets')
                ->onDelete('cascade');

            $table->foreign('classification_id')
                ->references('id_classification')
                ->on('classifications')
                ->onDelete('cascade');

            $table->primary(['projet_id', 'classification_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Projet <-> Entité accréditée
        |--------------------------------------------------------------------------
        */

        Schema::create('entite_accreditee_projet', function (Blueprint $table) {

            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('entite_accreditee_id');

            $table->foreign('projet_id')
                ->references('id_projet')
                ->on('projets')
                ->onDelete('cascade');

            $table->foreign('entite_accreditee_id')
                ->references('id_entite_accreditee')
                ->on('entite_accreditees')
                ->onDelete('cascade');

            $table->primary(['projet_id', 'entite_accreditee_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Projet <-> Domaine d'intervention
        |--------------------------------------------------------------------------
        */

        Schema::create('domaine_intervention_projet', function (Blueprint $table) {

            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('domaine_intervention_id');

            $table->foreign('projet_id')
                ->references('id_projet')
                ->on('projets')
                ->onDelete('cascade');

            $table->foreign('domaine_intervention_id')
                ->references('id_domaine_intervention')
                ->on('domaine_interventions')
                ->onDelete('cascade');

            $table->primary(['projet_id', 'domaine_intervention_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classification_projet');
        Schema::dropIfExists('entite_accreditee_projet');
        Schema::dropIfExists('domaine_intervention_projet');

        Schema::table('projets', function (Blueprint $table) {

            $table->unsignedBigInteger('classification_id')->nullable();
            $table->unsignedBigInteger('entite_accreditee_id')->nullable();
            $table->unsignedBigInteger('domaine_intervention_id')->nullable();

            $table->foreign('classification_id')
                ->references('id_classification')
                ->on('classifications')
                ->nullOnDelete();

            $table->foreign('entite_accreditee_id')
                ->references('id_entite_accreditee')
                ->on('entite_accreditees')
                ->nullOnDelete();

            $table->foreign('domaine_intervention_id')
                ->references('id_domaine_intervention')
                ->on('domaine_interventions')
                ->nullOnDelete();
        });
    }
};