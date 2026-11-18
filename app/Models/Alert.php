<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['organization_id', 'title', 'message', 'severity', 'status', 'source', 'acknowledged_at'])]
class Alert extends Model
{
    /** @use Factory<self> */
    use BelongsToOrganization, HasFactory;

    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
        ];
    }
}
