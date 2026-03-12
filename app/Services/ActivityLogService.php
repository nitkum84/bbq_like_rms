<?php
namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ActivityLogService {
    public function record(
        string $event,
        ?Model $subject = null,
        ?string $description = null,
        array $properties = [],
        ?int $causerId = null
    ): ?ActivityLog {
        if (! Schema::hasTable('activity_logs')) {
            return null;
        }

        if ($subject instanceof ActivityLog) {
            return null;
        }

        $request = request();

        return ActivityLog::create([
            'causer_id' => $causerId ?? auth()->id(),
            'event' => $event,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'properties' => array_merge($properties, [
                'subject_label' => $subject ? $this->subjectLabel($subject) : null,
            ]),
            'route_name' => $request?->route()?->getName(),
            'method' => $request?->method(),
            'url' => $request?->fullUrl(),
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 1000, ''),
        ]);
    }

    public function recordModelEvent(Model $model, string $event): ?ActivityLog {
        $ignoredKeys = ['updated_at', 'created_at', 'deleted_at', 'remember_token', 'password', 'otp'];

        if ($event === 'updated') {
            $changes = Arr::except($model->getChanges(), $ignoredKeys);

            if ($changes === []) {
                return null;
            }

            return $this->record(
                'updated',
                $model,
                class_basename($model).' updated',
                [
                    'old' => Arr::only($this->sanitize((array) $model->getOriginal()), array_keys($changes)),
                    'changes' => $this->sanitize($changes),
                ]
            );
        }

        if ($event === 'created') {
            return $this->record(
                'created',
                $model,
                class_basename($model).' created',
                ['attributes' => Arr::except($this->sanitize((array) $model->getAttributes()), $ignoredKeys)]
            );
        }

        if ($event === 'deleted') {
            return $this->record(
                'deleted',
                $model,
                class_basename($model).' deleted',
                ['attributes' => Arr::except($this->sanitize((array) $model->getOriginal()), $ignoredKeys)]
            );
        }

        return null;
    }

    protected function sanitize(array $payload): array {
        return collect($payload)->map(function ($value) {
            if (is_bool($value) || is_null($value) || is_numeric($value)) {
                return $value;
            }

            if (is_array($value)) {
                return $this->sanitize($value);
            }

            return Str::limit((string) $value, 1000, '');
        })->all();
    }

    protected function subjectLabel(Model $subject): string {
        foreach (['title', 'name', 'code', 'confirmation_code', 'table_number', 'email'] as $field) {
            if (isset($subject->{$field}) && filled($subject->{$field})) {
                return class_basename($subject).' - '.$subject->{$field};
            }
        }

        return class_basename($subject).' #'.$subject->getKey();
    }
}
