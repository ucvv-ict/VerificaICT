<?php

use App\Models\EntitySecurityTask;
use App\Services\EntitySecurityTaskStatusService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entity_security_tasks', function (Blueprint $table) {
            $table->enum('current_status', ['verde', 'arancione', 'rosso', 'nero'])
                ->default('nero')
                ->after('attiva')
                ->index();

            $table->integer('days_from_last_check')
                ->nullable()
                ->after('current_status')
                ->index();

            $table->timestamp('last_check_at')
                ->nullable()
                ->after('days_from_last_check')
                ->index();

            $table->timestamp('next_due_at')
                ->nullable()
                ->after('last_check_at')
                ->index();

            $table->enum('last_check_result', ['ok', 'ko', 'na'])
                ->nullable()
                ->after('next_due_at')
                ->index();

            $table->index(['entity_id', 'current_status']);
        });

        if (class_exists(EntitySecurityTaskStatusService::class)) {
            resolve(EntitySecurityTaskStatusService::class)->recalculateAll();
        }
    }

    public function down(): void
    {
        Schema::table('entity_security_tasks', function (Blueprint $table) {
            $table->dropIndex(['entity_security_tasks_current_status_index']);
            $table->dropIndex(['entity_security_tasks_days_from_last_check_index']);
            $table->dropIndex(['entity_security_tasks_last_check_at_index']);
            $table->dropIndex(['entity_security_tasks_next_due_at_index']);
            $table->dropIndex(['entity_security_tasks_last_check_result_index']);
            $table->dropIndex(['entity_security_tasks_entity_id_current_status_index']);

            $table->dropColumn([
                'current_status',
                'days_from_last_check',
                'last_check_at',
                'next_due_at',
                'last_check_result',
            ]);
        });
    }
};
