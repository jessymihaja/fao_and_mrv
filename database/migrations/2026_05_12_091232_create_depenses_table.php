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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->references('id_projet')->on('projets')->onDelete('cascade');
            $table->foreignId('financement_id')->nullable()->constrained()->nullOnDelete();
            $table->text('note');
            $table->decimal('montant', 20, 2);
            $table->date('date');
            $table->string('beneficiaire');
            $table->string('categorie')->nullable();
            $table->string('reference')->nullable();
            $table->string('justification_path');
            $table->string('justification_name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
