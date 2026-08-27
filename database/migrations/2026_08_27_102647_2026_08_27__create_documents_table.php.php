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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'composante_id')) {
                $table->foreignId('composante_id')
                      ->nullable()
                      ->after('project_id')
                      ->constrained('composantes')
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'composante_id')) {
                $table->dropForeign(['composante_id']);
                $table->dropColumn('composante_id');
            }
        });
    }
};