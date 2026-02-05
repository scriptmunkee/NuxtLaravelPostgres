<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoiAuthority extends Model
{
    protected $fillable = [
        'wdtk_slug',
        'name',
        'short_name',
        'url',
        'notes',
        'raw_json',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_json' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    public function requests(): HasMany
    {
        return $this->hasMany(FoiRequest::class);
    }

    public function getWdtkUrlAttribute(): string
    {
        return "https://www.whatdotheyknow.com/body/{$this->wdtk_slug}";
    }
}
