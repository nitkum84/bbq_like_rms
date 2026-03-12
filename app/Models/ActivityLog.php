<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model {
    protected $fillable = [
        'causer_id',
        'event',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'route_name',
        'method',
        'url',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function causer(): BelongsTo {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public function subject(): MorphTo {
        return $this->morphTo();
    }
}
