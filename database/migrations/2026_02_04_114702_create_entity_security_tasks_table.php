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
        Schema::create('entity_security_tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entity_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('security_task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('responsabile_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('attiva')->default(true);

            $table->timestamps();

            $table->unique(['entity_id', 'security_task_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_security_tasks');
    }
};
