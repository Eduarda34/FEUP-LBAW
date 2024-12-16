<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function parent(): HasOne  {
        return $this->hasOne(Reply::class, 'comment_id', 'comment_id')->with('parent');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies(): HasMany  {
        return $this->hasMany(Reply::class, 'parent_comment_id', 'comment_id')->with('reply');
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
