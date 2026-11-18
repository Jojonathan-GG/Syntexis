<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['organization_id', 'created_by', 'name', 'type', 'config', 'status', 'last_tested_at'])]
class Connection extends Model
{
    /** @use Factory<self> */
    use BelongsToOrganization, HasFactory;

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'last_tested_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class);
    }

    public function pipelines(): HasMany
    {
        return $this->hasMany(Pipeline::class);
    }
}
