<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'county',
        'city',
        'postcode',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    // Scopes
    public function scopeByCounty($query, $county)
    {
        return $query->where('county', $county);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    // Helper method to get full location string
    public function getFullLocationAttribute(): string
    {
        return trim("{$this->city}, {$this->county}, {$this->country}");
    }
}
