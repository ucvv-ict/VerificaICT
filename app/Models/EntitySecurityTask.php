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
    ];

    protected $appends = [
        'current_status',
        'days_from_last_check',
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
        // Se non è attiva, consideriamola comunque rossa
        if (!$this->attiva) {
            return 'rosso';
        }

        $task = $this->securityTask;

        // Nessun controllo eseguito
        if (!$this->latestCheck) {
            return 'rosso';
        }

        // Se l'ultimo esito non è "ok", lo stato è sempre rosso.
        if ($this->latestCheck->esito !== 'ok') {
            return 'rosso';
        }

        $days = (int) $this->latestCheck->checked_at
            ->startOfDay()
            ->diffInDays(now()->startOfDay());

        if ($days <= $task->periodicita_giorni) {
            return 'verde';
        }

        if ($days <= $task->warning_after) {
            return 'arancione';
        }

        return 'rosso';
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
