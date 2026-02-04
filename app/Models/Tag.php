<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $nome
 * @property string|null $tipo
 * @property-read EloquentCollection<int, SecurityTask> $securityTasks
 */
class Tag extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'tipo',
    ];

    public function securityTasks(): BelongsToMany
    {
        return $this->belongsToMany(SecurityTask::class, 'security_task_tag');
    }
}
