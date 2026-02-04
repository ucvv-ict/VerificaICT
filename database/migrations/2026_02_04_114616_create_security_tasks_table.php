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
        Schema::create('security_tasks', function (Blueprint $table) {
            $table->id();

            $table->string('titolo');
            $table->text('descrizione')->nullable();

            // periodicità
            $table->unsignedInteger('periodicita_giorni');     // es. 90
            $table->unsignedInteger('warning_after');          // es. 110
            $table->unsignedInteger('critical_after');         // es. 135

            $table->boolean('attiva')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_tasks');
    }
};
