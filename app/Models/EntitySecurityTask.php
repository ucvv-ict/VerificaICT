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
 * @property string|null $current_status
 * @property int|null $days_from_last_check
 * @property \Illuminate\Support\Carbon|null $last_check_at
 * @property \Illuminate\Support\Carbon|null $next_due_at
 * @property string|null $last_check_result
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
            'current_status' => 'string',
            'days_from_last_check' => 'integer',
            'last_check_at' => 'datetime',
            'next_due_at' => 'datetime',
            'last_check_result' => 'string',
        ];
    }

    public function getPriorityLevelAttribute(): int
    {
        return match ($this->current_status) {
            'rosso' => 3,
            'arancione' => 2,
            'verde' => 1,
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

    public function getDaysToDeadlineAttribute(): ?int
    {
        if (! $this->next_due_at) {
            return null;
        }

        return (int) now()
            ->startOfDay()
            ->diffInDays($this->next_due_at->startOfDay(), false);
    }
}
