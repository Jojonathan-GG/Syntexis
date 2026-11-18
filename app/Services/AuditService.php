<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function record(
        string $action,
        ?Model $subject = null,
        ?string $description = null
    ): void {
        $organizationId = Organization::currentId();

        if ($organizationId === null) {
            return;
        }

        AuditLog::create([
            'organization_id' => $organizationId,
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
