<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('project_ideas', function (Blueprint $table) {
        // Supprime l'ancienne clé étrangère qui pointe vers 'users'
        $table->dropForeign(['created_by']);

        // Crée la nouvelle clé étrangère qui pointe vers 'utilisateurs'
        $table->foreign('created_by')->references('id_utilisateur')->on('utilisateurs');
    });
}

public function down(): void
{
    Schema::table('project_ideas', function (Blueprint $table) {
        $table->dropForeign(['created_by']);
        $table->foreign('created_by')->references('id_utilisateur')->on('utilisateurs');
    });
}
};
