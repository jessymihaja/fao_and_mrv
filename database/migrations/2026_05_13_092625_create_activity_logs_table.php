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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_utilisateur')->references('id_utilisateur')->on('utilisateurs')->onDelete('cascade');
            $table->string('action');        // create, update, delete, upload, login…
            $table->string('module');        // project, financement, document, user…
            $table->text('description')->nullable();
            $table->unsignedBigInteger('id_projet')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable(); // id de l'entité concernée
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
