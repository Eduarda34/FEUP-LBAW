<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the post.
     */
    public function owner(): BelongsTo  {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for a post.
     */
    public function comments(): HasMany  {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the categories for a post.
     */
    public function categories(): BelongsToMany  {
        return $this->belongsToMany(Category::class, 'post_categories', 'post_id', 'category_id');
    }

    /**
     * Get the users that added to their favorites a post.
     */
    public function fans(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_favorites', 'post_id', 'user_id');
    }

}
