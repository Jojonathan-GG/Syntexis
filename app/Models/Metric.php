<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['organization_id', 'key', 'name', 'category', 'value', 'unit', 'change_percent', 'series'])]
class Metric extends Model
{
    /** @use Factory<self> */
    use BelongsToOrganization, HasFactory;

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'change_percent' => 'decimal:2',
            'series' => 'array',
        ];
    }
}
