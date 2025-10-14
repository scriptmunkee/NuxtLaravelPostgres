<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetListing extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'breed',
        'age_months',
        'price',
        'gender',
        'images',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}
