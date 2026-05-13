<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public function log(
        string $action,
        string $module,
        string $description,
        ?int   $projectId = null
    ): void {
        ActivityLog::create([
            'id_utilisateur' => Auth::id(),
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'id_projet'   => $projectId,
            'created_at'  => now(),
        ]);
    }
}