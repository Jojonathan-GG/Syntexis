<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    /** @use Factory<self> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'plan',
        'brand_color',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public static function currentId(): ?int
    {
        $organizationId = session('current_organization_id') ?? auth()->user()?->organization_id;

        return $organizationId ? (int) $organizationId : null;
    }

    public static function current(): ?self
    {
        $organizationId = self::currentId();

        return $organizationId ? self::find($organizationId) : null;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }

    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class);
    }

    public function pipelines(): HasMany
    {
        return $this->hasMany(Pipeline::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(Metric::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
