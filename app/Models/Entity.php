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
 * @property string $nome
 * @property string|null $codice
 * @property bool $attivo
 * @property-read EloquentCollection<int, EntitySecurityTask> $entitySecurityTasks
 * @property-read EloquentCollection<int, User> $users
 */
class Entity extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'codice',
        'attivo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attivo' => 'boolean',
        ];
    }

    public function entitySecurityTasks(): HasMany
    {
        return $this->hasMany(EntitySecurityTask::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'entity_user')
            ->withPivot('ruolo')
            ->withTimestamps();
    }
}
