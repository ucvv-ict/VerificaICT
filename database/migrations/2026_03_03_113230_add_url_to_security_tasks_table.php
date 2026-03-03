<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('security_tasks', function (Blueprint $table) {
            $table->string('documentation_url')->nullable()->after('priorita');
        });
    }

    public function down(): void
    {
        Schema::table('security_tasks', function (Blueprint $table) {
            $table->dropColumn('documentation_url');
        });
    }
};