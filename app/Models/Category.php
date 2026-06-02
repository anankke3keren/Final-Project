<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'color',
        'icon',
    ];

    /**
     * Get notes belonging to this category.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
