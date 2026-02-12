<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $entity_security_task_id
 * @property string $esito
 * @property string|null $note
 * @property \Illuminate\Support\Carbon $checked_at
 * @property int $checked_by
 * @property-read EntitySecurityTask $entitySecurityTask
 * @property-read User $checkedBy
 */
class SecurityCheck extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'entity_security_task_id',
        'esito',
        'note',
        'checked_at',
        'checked_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entity_security_task_id' => 'integer',
            'checked_by' => 'integer',
            'checked_at' => 'datetime',
            'esito' => 'string',
        ];
    }

    public function entitySecurityTask(): BelongsTo
    {
        return $this->belongsTo(EntitySecurityTask::class);
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'checked_by');
    }

}
