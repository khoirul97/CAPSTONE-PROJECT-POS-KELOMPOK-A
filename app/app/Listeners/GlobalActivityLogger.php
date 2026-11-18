<?php

namespace App\Listeners;

use App\Laravue\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class GlobalActivityLogger
{
    /**
     * Model CREATED.
     */
    public function created($event, $models): void
    {
        $model = $this->getModel($models);

        if (!$model) {
            return;
        }

        ActivityLogger::created(
            $model,
            null,
            [
                'attributes' => ActivityLogger::sanitizeAttributes(
                    $model->getAttributes()
                ),
            ]
        );
    }

    /**
     * Model UPDATED.
     */
    public function updated($event, $models): void
    {
        $model = $this->getModel($models);

        if (!$model) {
            return;
        }

        $changes = $model->getChanges();

        if (empty($changes)) {
            return;
        }

        ActivityLogger::updated(
            $model,
            null,
            [
                'changes' => ActivityLogger::sanitizeAttributes(
                    $changes
                ),
            ]
        );
    }

    /**
     * Model DELETED.
     */
    public function deleted($event, $models): void
    {
        $model = $this->getModel($models);

        if (!$model) {
            return;
        }

        ActivityLogger::deleted(
            $model,
            null,
            [
                'attributes' => ActivityLogger::sanitizeAttributes(
                    $model->getAttributes()
                ),
            ]
        );
    }

    /**
     * Model RESTORED.
     */
    public function restored($event, $models): void
    {
        $model = $this->getModel($models);

        if (!$model) {
            return;
        }

        ActivityLogger::restored($model);
    }

    /**
     * Ambil model dari payload event Laravel.
     */
    protected function getModel($models): ?Model
    {
        if (!is_array($models)) {
            return null;
        }

        $model = $models[0] ?? null;

        /*
         * Jangan proses ActivityLog.
         */
        if ($model instanceof ActivityLog) {
            return null;
        }

        if (!$model instanceof Model) {
            return null;
        }

        return $model;
    }
}