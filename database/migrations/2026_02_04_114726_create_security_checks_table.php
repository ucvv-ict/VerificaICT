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
        Schema::create('security_checks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entity_security_task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('esito', ['ok', 'ko', 'na']);
            $table->text('note')->nullable();

            $table->timestamp('checked_at');
            $table->foreignId('checked_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_checks');
    }
};
