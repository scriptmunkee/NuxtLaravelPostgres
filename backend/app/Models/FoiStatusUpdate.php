<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoiStatusUpdate extends Model
{
    protected $fillable = [
        'foi_request_id',
        'old_status',
        'new_status',
        'display_status',
        'description',
        'is_acknowledged',
        'detected_at',
    ];

    protected function casts(): array
    {
        return [
            'is_acknowledged' => 'boolean',
            'detected_at' => 'datetime',
        ];
    }

    public function foiRequest(): BelongsTo
    {
        return $this->belongsTo(FoiRequest::class);
    }

    public function scopeUnacknowledged($query)
    {
        return $query->where('is_acknowledged', false);
    }
}
