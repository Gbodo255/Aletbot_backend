<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait CanLogActivity
{
    /**
     * Log a user activity
     *
     * @param string $action
     * @param string $description
     * @param string|null $model
     * @param int|null $modelId
     * @param array|null $oldValues
     * @param array|null $newValues
     * @return void
     */
    protected function logActivity(
        string $action,
        string $description,
        ?string $model = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
