<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $entity_id
 * @property int $security_task_id
 * @property int|null $responsabile_user_id
 * @property bool $attiva
 * @property-read Entity $entity
 * @property-read SecurityTask $securityTask
 * @property-read User|null $responsabile
 * @property-read EloquentCollection<int, SecurityCheck> $checks
 * @property-read SecurityCheck|null $latestCheck
 */
class EntitySecurityTask extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'entity_id',
        'security_task_id',
        'responsabile_user_id',
        'attiva',
        'descrizione_specifica'
    ];

    protected $appends = [
        'current_status',
        'days_from_last_check',
        'priority_level',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entity_id' => 'integer',
            'security_task_id' => 'integer',
            'responsabile_user_id' => 'integer',
            'attiva' => 'boolean',
        ];
    }

    public function getPriorityLevelAttribute(): int
    {
        return match ($this->current_status) {
            'rosso' => 3,
            'arancione' => 2,
            'verde' => 1,
            'nero' => 4,
            default => 0,
        };
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function securityTask(): BelongsTo
    {
        return $this->belongsTo(SecurityTask::class);
    }

    public function responsabile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsabile_user_id');
    }

    public function checks(): HasMany
    {
        return $this->hasMany(SecurityCheck::class);
    }

    public function latestCheck(): HasOne
    {
        return $this->hasOne(SecurityCheck::class)->latestOfMany('checked_at');
    }

    public function getCurrentStatusAttribute(): string
    {
        if (! $this->attiva) {
            return 'nero';
        }

        if (! $this->latestCheck) {
            return 'nero';
        }

        $esito = strtolower(trim((string) $this->latestCheck->esito));
        if ($esito !== 'ok') {
            return 'nero';
        }

        $task = $this->securityTask;

        $period = (int) $task->periodicita_giorni;

        $warningAlert = (int) ($task->warning_alert
            ?? config('security.default_warning_alert'));

        $criticalAfter = (int) ($task->critical_after
            ?? config('security.default_critical_after'));

        $daysPassed = $this->latestCheck->checked_at
            ->startOfDay()
            ->diffInDays(now()->startOfDay());

        // 🟢 prima della soglia warning
        if ($daysPassed < ($period - $warningAlert)) {
            return 'verde';
        }

        // 🟠 tra warning e scadenza
        if ($daysPassed < $period) {
            return 'arancione';
        }

        $daysOverdue = $daysPassed - $period;

        // 🔴 scaduto ma non critico
        if ($daysOverdue < $criticalAfter) {
            return 'rosso';
        }

        // ⚫ oltre soglia critica
        return 'nero';
    }


public function getDaysToDeadlineAttribute(): ?int
{
    $lastCheck = $this->latestCheck?->checked_at;

    if (! $lastCheck) {
        return null;
    }

    $period = (int) ($this->securityTask->periodicita_giorni ?? 0);

    if ($period <= 0) {
        return null;
    }

    $deadline = $lastCheck->copy()
        ->startOfDay()
        ->addDays($period);

    $today = now()->startOfDay();

    return (int) $today->diffInDays($deadline, false);
}

    public function getDaysFromLastCheckAttribute(): ?int
    {
        if (!$this->latestCheck) {
            return null;
        }

        return (int) $this->latestCheck->checked_at
            ->startOfDay()
            ->diffInDays(now()->startOfDay());
    }

}
