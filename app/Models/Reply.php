<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reply extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    /**
     * Get the parent comment for this reply relationship.
     */
    public function parent(): BelongsTo {
        return $this->belongsTo(Comment::class, 'parent_comment_id', 'comment_id');
    }

    /**
     * Get the reply comment for this relationship.
     */
    public function reply(): BelongsTo {
        return $this->belongsTo(Comment::class, 'comment_id', 'comment_id');
    }
}
