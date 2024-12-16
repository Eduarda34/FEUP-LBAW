<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'profile_picture'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the posts for a user.
     */
    public function posts(): HasMany {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the comments for a user.
     */
    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the followed users for a user.
     */
    public function following(): BelongsToMany {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
    }

    /**
     * Get the follower users for a user.
     */
    public function followers(): BelongsToMany {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id');
    }

    /**
     * Get the favorite posts for a user.
     */
    public function favorites(): BelongsToMany {
        return $this->belongsToMany(Post::class, 'user_favorites', 'user_id', 'post_id');
    }

    /**
     * Get the followed categories for a user.
     */
    public function followed_categories(): BelongsToMany {
        return $this->belongsToMany(Category::class, 'user_category', 'user_id', 'category_id');
    }

    /**
     * Get the system_managers for a user.
     */
    public function system_managers(): HasOne {
        return $this->hasOne(SystemManager::class, 'sm_id');
    }

    /**
     * Get the user reports submitted by the user.
     */
    public function reports(): HasMany {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Get the user reports where the user is reported.
     */
    public function reported(): HasMany {
        return $this->hasMany(UserReport::class, 'reported_id');
    }

    /**
     * Get the blocked user for a user.
     */
    public function blocked(): HasOne {
        return $this->hasOne(BlockedUser::class, 'blocked_id');
    }

    /**
     * Get the user notifications.
     */
    public function notifications(): HasMany {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Get the user profile picture.
     */
    public function getProfilePicture(): string {
        return $this->profile_picture 
            ? asset('storage/' . $this->profile_picture) 
            : asset('storage/default.png');
    }
}
