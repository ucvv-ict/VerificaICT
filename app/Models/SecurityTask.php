<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $titolo
 * @property string|null $descrizione
 * @property int $periodicita_giorni
 * @property int $warning_after
 * @property int $critical_after
 * @property bool $attiva
 * @property-read EloquentCollection<int, Tag> $tags
 * @property-read EloquentCollection<int, EntitySecurityTask> $entitySecurityTasks
 */
class SecurityTask extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'titolo',
        'descrizione',
        'periodicita_giorni',
        'warning_after',
        'critical_after',
        'attiva',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'periodicita_giorni' => 'integer',
            'warning_after' => 'integer',
            'critical_after' => 'integer',
            'attiva' => 'boolean',
        ];
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function entitySecurityTasks(): HasMany
    {
        return $this->hasMany(EntitySecurityTask::class);
    }
}
