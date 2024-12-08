<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $primaryKey = 'comment_id';

    /**
     * Get the user that owns the comment.
     */
    public function owner(): BelongsTo  {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the post that the comment belongs.
     */
    public function post(): BelongsTo  {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
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

    /**
     * Get the votes for a post.
     */
    public function votes(): HasMany  {
        return $this->hasMany(CommentVote::class, 'comment_id', 'comment_id')
            ->whereNotIn('user_id', BlockedUser::query()->select('blocked_id'));
    }

    /**
     * Get the reports related to the comment.
     */
    public function reports(): HasMany {
        return $this->hasMany(CommentReport::class, 'comment_id');
    }
}
