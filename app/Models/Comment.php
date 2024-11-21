<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the comment.
     */
    public function owner(): BelongsTo  {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that the comment belongs.
     */
    public function post(): BelongsTo  {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the comment that the reply belongs.
     */
    public function parent(): BelongsTo  {
        return $this->belongsTo(Comment::class, 'replies', 'comment_id', 'parent_comment_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies(): HasMany  {
        return $this->hasMany(Comment::class, 'replies', 'parent_comment_id', 'comment_id');
    }
}
