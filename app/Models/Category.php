<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Get the posts for a category.
     */
    public function posts(): BelongsToMany  {
        return $this->belongsToMany(Post::class, 'post_categories', 'category_id', 'post_id');
    }

    /**
     * Get the users following a category.
     */
    public function users(): BelongsToMany  {
        return $this->belongsToMany(User::class, 'user_category', 'category_id', 'user_id');
    }
}
