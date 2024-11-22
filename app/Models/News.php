<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    protected $table = 'news'; // Table name

    /**
     * Get the user who submitted the news.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}