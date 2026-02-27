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
        Schema::table('security_tasks', function (Blueprint $table) {
            $table->unsignedTinyInteger('priorita')
                ->default(2)
                ->after('periodicita_giorni');
        });
    }

    public function down(): void
    {
        Schema::table('security_tasks', function (Blueprint $table) {
            $table->dropColumn('priorita');
        });
    }
};
