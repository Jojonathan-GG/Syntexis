<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['organization_id', 'connection_id', 'dataset_id', 'name', 'status', 'schedule', 'config', 'last_run_at'])]
class Pipeline extends Model
{
    /** @use Factory<self> */
    use BelongsToOrganization, HasFactory;

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'last_run_at' => 'datetime',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(PipelineRun::class);
    }

    public function latestRun(): HasOne
    {
        return $this->hasOne(PipelineRun::class)->latestOfMany();
    }
}
