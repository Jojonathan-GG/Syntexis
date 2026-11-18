<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['organization_id', 'connection_id', 'name', 'description', 'schema_json', 'record_count', 'freshness_at'])]
class Dataset extends Model
{
    /** @use Factory<self> */
    use BelongsToOrganization, HasFactory;

    protected function casts(): array
    {
        return [
            'schema_json' => 'array',
            'freshness_at' => 'datetime',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(DataRecord::class);
    }

    public function pipelines(): HasMany
    {
        return $this->hasMany(Pipeline::class);
    }
}
