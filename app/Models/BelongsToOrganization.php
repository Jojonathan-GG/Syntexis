<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Shared tenancy behaviour for every organization-owned model.
 */
trait BelongsToOrganization
{
    /**
     * Register the tenant scope and automatically stamp new records.
     */
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder): void {
            $organizationId = Organization::currentId();

            if ($organizationId !== null) {
                $builder->where(
                    $builder->getModel()->qualifyColumn('organization_id'),
                    $organizationId
                );
            }
        });

        static::creating(function (Model $model): void {
            if (empty($model->organization_id)) {
                $model->organization_id = Organization::currentId();
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
