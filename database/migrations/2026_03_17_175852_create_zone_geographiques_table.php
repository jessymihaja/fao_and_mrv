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
        Schema::create('zone_geographiques', function (Blueprint $table) {
            $table->id('id_zone_geographique');
            $table->string('nom');
            $table->string('code');
            $table->decimal('latitude',10,8);
            $table->decimal('longitude',10,8);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_geographiques');
    }
};
