<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogService
{
    /**
     * Record an administrative audit log.
     */
    public function log(
        ?Admin $admin,
        string $action,
        string $resourceType,
        ?string $resourceId = null,
        string $description = '',
        ?Request $request = null
    ): AuditLog {
        $req = $request ?? request();

        return AuditLog::create([
            'admin_id' => $admin?->_id,
            'admin_email' => $admin?->email,
            'action' => strtoupper($action),
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'ip_address' => $req?->ip(),
            'user_agent' => $req?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
