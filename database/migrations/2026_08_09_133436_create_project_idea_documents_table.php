<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_idea_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_idea_id')->constrained('project_ideas')->cascadeOnDelete();
            $table->enum('type', ['concept_note', 'etude_faisabilite', 'budget', 'carte', 'images', 'autre']);
            $table->string('libelle')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_idea_documents');
    }
};