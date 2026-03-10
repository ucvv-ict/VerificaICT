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
        Schema::create('security_task_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_task_id')->constrained()->cascadeOnDelete();
            $table->string('nome');
            $table->string('file_path');
            $table->timestamps();
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_task_documents');
    }
};
