<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoiRequest extends Model
{
    protected $fillable = [
        'wdtk_url_title',
        'title',
        'foi_authority_id',
        'authority_name',
        'status',
        'display_status',
        'description',
        'wdtk_url',
        'requester_name',
        'request_created_at',
        'request_updated_at',
        'is_tracked',
        'internal_notes',
        'tags',
        'raw_json',
        'last_polled_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'raw_json' => 'array',
            'is_tracked' => 'boolean',
            'request_created_at' => 'datetime',
            'request_updated_at' => 'datetime',
            'last_polled_at' => 'datetime',
        ];
    }

    public function authority(): BelongsTo
    {
        return $this->belongsTo(FoiAuthority::class, 'foi_authority_id');
    }

    public function statusUpdates(): HasMany
    {
        return $this->hasMany(FoiStatusUpdate::class)->orderByDesc('detected_at');
    }

    public function scopeTracked($query)
    {
        return $query->where('is_tracked', true);
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', [
            'waiting_response_overdue',
            'waiting_response_very_overdue',
        ]);
    }

    public function scopeAwaitingAction($query)
    {
        return $query->whereIn('status', [
            'waiting_clarification',
            'waiting_classification',
        ]);
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, [
            'waiting_response_overdue',
            'waiting_response_very_overdue',
        ]);
    }

    public function isResolved(): bool
    {
        return in_array($this->status, [
            'successful',
            'partially_successful',
            'not_held',
            'rejected',
            'user_withdrawn',
        ]);
    }

    public function getWdtkFullUrlAttribute(): string
    {
        return "https://www.whatdotheyknow.com/request/{$this->wdtk_url_title}";
    }
}
