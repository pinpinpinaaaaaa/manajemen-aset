<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\AuditLogService;

abstract class BaseModel extends Model
{
    protected static function booted()
    {
        // CREATE
        static::created(function ($model) {
            AuditLogService::log(
                'create',
                $model->getTable(),
                $model->getKey(),
                null,
                $model->toArray()
            );
        });

        // UPDATE
        static::updated(function ($model) {
            $changes = $model->getChanges();

            // skip kalau cuma updated_at
            if (count($changes) === 1 && isset($changes['updated_at'])) {
                return;
            }

            AuditLogService::log(
                'update',
                $model->getTable(),
                $model->getKey(),
                array_intersect_key($model->getOriginal(), $changes),
                $changes
            );
        });

        // DELETE
        static::deleted(function ($model) {
            AuditLogService::log(
                'delete',
                $model->getTable(),
                $model->getKey(),
                $model->toArray(),
                null
            );
        });
    }
}
