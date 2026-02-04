<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'force_password_change')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('force_password_change')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'force_password_change')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('force_password_change');
            });
        }
    }
};
