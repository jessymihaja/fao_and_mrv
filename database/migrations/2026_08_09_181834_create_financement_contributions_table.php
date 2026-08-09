<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financement_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->onDelete('cascade');
            $table->unsignedBigInteger('organisme_contributeur_id')->nullable();
            $table->string('mode_contribution');
            $table->decimal('montant', 20, 2);
            $table->string('devise', 10);
            $table->decimal('montant_mga', 20, 2);
            $table->date('date_contribution');
            $table->unsignedBigInteger('categorie_contribution_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financement_contributions');
    }
};