<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityTaskDocument extends Model
{
    protected $fillable = [
        'security_task_id',
        'nome',
        'file_path',
    ];

    public function securityTask()
    {
        return $this->belongsTo(SecurityTask::class);
    }
}