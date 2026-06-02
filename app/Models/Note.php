<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = [
        'title',
        'content',
        'color',
        'is_pinned',
        'is_archived',
        'is_trashed',
        'category_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_archived' => 'boolean',
        'is_trashed' => 'boolean',
    ];

    protected $appends = [
        'word_count',
        'char_count',
        'reading_time',
    ];

    /**
     * Get the category that this note belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get word count of note content.
     */
    public function getWordCountAttribute(): int
    {
        if (empty($this->content)) {
            return 0;
        }
        return str_word_count(strip_tags($this->content));
    }

    /**
     * Get character count of note content.
     */
    public function getCharCountAttribute(): int
    {
        if (empty($this->content)) {
            return 0;
        }
        return mb_strlen(strip_tags($this->content));
    }

    /**
     * Get estimated reading time in minutes (assuming 200 WPM).
     */
    public function getReadingTimeAttribute(): int
    {
        $words = $this->word_count;
        if ($words <= 0) {
            return 0;
        }
        $time = ceil($words / 200);
        return (int) $time;
    }
}
