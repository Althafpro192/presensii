<?php

namespace App\Models\Traits;

use App\Models\AuditLog;

/**
 * Trait Auditable
 *
 * Automatically logs create, update, and delete events to audit_logs table.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::logAudit($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }
            $oldValues = collect($dirty)->mapWithKeys(function ($value, $key) use ($model) {
                return [$key => $model->getOriginal($key)];
            })->toArray();
            static::logAudit($model, 'updated', $oldValues, $dirty);
        });

        static::deleted(function ($model) {
            static::logAudit($model, 'deleted', $model->getAttributes(), null);
        });
    }

    /**
     * Write an audit log entry.
     */
    protected static function logAudit($model, string $action, ?array $oldValues, ?array $newValues): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()?->ip() ?? '127.0.0.1',
                'user_agent' => request()?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Don't let audit logging break the main operation
            logger()->error('Audit log failed: ' . $e->getMessage());
        }
    }
}
