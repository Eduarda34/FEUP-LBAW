<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;

    protected $primaryKey = 'post_id';

    /**
     * Get the user that owns the post.
     */
    public function owner(): BelongsTo  {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the comments for a post.
     */
    public function comments(): HasMany  {
        return $this->hasMany(Comment::class, 'post_id', 'post_id')
            ->whereNotIn('user_id', BlockedUser::query()->select('blocked_id'))
            ->whereNotIn('comment_id', Reply::query()->select('comment_id'));
    }

    /**
     * Get the categories for a post.
     */
    public function categories(): BelongsToMany  {
        return $this->belongsToMany(Category::class, 'post_categories', 'post_id', 'category_id');
    }

    /**
     * Get the users that added a post to their favorites.
     */
    public function fans(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_favorites', 'post_id', 'user_id');
    }

    /**
     * Get the votes for a post.
     */
    public function votes(): HasMany  {
        return $this->hasMany(PostVote::class, 'post_id', 'post_id')
            ->whereNotIn('user_id', BlockedUser::query()->select('blocked_id'));
    }

    /**
     * Get the reports related to the post.
     */
    public function reports(): HasMany {
        return $this->hasMany(PostReport::class, 'post_id');
    }

    /**
     * Get the news cover image.
     */
    public function getCoverImage(): string {
        return $this->image 
            ? asset('storage/' . $this->image) 
            : asset('storage/default-news.jpg');
    }
}
